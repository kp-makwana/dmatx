<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\AngleOneAccountCreateRequest;
use App\Models\V1\Account;
use Illuminate\Http\Request;

class AngleOneAccount extends Controller
{
  public function createStepOne()
  {
    $this->authorize('create',Account::class);
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-one',compact('pageConfigs'));
  }

  public function submitStepOne(AngleOneAccountCreateRequest $request)
  {
    $this->authorize('create', Account::class);
    dd($request->all());
  }
}
