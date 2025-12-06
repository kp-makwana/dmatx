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
    $pageConfigs = ['myLayout' => 'blank'];
    return view('auth.verify-otp', compact('user','pageConfigs'));
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
      return back()->withErrors([
        'otp' => $e->getMessage() ?? __('otp.invalid')
      ]);
    }

    return redirect()
      ->route('dashboard')
      ->with('success', __('otp.verified'));
  }

  /**
   * Resend OTP
   */
  public function resendOtp(User $user)
  {
    $this->otp->resend($user);

    return back()->with('success', __('otp.resent'));
  }
}
