<?php

namespace App\Services;

use GuzzleHttp\Client;

class AngelSmartApiService
{
  protected Client $client;
  protected string $baseUrl = 'https://apiconnect.angelone.in';

  public function __construct()
  {
    $this->client = new Client(['timeout' => 15]);
  }

  /**
   * Helper to get standard headers
   */
  private function getHeaders(): array
  {
    return [
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'Origin' => 'https://smartapi.angelbroking.com',
      'Referer' => 'https://smartapi.angelbroking.com/',
      'X-SourceID' => 'WEB',
      'X-UserType' => 'USER',
      'X-ClientLocalIP' => request()->ip() ?? '127.0.0.1',
      'X-ClientPublicIP' => request()->ip() ?? '127.0.0.1',
      'X-MACaddress' => '00:00:00:00:00:00',
      'X-PrivateKey' => 'smartapi_key', // Consider using config('services.angel.key')
    ];
  }

  public function signup(array $payload, $user): array
  {
    $requestPayload = [
      'name' => $payload['account_name'],
      'email' => $payload['email'],
      'mobileno' => $payload['mobile'],
      'clientcode' => $payload['client_id'],
      'password' => $payload['password'],
      'stateresidence' => 'state',
    ];

    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/client/v1/signup', [
      'headers' => $this->getHeaders(),
      'json' => $requestPayload,
    ]);

    return json_decode($response->getBody(), true);
  }

  public function emailOtpResend(string $email): array
  {
    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/client/v1/sendEmailOTP', [
      'headers' => $this->getHeaders(),
      'json' => ['email' => $email],
    ]);

    return json_decode($response->getBody(), true);
  }

  public function mobileOtpResend(string $mobile, string $email): array
  {
    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/client/v1/sendSMSOTP', [
      'headers' => $this->getHeaders(),
      'json' => [
        'mobileNumber' => $mobile,
        'email' => $email
      ],
    ]);

    return json_decode($response->getBody(), true);
  }

  public function validateEmailOTP(string $email, string $otp): array
  {
    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/client/v1/validateEmailOTP', [
      'headers' => $this->getHeaders(),
      'json' => [
        'email' => $email,
        'otp' => $otp,
      ],
    ]);

    return json_decode($response->getBody(), true);
  }

  /**
   * Validate SMS (Mobile) OTP
   */
  public function validateSMSOTP(string $mobile, string $email, string $otp): array
  {
    $payload = [
      'mobileNumber' => $mobile,
      'email' => $email,
      'otp' => $otp,
    ];
    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/client/v1/validateSMSOTP', [
      'headers' => $this->getHeaders(),
      'json' => $payload,
    ]);

    return json_decode($response->getBody(), true);
  }

  public function generateTOTP(string $clientCode, string $pin): array
  {
    $payload = [
      'clientcode' => $clientCode,
      'password' => $pin,
    ];

    $response = $this->client->post(
      $this->baseUrl . '/rest/auth/angelbroking/user/v1/totp/login',
      [
        'headers' => $this->getHeaders(),
        'json' => $payload,
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function totpOtpResend(string $clientCode)
  {
    $response = $this->client->post($this->baseUrl . '/rest/auth/angelbroking/user/v1/totp/otp/generate', [
      'headers' => $this->getHeaders(),
      'json' => [
        'clientcode' => $clientCode,
      ],
    ]);

    return json_decode($response->getBody(), true);
  }

  public function validateTOTP(string $clientCode, string $otp): array
  {
    $payload = [
      'clientcode' => $clientCode,
      'otp' => $otp,
    ];

    $response = $this->client->post(
      $this->baseUrl . '/rest/auth/angelbroking/user/v1/totp/otp/verify',
      [
        'headers' => $this->getHeaders(),
        'json' => $payload,
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function smartApiLogin($account)
  {
    $payload = [
      'email' => $account->email,
      'password' => $account->password,
    ];

    $response = $this->client->post(
      $this->baseUrl . '/rest/auth/angelbroking/client/v1/login',
      [
        'headers' => $this->getHeaders(),
        'json' => $payload,
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function getExistingApiKeys($jwtToken)
  {
    $headers = $this->getHeaders();
    $headers['Authorization'] = 'Bearer ' . $jwtToken;
    $response = $this->client->get(
      $this->baseUrl . '/rest/secure/angelbroking/client/v1/getapps',
      [
        'headers' => $headers,
      ]
    );

    return json_decode($response->getBody(), true);
  }

  public function createApiKey($jwtToken, $payload)
  {
    $headers = $this->getHeaders();
    $headers['Authorization'] = 'Bearer ' . $jwtToken;

    $response = $this->client->post(
      $this->baseUrl . '/rest/secure/angelbroking/client/v1/createapp',
      [
        'headers' => $headers,
        'json' => $payload,
      ]
    );
    return json_decode($response->getBody(), true);
  }
}
