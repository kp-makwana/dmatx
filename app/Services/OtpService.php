<?php

namespace App\Services;

use App\Mail\Auth\SendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
  /**
   * Verify OTP
   */
  public function verify(User $user, string $otp): bool
  {
    if ($user->otp !== $otp) {
      throw ValidationException::withMessages([
        'otp' => 'Invalid OTP entered.',
      ]);
    }

    if (now()->greaterThan($user->otp_expires_at)) {
      throw ValidationException::withMessages([
        'otp' => 'OTP expired. Please request a new one.',
      ]);
    }

    // Mark email as verified
    $user->email_verified_at = now();
    $user->otp = null;
    $user->otp_expires_at = null;
    $user->save(); // Your rule: always use save()

    // Auto login the user
    Auth::login($user);

    activity()
    ->performedOn($user)
    ->causedBy($user)
    ->log('Email Verified via OTP');

    return true;
  }

  /**
   * Resend OTP
   */
  public function resend(User $user): void
  {
    $otp = random_int(100000, 999999);

    $user->otp = $otp;
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save(); // Always save

    Mail::to($user->email)->send(new SendOtpMail($user));

    activity()
      ->performedOn($user)
      ->causedBy($user)
      ->log('OTP Resent');
  }
}
