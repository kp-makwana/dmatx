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
}
