<?php

namespace App\Services;

use App\Models\V1\Account;
use Illuminate\Support\Facades\Auth;

class AccountService
{
  /**
   * Store a new Smart-API account
   */
  public function create(array $data): Account
  {
    $account = new Account();

    $account->user_id       = Auth::id();
    $account->account_name  = $data['nickname'];
    $account->client_id     = $data['client_id'];
    $account->api_key       = $data['api_key'];
    $account->client_secret = $data['client_secret'];
    $account->totp_secret   = $data['totp_secret'];
    $account->status        = 'active';
    $account->is_active     = 1;

    $account->save();

    activity()
      ->performedOn($account)
      ->causedBy(Auth::user())
      ->withProperties([
        'client_id' => $account->client_id,
        'account_name' => $account->account_name
      ])
      ->log('New Smart-API Account Created');

    return $account;
  }
}
