<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $table = 'accounts';
  protected $guarded = [];

  protected $casts = [
    'net' => 'decimal:2',
    'amount_used' => 'decimal:2',
  ];

  public const TYPE_MANUAL = 'manual';
  public const TYPE_AUTO = 'auto';

  public const STATUS_SIGNUP_FORM_SUBMITTED = 'signup_form_submitted';
  public const STATUS_SIGNUP_SUCCESS = 'signup_success';
  public const STATUS_TOTP_ENABLE = 'totp_enable';
  public const STATUS_ACTIVE = 'active';
}
