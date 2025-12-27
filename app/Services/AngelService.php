<?php

namespace App\Services;

use App\Models\V1\Account;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use OTPHP\TOTP;

class AngelService
{
  protected Client $client;
  protected string $baseUrl = "https://apiconnect.angelone.in";

  public function __construct()
  {
    $this->client = new Client();
  }

  /**
   * Login using password + TOTP
   */
  public function login(Account $account)
  {
    try {
      $clientCode = $account->client_id;
      $apiKey = $account->api_key;
      $password = $account->pin;
      $totp = self::generateTOTP($account->totp_secret);


      $endpoint = $this->baseUrl . "/rest/auth/angelbroking/user/v1/loginByPassword";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $apiKey,
      ];

      $payload = [
        "clientcode" => $clientCode,
        "password" => $password,
        "totp" => $totp,
        "state" => "live"
      ];


      $response = $this->client->post($endpoint, [
        'headers' => $headers,
        'json' => $payload
      ]);
      $success = json_decode($response->getBody(), true);

      $data = $success['data'] ?? null;
      if ($success['status'] && !empty($data)) {
        $account->session_token = $data['jwtToken'];
        $account->refresh_token = $data['refreshToken'];
        $account->feed_token = $data['feedToken'];
        $account->is_active = 1;
        $account->status = Account::STATUS_ACTIVE;

        $account->last_error = null;
        $account->last_login_at = Carbon::now();
        $account->token_expiry = self::getTokenExpiry($data['refreshToken']);

        $account = $this->setUserAccountName($account);

        $account->save();

        activity()
          ->performedOn($account)
          ->withProperties([
            'state' => $data['state'],
            'session_token' => $data['jwtToken'],
          ])
          ->log('Angel login successful');
        return ['success' => true, 'message' => 'Angel login successful'];
      } else {
        $account->is_active = 0;
        $account->last_error_code = $success['errorcode'] ?? null;
        $account->last_error = $success['message'];
        $account->status = 'Fail';
        $account->save();

        activity()
          ->performedOn($account)
          ->withProperties([
            'client_id' => $account->client_id,
            'error' => $success['message'],
          ])
          ->log('Angel login failed');
        return ['success' => false, 'message' => $success['message']];
      }
    } catch (\Exception $e) {
      activity()
        ->performedOn($account)
        ->withProperties([
          'client_id' => $account->client_id,
          'error' => $e->getMessage(),
        ])
        ->log('Angel login failed');
      Log::error("Angel Login Error: " . $e->getMessage());
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public function refreshAccount($validatedData,$account)
  {
    $clientCode = $account->client_id;
    if (!empty($validatedData['api_key'])){
      $apiKey = $validatedData['api_key'];
    } else {
      $apiKey = $account->api_key;
    }
    if (!empty($validatedData['pin'])){
      $password = $validatedData['pin'];
    } else {
      $password = $account->api_key;
    }
    $totp = self::generateTOTP($account->totp_secret);

    $endpoint = $this->baseUrl . "/rest/auth/angelbroking/user/v1/loginByPassword";

    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'X-UserType' => 'USER',
      'X-SourceID' => 'WEB',
      'X-ClientLocalIP' => request()->ip(),
      'X-ClientPublicIP' => request()->ip(),
      'X-MACAddress' => '00:00:00:00:00:00',
      'X-PrivateKey' => $apiKey,
    ];

    $payload = [
      "clientcode" => $clientCode,
      "password" => $password,
      "totp" => $totp,
      "state" => "live"
    ];


    $response = $this->client->post($endpoint, [
      'headers' => $headers,
      'json' => $payload
    ]);
    return json_decode($response->getBody(), true);
  }

  public static function generateTOTP(string $totpSecret): string
  {
    $totp = TOTP::create($totpSecret, 30, 'sha1', 6);
    return $totp->now();
  }

  public function getTokenExpiry(string $token)
  {
    $decoded = self::decodeJwt($token);
    $refreshToken = $decoded['payload']['REFRESH-TOKEN'] ?? null;
    $decodedData = self::decodeJwt($refreshToken);
    $expiry = $decodedData['payload']['exp'] ?? null;

    return Carbon::createFromTimestamp($expiry)->setTimezone(config('app.timezone'));
  }

