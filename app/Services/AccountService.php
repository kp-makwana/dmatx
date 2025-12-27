<?php

namespace App\Services;

use App\Http\Resources\V1\Account\ListResource;
use App\Models\V1\Account;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountService
{

  public function index($request)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    $query = Account::query();
    // GLOBAL SEARCH
    if ($search = $request->input('search')) {
      $query->where(function ($q) use ($search) {
        $q->where('account_name', 'like', "%{$search}%")
          ->orWhere('client_id', 'like', "%{$search}%")
          ->orWhere('status', 'like', "%{$search}%");
      });
    }
    // USER FILTER
    if (!auth()->user()->hasRole('admin')) {
      $query->where('user_id', auth()->id());
    }

    // STATUS FILTER
    if ($status = $request->input('status')) {
      $query->where('status', $status);
    }

    // SORTING
    $sortable = ['nickname', 'client_id', 'last_login_at'];

    $sortBy  = $request->input('sort_by');
    $sortDir = $request->input('sort_dir');

    // validate column
    if (!in_array($sortBy, $sortable)) {
      $sortBy = null; // ignore sorting
    }

    // validate direction
    if (!in_array($sortDir, ['asc', 'desc'])) {
      $sortDir = 'asc'; // default
    }

    // apply sorting only if valid
    if ($sortBy) {
      $query->orderBy($sortBy, $sortDir);
    }

    // PAGINATION
    $perPage = $request->input('per_page', 10);
    $accounts = $query->paginate($perPage)->appends($request->query());

    // MAP RESULTS
    $mapped = ListResource::collection($accounts->items())->resolve();

    $accounts = $accounts->setCollection(collect($mapped));
    return ['pageConfigs'=>$pageConfigs, 'accounts' => $accounts, 'sortBy' => $sortBy, 'sortDir' => $sortDir];
  }
  /**
   * Store a new Smart-API account
   */
  public function create(array $data): Account
  {
    return DB::transaction(function () use ($data) {
      $account = new Account();
      $account->user_id       = Auth::id();
      $account->nickname      = $data['nickname'];
      $account->client_id     = $data['client_id'];
      $account->pin           = $data['pin'];
      $account->api_key       = $data['api_key'];
      $account->client_secret = $data['client_secret'];
      $account->totp_secret   = $data['totp_secret'];
      $account->status        = 'active';
      $account->is_active     = 1;

      $account->save();

      $response = resolve(AngelService::class)->login($account);

      // ❌ Login failed → rollback
      if (empty($response['success'])) {
        throw new Exception($response['message'] ?? 'Angel login failed');
      }

      return $account;
    });
  }

  public function destroy(Account $account): void
  {
    activity()
      ->performedOn($account)
      ->causedBy(Auth::user())
      ->withProperties([
        'account_id'   => $account->id,
        'client_id'    => $account->client_id,
        'nickname'     => $account->nickname,
        'deleted_by'   => Auth::id(),
      ])
      ->log('Smart-API Account Deleted');

    $account->delete();
  }

  public function refresh($account)
  {
    $response = resolve(AngelService::class)->getRMS($account);
    if ($response['success']){
      $data = $response['data'];
      $account->net = $data['net'];
      $account->amount_used = $data['utiliseddebits'];
      $account->save();
    }
    return $response;
  }

  public function getHoldings($account)
  {
    return resolve(AngelService::class)->getAllHolding($account);
  }

  public function getOrderBook($account)
  {
    $response = resolve(AngelService::class)->getOrderBook($account);

    if ($response['success']) {

      $orders = $response['data'] ?? [];

      $groupedOrders = [
        'Pending'  => [],
        'Executed' => [],
        'Rejected' => [],
        'Cancel'   => [],
      ];
      $tokens = [];
      foreach ($orders as $order) {
        $tokens[] = $order['symboltoken'];

        // ✅ Remove -EQ from symbol (display purpose)
        if (!empty($order['tradingsymbol'])) {
          $order['tradingsymbol'] = str_replace('-EQ', '', $order['tradingsymbol']);
        }

        // Normalize status
        $status = strtolower($order['status'] ?? '');

        switch ($status) {
          case 'complete':
            $groupedOrders['Executed'][] = $order;
            break;

          case 'open':
          case 'trigger pending':
            $groupedOrders['Pending'][] = $order;
            break;

          case 'rejected':
            $groupedOrders['Rejected'][] = $order;
            break;

          case 'cancelled':
            $groupedOrders['Cancel'][] = $order;
            break;

          default:
            $groupedOrders['Pending'][] = $order;
        }
      }
      foreach ($groupedOrders as $key => $group) {
        usort($group, function ($a, $b) {
          $timeA = $a['updatetime'] ?? $a['exchtime'] ?? null;
          $timeB = $b['updatetime'] ?? $b['exchtime'] ?? null;
          if (!$timeA || !$timeB) {
            return 0;
          }
          return strtotime($timeB) <=> strtotime($timeA); // DESC
        });
        $groupedOrders[$key] = $group;
      }
      $tokens = array_values(array_unique($tokens));
      $response['data']['data'] = $groupedOrders;
      $response['data']['tokens'] = array_values(array_unique($tokens));
    }
    return $response;
  }

  public function cancelOrder($account,$order)
  {
    return resolve(AngelService::class)->cancelOrder($account,$order);
  }

  public function modifyOrder($account,$payload)
  {
    return resolve(AngelService::class)->modifyOrder($account,$payload);
  }

  public function placeOrder($account,$payload)
  {
    return resolve(AngelService::class)->placeOrder($account,$payload);
  }

  public function stepOne($validatedData,$user)
  {
      DB::beginTransaction();
      $account = new Account();
      $validatedData['user_id'] = $user->id;
      $validatedData['nickname'] = $validatedData['account_name'];
      $validatedData['type'] = Account::TYPE_AUTO;
      $account->fill($validatedData);
      $account->save();

      $response = resolve(AngelSmartApiService::class)->signUp($account->toArray(),$user);;
      if (!$response['status']) {
        DB::rollBack();
        return $response;
      }
      $account->status = Account::STATUS_SIGNUP_FORM_SUBMITTED;
      $account->save();
      $response['data'] = $account;
      DB::commit();
      return $response;
  }

  public function createStepTwo($account)
  {
    if ($account->status == Account::STATUS_SIGNUP_FORM_SUBMITTED) {
      return ['success' => true,'account' => $account];
    }
    return ['success' => false,'error' => 'Account not yet processed'];
  }

  public function emailOtpResend($account)
  {
    return resolve(AngelSmartApiService::class)->emailOtpResend($account->email);
  }

  public function mobileOtpResend($account)
  {
    return resolve(AngelSmartApiService::class)->mobileOtpResend($account->mobile,$account->email);
  }

  public function submitStepTwo($account,$validated)
  {
    $emailValidateResponse = resolve(AngelSmartApiService::class)->validateEmailOTP($account->email,$validated['email_otp']);
    $mobileValidateResponse = resolve(AngelSmartApiService::class)->validateSMSOTP($account->mobile,$account->email,$validated['mobile_otp']);
    if (!$emailValidateResponse['status']){
      $errors['email_otp'] = $email['message'] ?? 'Invalid email OTP';
    }
    if (!$mobileValidateResponse['status']){
      $errors['mobile_otp'] = $mobile['message'] ?? 'Invalid mobile OTP';
    }
    if (empty($errors)){
      $account->status = Account::STATUS_SIGNUP_SUCCESS;
      $account->save();
    }
    return [
      'email' => $emailValidateResponse,
      'mobile' => $mobileValidateResponse,
    ];
  }

  public function createStepThree($account)
  {
    if ($account->status == Account::STATUS_SIGNUP_SUCCESS) {
      return ['success' => true,'account' => $account];
    }
    return ['success' => false,'error' => 'Account not yet processed'];
  }

  public function generateTOTP($account,$validated)
  {
    $response = resolve(AngelSmartApiService::class)->generateTOTP($account->client_id,$validated['pin']);
    if ($response['status']){
      $account->pin = $validated['pin'];
      $account->save();
    }
    return $response;
  }

  public function createStepFour($account)
  {
    if ($account->status == Account::STATUS_SIGNUP_SUCCESS) {
      return ['success' => true,'account' => $account];
    }
    return ['success' => false,'error' => 'Account not yet processed'];
  }

  public function totpOtpResend($account)
  {
    return resolve(AngelSmartApiService::class)->totpOtpResend($account->client_id);
  }

  public function submitStepFour($account,$validated)
  {
    $response = resolve(AngelSmartApiService::class)->validateTOTP($account->client_id,$validated['email_mobile_otp']);
    if ($response['status']){
      $parsedUrl = parse_url($response['data']['uri']);
      parse_str($parsedUrl['query'], $queryParams);
      $account->totp_secret = $queryParams['secret'];
      $account->status = Account::STATUS_TOTP_ENABLE;
      $account->save();
    }
    return $response;
  }

  public function createStepFive($account)
  {
    if ($account->status == Account::STATUS_TOTP_ENABLE) {
      return ['success' => true,'account' => $account];
    }
    return ['success' => false,'error' => 'Account TOTP not enable'];
  }

  public function submitStepFive($account)
  {
    $smartApiLoginResponse = resolve(AngelSmartApiService::class)->smartApiLogin($account);
    $finalResponse = ['success' => true,'message' => 'Something went wrong'];
    if ($smartApiLoginResponse['status']){
      $data = $smartApiLoginResponse['data'];
      $account->smart_api_jwt_token = $data['jwtToken'];
      $account->smart_api_refresh_token = $data['refreshToken'];

      $smartApiLoginResponse = resolve(AngelSmartApiService::class)->getExistingApiKeys($data['jwtToken']);
      if ($smartApiLoginResponse['status']){
        $apiKeys = $smartApiLoginResponse['data'];
        $dmatxApiKey = null;
        $dmatxSecretKey = null;
        foreach ($apiKeys as $apiKey){
          if (Str::startsWith($apiKey['appname'], 'dmatx') && $apiKey['status'] == 0){
            $dmatxApiKey = $apiKey['apikey'];
            $dmatxSecretKey = $apiKey['secretkey'];
          }
        }
        if (!empty($dmatxApiKey)){
          $account->api_key = $dmatxApiKey;
          $account->client_secret = $dmatxSecretKey;
          $account->status = Account::STATUS_ACTIVE;
          $account->save();
          return ['success' => true,'message' => 'API key set successfully'];
        } else {
          $redirectUrl = route('angle-one.redirect.webhook',$account->id);
          $postbackUrl = route('angle-one.postback.webhook',$account->id);
          $payload = [
            'appname' => 'dmatx',
            'clientcode' => $account->client_id,
            'appiconpath' => '',
            'redirecturl' => $redirectUrl,
            'postbackurl' => $postbackUrl,
            'description' => 'des',
            'apptype' => 'Trading',

          ];
          $createApiKeyResponse = resolve(AngelSmartApiService::class)->createApiKey($data['jwtToken'],$payload);
          if ($createApiKeyResponse['status']){
            $account->api_key = $createApiKeyResponse['data']['apikey'];
            $account->client_secret = $createApiKeyResponse['data']['secretkey'];
            $account->status = Account::STATUS_ACTIVE;
            $account->save();
            return ['success' => true,'message' => 'API key set successfully'];
          } else {
            $finalResponse['message'] = $createApiKeyResponse['message'];
          }
        }
      } else {
        $finalResponse['message'] = $smartApiLoginResponse['message'];
      }
      $account->save();
    }else {
      $finalResponse['message'] = $smartApiLoginResponse['message'];
    }
    return $finalResponse;
  }

  public function accountUpdate($validatedData,$account)
  {
    $account->nickname = $validatedData['nickname'];
    $refreshAccount = false;
    if (!empty($validatedData['pin']) && $account->pin != $validatedData['pin']){
      $account->pin = $validatedData['pin'];
      $refreshAccount = true;
    }
    if (!empty($validatedData['api_key']) && $account->api_key != $validatedData['api_key']){
      $account->api_key = $validatedData['api_key'];
      $refreshAccount = true;
    }
    if ($refreshAccount){
      $response = resolve(AngelService::class)->refreshAccount($validatedData,$account);
      if ($response['status']){
        $data = $response['data'];
        $account->session_token = $data['jwtToken'];
        $account->refresh_token = $data['refreshToken'];
        $account->feed_token = $data['feedToken'];
        $account->save();
      } else {
        return ['success' => false,'message' => $response['message']];
      }
    }
    $account->save();
    return ['success' => true,'message' =>'Account updated successfully'];
  }
}
