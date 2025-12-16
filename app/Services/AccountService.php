<?php

namespace App\Services;

use App\Http\Resources\V1\Account\ListResource;
use App\Jobs\AngelLoginJob;
use App\Models\V1\Account;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
}
