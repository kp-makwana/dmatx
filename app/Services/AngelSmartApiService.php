<?php

namespace App\Services;

use GuzzleHttp\Client;

class AngelSmartApiService
{
  protected Client $client;
  protected string $baseUrl = 'https://apiconnect.angelone.in';

  public function __construct()
  {
    $this->client = new Client([
      'timeout' => 15,
    ]);
  }

  /**
   * AngelOne SmartAPI Signup
   */
  public function signup(array $payload, $user): array
  {
    $endpoint = $this->baseUrl . '/rest/auth/angelbroking/client/v1/signup';
    $headers = [
      'Accept' => 'application/json',
      'Content-Type' => 'application/json',
      'Origin' => 'https://smartapi.angelbroking.com',
      'Referer' => 'https://smartapi.angelbroking.com/',
      'X-SourceID' => 'WEB',
      'X-UserType' => 'USER',
      'X-ClientLocalIP' => '127.0.0.1',
      'X-ClientPublicIP' => '127.0.0.1',
      'X-MACaddress' => '00:00:00:00:00:00',
      'X-PrivateKey' => 'smartapi_key',
    ];

    /**
     * Required Payload Structure
     */
    $requestPayload = [
      'name' => $payload['account_name'],
      'email' => $payload['email'],
      'mobileno' => $payload['mobile'],
      'clientcode' => $payload['client_id'],
      'password' => $payload['password'],
      'stateresidence' => 'state',
    ];

    /*$response = $this->client->post($endpoint, [
      'headers' => $headers,
      'json'    => $requestPayload,
    ]);
    $result = json_decode($response->getBody(), true);*/

    // TODO remove static code
    return [
      "status" => true,
      "message" => "Please verify email and mobile otp.",
      "errorcode" => "",
      "data" => null,
    ];

    /*return [
      'message'   => 'Failed to send otp.',
      'errorcode' => 'AB1040',
      'status'    => false,
      'data'      => null,
    ];*/
  }

}
