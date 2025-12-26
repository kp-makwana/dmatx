<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\AngleOneAccountCreateRequest;
use App\Models\V1\Account;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AngleOneAccount extends Controller
{
  protected AccountService $service;

  public function __construct(AccountService $service)
  {
    $this->service = $service;
  }
  public function createStepOne()
  {
    $this->authorize('create',Account::class);
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-one',compact('pageConfigs'));
  }

  public function submitStepOne(AngleOneAccountCreateRequest $request)
  {
    $this->authorize('create', Account::class);
    $validated = $request->validated();

    $user = Auth::user();
    $response = $this->service->stepOne($validated,$user);
    if (!$response['status']) {
      Session::flash('error', $response['message']);
      return redirect()->back();
    }
    $accountId = $response['data']['id'];
    return redirect()->route('angle-one.create.step.two', ['account' => $accountId]);
  }

  public function createStepTwo(Account $account)
  {
    dd($account);
  }

  public function submitStepTwo()
  {

  }
}
