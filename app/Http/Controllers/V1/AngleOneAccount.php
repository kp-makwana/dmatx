<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\AngleOneAccountCreateRequest;
use App\Http\Requests\V1\Accounts\OTPValidateRequest;
use App\Http\Requests\V1\Accounts\PinValidationRequest;
use App\Http\Requests\V1\Accounts\TOTPValidationRequest;
use App\Models\V1\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

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
    $this->authorize('create', Account::class);
    $response = $this->service->createStepTwo($account);
    if (!$response['success']) {
      Session::flash('error','Account not added properly');
      return redirect()->back();
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-two',compact('account','pageConfigs'));
  }

  public function emailOtpResend(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->emailOtpResend($account);
    if (!$response['status']) {
        Session::flash('error', $response['message']);
        return $this->errorResponse($response['message']);
    }
    return $this->successResponse('Email OTP Resend');
  }

  public function mobileOtpResend(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->mobileOtpResend($account);
    if (!$response['status']) {
      Session::flash('error', $response['message']);
      return $this->errorResponse($response['message']);
    }
    return $this->successResponse('Mobile OTP Resend');
  }

  public function submitStepTwo(OTPValidateRequest $request,Account $account)
  {
    $this->authorize('update', $account);
    $validated = $request->validated();
    $response = $this->service->submitStepTwo($account,$validated);
    $email = $response['email'];
    $mobile = $response['mobile'];
    if (!$email['status']){
      $errors['email_otp'] = $email['message'] ?? 'Invalid email OTP';
    }
    if (!$mobile['status']){
      $errors['mobile_otp'] = $mobile['message'] ?? 'Invalid mobile OTP';
    }
    if (! empty($errors)) {
      throw ValidationException::withMessages($errors);
    }
    return redirect()
      ->route('angle-one.create.step.three', ['account' => $account->id])
      ->with('success', 'OTP verified successfully');
  }

  public function createStepThree(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->createStepThree($account);
    if (!$response['success']) {
      Session::flash('error','Account not yet processed');
      return redirect()->back();
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-three',compact('account','pageConfigs'));
  }

  public function submitStepThree(PinValidationRequest $request,Account $account)
  {
    $this->authorize('update', $account);
    $validated = $request->validated();
    $response = $this->service->generateTOTP($account,$validated);
    if (!$response['status']){
      $message = $response['message'];
      Session::flash('error',$message);
      $errors['pin'] = $message;
      throw ValidationException::withMessages($errors);
    }
    return redirect()->route('angle-one.create.step.four',$account->id)->with('success','OTP Sent successfully');
  }

  public function createStepFour(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->createStepFour($account);
    if (!$response['success']) {
      Session::flash('error','Account not yet processed');
      return redirect()->back();
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-four',compact('account','pageConfigs'));
  }

  public function submitStepFour(TOTPValidationRequest $request,Account $account)
  {
    $this->authorize('update', $account);
    $validated = $request->validated();
    $response = $this->service->submitStepFour($account,$validated);
    if (!$response['status']){
      $errors['email_mobile_otp'] = $response['message'] ?? 'Invalid OTP';
    }
    if (! empty($errors)) {
      throw ValidationException::withMessages($errors);
    }
    return redirect()
      ->route('angle-one.create.step.five', ['account' => $account->id])
      ->with('success', 'OTP verified successfully');
  }

  public function resendTotpOtp(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->totpOtpResend($account);
    if (!$response['status']) {
      Session::flash('error', $response['message']);
      return $this->errorResponse($response['message']);
    }
    return $this->successResponse('OTP Resend');
  }

  public function createStepFive(Account  $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->createStepFive($account);
    if (!$response['success']) {
      Session::flash('error',$response['message']);
      return redirect()->back();
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-five',compact('account','pageConfigs'));
  }

  public function submitStepFive(Account $account)
  {
    $this->authorize('update', $account);
    $response = $this->service->submitStepFive($account);
    if (!$response['success']) {
      Session::flash('error',$response['message']);
      return redirect()->back();
    }
    return redirect()->route('accounts.show',$account->id);
  }
}
