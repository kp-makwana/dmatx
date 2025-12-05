<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
  protected PasswordService $service;

  public function __construct(PasswordService $service)
  {
    $this->service = $service;
  }

  public function showLinkRequestForm()
  {
    return view('auth.forgot-password');
  }

  public function sendResetLinkEmail(Request $request)
  {
    $request->validate([
      'email' => 'required|email'
    ]);

    $this->service->sendResetLink($request->email);

    return back()->with('success', 'Password reset link has been sent to your email.');
  }

  public function showResetForm($token)
  {
    return view('auth.reset-password', ['token' => $token]);
  }

  public function resetPassword(Request $request)
  {
    $request->validate([
      'token'    => 'required',
      'email'    => 'required|email',
      'password' => 'required|min:6|confirmed',
    ]);

    $this->service->resetPassword([
      'email'                 => $request->email,
      'password'              => $request->password,
      'password_confirmation' => $request->password_confirmation,
      'token'                 => $request->token,
    ]);

    return redirect()
      ->route('login')
      ->with('success', 'Password has been reset successfully.');
  }
}
