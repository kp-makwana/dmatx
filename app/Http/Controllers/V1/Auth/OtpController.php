<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
  protected OtpService $otp;

  public function __construct(OtpService $otp)
  {
    $this->otp = $otp;
  }

  /**
   * Show OTP verification form
   */
  public function showVerifyForm(User $user)
  {
    return view('auth.verify-otp', compact('user'));
  }

  /**
   * Verify OTP
   */
  public function verifyOtp(Request $request, User $user)
  {
    $request->validate([
      'otp' => 'required|numeric',
    ]);

    try {
      $this->otp->verify($user, $request->otp);
    } catch (ValidationException $e) {
      return back()->withErrors($e->errors());
    }

    return redirect()->route('dashboard')->with('success', 'Your email has been verified!');
  }

  /**
   * Resend OTP
   */
  public function resendOtp(User $user)
  {
    $this->otp->resend($user);

    return back()->with('success', 'A new OTP has been sent to your email.');
  }
}