  public static function decodeJwt(string $jwt): ?array
  {
    $parts = explode('.', $jwt);

    if (count($parts) !== 3) {
      return null;
    }

    return [
      'header' => json_decode(self::base64UrlDecode($parts[0]), true),
      'payload' => json_decode(self::base64UrlDecode($parts[1]), true),
      'signature' => $parts[2],
    ];
  }

  private static function base64UrlDecode(string $input): string
  {
    $remainder = strlen($input) % 4;
    if ($remainder > 0) {
      $input .= str_repeat('=', 4 - $remainder);
    }
    $input = str_replace(['-', '_'], ['+', '/'], $input);
    return base64_decode($input);
  }

  public function setUserAccountName($account)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/user/v1/getProfile";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
        'Authorization' => 'Bearer ' . $account->session_token,
      ];

      $response = $this->client->get($endpoint, [
        'headers' => $headers
      ]);

      $result = json_decode($response->getBody(), true);

      if ($result['status'] && !empty($result['data']['name'])) {

        // Save user full name if needed
        $account->account_name = $result['data']['name'];

        // Log activity
        activity()
          ->performedOn($account)
          ->withProperties([
            'clientcode' => $result['data']['clientcode'],
            'name' => $result['data']['name'],
          ])
          ->log('Fetched Angel user profile');
      }

    } catch (\Exception $e) {
      activity()
        ->performedOn($account)
        ->withProperties([
          'client_id' => $account->client_id,
          'error' => $e->getMessage(),
        ])
        ->log('Angel API: getProfile failed');

      Log::error("Angel getProfile Error: " . $e->getMessage());
    }
    return $account;
  }

  public function getRMS($account)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/user/v1/getRMS";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
        'Authorization' => 'Bearer ' . $account->session_token,
      ];


      $response = $this->client->get($endpoint, [
        'headers' => $headers
      ]);

      $result = json_decode($response->getBody(), true);
      $data = $result['data'];
      if (!empty($data)){
        return ['success' => true, 'message' => 'Balance fetch successfully', 'data' => $data];
      }
      $errorCode = $result['errorCode'] ?? null;
      if ($errorCode == 'AG8001') {
        $refreshTokenResponse = $this->generateTokens($account);
        if ($refreshTokenResponse['success']) {
          $this->getRMS($account);
        }
      }
      $account->save();
      return ['success' => false, 'message' => 'Internal server error', 'data' => $account];
    } catch (\Exception $exception) {
      return ['success' => false, 'message' => $exception->getMessage()];
    }
  }

  private function generateTokens(&$account)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/auth/angelbroking/jwt/v1/generateTokens";
      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
      ];

      $payload = [
        'refreshToken' => $account->refresh_token,
      ];

      $response = $this->client->post($endpoint, [
        'headers' => $headers,
        'json' => $payload,
      ]);

      $result = json_decode($response->getBody(), true);
      $data = $result['data'];
      if (!empty($data)){
        $account->session_token = $data['jwtToken'];
        $account->refresh_token = $data['refreshToken'];
        $account->feed_token = $data['feedToken'];
        return ['success' => true, 'message' => 'Balance fetch successfully', 'data' => $data];
      }
      $errorCode = $result['errorCode'];
      $message = $result['message'];
      if ($errorCode == 'AG8001') {
        $refreshTokenResponse = $this->login($account);
        if ($refreshTokenResponse['success']) {
          $message = $refreshTokenResponse['message'];
          return ['success' => true, 'message' => $message, 'data' => $refreshTokenResponse['data']];
        }
      }
      return ['success' => false, 'message' => $message, $result['data']];
    } catch (\Exception $exception) {
      Log::error("Angel refreshToken Error: " . $exception->getMessage());
      return ['success' => false, 'message' => $exception->getMessage()];
    }
  }

  public function getAllHolding($account)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/portfolio/v1/getAllHolding";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
        'Authorization' => 'Bearer ' . $account->session_token,
      ];


      $response = $this->client->get($endpoint, [
        'headers' => $headers
      ]);

      $result = json_decode($response->getBody(), true);

      $data = $result['data'];
      if (!empty($data)){
        return ['success' => true, 'message' => 'All holding fetch successfully', 'data' => $data];
      }
      $errorCode = $result['errorCode'] ?? null;
      if ($errorCode == 'AG8001') {
        $refreshTokenResponse = $this->generateTokens($account);
        if ($refreshTokenResponse['success']) {
          $this->getAllHolding($account);
        }
      }
      $account->save();
      return ['success' => false, 'message' => 'Internal server error', 'data' => $account];
    } catch (\Exception $exception) {
      return ['success' => false, 'message' => $exception->getMessage().':'.$exception->getLine()];
    }
  }

  public function getOrderBook($account)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/order/v1/getOrderBook";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
        'Authorization' => 'Bearer ' . $account->session_token,
      ];


      $response = $this->client->get($endpoint, [
        'headers' => $headers
      ]);

      $result = json_decode($response->getBody(), true);

      $data = $result['data'];
      if (!empty($data)){
        return ['success' => true, 'message' => 'Orders fetch successfully', 'data' => $data];
      }
      $errorCode = $result['errorCode'] ?? null;
      if ($errorCode == 'AG8001') {
        $refreshTokenResponse = $this->generateTokens($account);
        if ($refreshTokenResponse['success']) {
          $this->getOrderBook($account);
        }
      }
      $account->save();
      return ['success' => false, 'message' => 'Internal server error', 'data' => $account];
    } catch (\Exception $exception) {
      return ['success' => false, 'message' => $exception->getMessage().':'.$exception->getLine()];
    }
  }

  public function cancelOrder($account,$order)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/order/v1/cancelOrder";

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-UserType' => 'USER',
        'X-SourceID' => 'WEB',
        'X-ClientLocalIP' => request()->ip(),
        'X-ClientPublicIP' => request()->ip(),
        'X-MACAddress' => '00:00:00:00:00:00',
        'X-PrivateKey' => $account->api_key,
        'Authorization' => 'Bearer ' . $account->session_token,
      ];


      $payload = [
        'variety' => 'NORMAL',
        'orderid' => $order
      ];

      $response = $this->client->post($endpoint, [
        'headers' => $headers,
        'json'    => $payload
      ]);

      $result = json_decode($response->getBody(), true);
      $data = $result['data'];
      if (!empty($data)){
        return ['success' => true, 'message' => 'Order cancel successfully', 'data' => $data];
      }
      $errorCode = $result['errorCode'] ?? null;
      if ($errorCode == 'AG8001') {
        $refreshTokenResponse = $this->generateTokens($account);
        if ($refreshTokenResponse['success']) {
          $this->cancelOrder($account,$order);
        }
      }
      $account->save();
      return ['success' => false, 'message' => 'Internal server error', 'data' => $account];
    } catch (\Exception $exception) {
      return ['success' => false, 'message' => $exception->getMessage()];
    }
  }

  public function modifyOrder(Account $account, array $payload)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/order/v1/modifyOrder";

      $headers = [
        'Content-Type'      => 'application/json',
        'Accept'            => 'application/json',
        'X-UserType'        => 'USER',
        'X-SourceID'        => 'WEB',
        'X-ClientLocalIP'   => request()->ip(),
        'X-ClientPublicIP'  => request()->ip(),
        'X-MACAddress'      => '00:00:00:00:00:00',
        'X-PrivateKey'      => $account->api_key,
        'Authorization'     => 'Bearer ' . $account->session_token,
      ];


      $requestPayload = [
        'variety'   => 'NORMAL',
        'orderid'   => $payload['orderid'],
        'ordertype' => $payload['ordertype'],
        'producttype' => 'DELIVERY',
        'duration'   => 'DAY',
        'quantity'  => (int) $payload['quantity'],
        'tradingsymbol' => $payload['tradingsymbol'].'-EQ',
        'symboltoken' => $payload['symboltoken'] ?? null,
        'exchange' => 'NSE',
      ];

      if ($payload['ordertype'] === 'LIMIT') {
        $requestPayload['price'] = (float) $payload['price'];
      }

      $response = $this->client->post($endpoint, [
        'headers' => $headers,
        'json'    => $requestPayload,
      ]);
      $result = json_decode($response->getBody(), true);

      if (!empty($result['data'])) {
        return [
          'success' => true,
          'message' => 'Order modified successfully',
          'data'    => $result['data'],
        ];
      }

      if (($result['errorCode'] ?? null) === 'AG8001') {
        $refresh = $this->generateTokens($account);
        if ($refresh['success']) {
          return $this->modifyOrder($account, $payload);
        }
      }

      return [
        'success' => false,
        'message' => $result['message'] ?? 'Order modify failed',
      ];

    } catch (\Exception $e) {
      Log::error('Angel Modify Order Error', [
        'error' => $e->getMessage(),
        'line'  => $e->getLine(),
      ]);

      return [
        'success' => false,
        'message' => $e->getMessage(),
      ];
    }
  }

  public function placeOrder($account,$payload)
  {
    try {
      $endpoint = $this->baseUrl . "/rest/secure/angelbroking/order/v1/placeOrder";

      $headers = [
        'Content-Type'      => 'application/json',
        'Accept'            => 'application/json',
        'X-UserType'        => 'USER',
        'X-SourceID'        => 'WEB',
        'X-ClientLocalIP'   => request()->ip(),
        'X-ClientPublicIP'  => request()->ip(),
        'X-MACAddress'      => '00:00:00:00:00:00',
        'X-PrivateKey'      => $account->api_key,
        'Authorization'     => 'Bearer ' . $account->session_token,
      ];

      /**
       * Required payload structure
       */
      $requestPayload = [
        'variety'         => 'NORMAL',
        'tradingsymbol'  => $payload['tradingsymbol'],
        'symboltoken'    => $payload['symboltoken'],
        'exchange'       => $payload['exchange'] ?? 'NSE',
        'transactiontype'=> strtoupper($payload['transactiontype']),
        'ordertype'      => strtoupper($payload['ordertype']),
        'producttype'    => $payload['producttype'] ?? 'DELIVERY',
        'duration'       => 'DAY',
        'scripconsent'   => 'YES',
        'quantity'       => (int) $payload['quantity'],
      ];

      /**
       * LIMIT order needs price
       */
      if ($requestPayload['ordertype'] === 'LIMIT') {
        $requestPayload['price'] = (float) $payload['price'];
      }

      /**
       * STOPLOSS (optional)
       */
      if (!empty($payload['triggerprice'])) {
        $requestPayload['triggerprice'] = (float) $payload['triggerprice'];
      }

      $response = $this->client->post($endpoint, [
        'headers' => $headers,
        'json'    => $requestPayload,
      ]);

      $result = json_decode($response->getBody(), true);

      /**
       * SUCCESS
       */
      if (!empty($result['data'])) {
        activity()
          ->performedOn($account)
          ->withProperties([
            'orderid' => $result['data']['orderid'] ?? null,
            'payload' => $requestPayload,
          ])
          ->log('Angel order placed');

        return [
          'success' => true,
          'message' => 'Order placed successfully',
          'data'    => $result['data'],
        ];
      }

      /**
       * TOKEN EXPIRED → REFRESH
       */
      if (($result['errorCode'] ?? null) === 'AG8001') {
        $refresh = $this->generateTokens($account);
        if ($refresh['success']) {
          return $this->placeOrder($account, $payload);
        }
      }

      return [
        'success' => false,
        'message' => $result['message'] ?? 'Order placement failed',
        'errorCode' => $result['errorCode'] ?? null,
      ];

    } catch (\Exception $e) {
      Log::error('Angel PlaceOrder Error', [
        'error' => $e->getMessage(),
        'line'  => $e->getLine(),
        'payload' => $payload,
      ]);

      return [
        'success' => false,
        'message' => $e->getMessage(),
      ];
    }
  }
}
