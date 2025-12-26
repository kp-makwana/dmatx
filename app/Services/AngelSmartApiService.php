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
}
