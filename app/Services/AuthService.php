<?php

namespace App\Services;

use App\Mail\Auth\SendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthService
{
  /**
   * Register new user with Email OTP
   */
  public function register(array $data): User
  {
    // Generate OTP
    $otp = $this->generateOtp();

    // Create user
    $user = new User();
    $user->name           = $data['name'];
    $user->email          = $data['email'];
    $user->password       = Hash::make($data['password']);
    $user->otp            = $otp;
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save();

    // Assign default role
    $user->assignRole('user');

    // Log registration
    activity()
      ->performedOn($user)
      ->causedBy($user)
      ->withProperties(['otp_sent' => true])
      ->log('User Registered (OTP Sent)');

    // Send OTP email
    Mail::to($user->email)->send(new SendOtpMail($user));

    return $user;
  }

  /**
   * Login user — only allowed after verifying OTP
   */
  public function login(array $data): bool
  {
    if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
      return false;
    }

    $user = Auth::user();

    // Email not verified?
    if (is_null($user->email_verified_at)) {
      Auth::logout();

      throw ValidationException::withMessages([
        'email' => 'Please verify your email using the OTP sent to you.',
      ]);
    }

    // Log login activity
    activity()
      ->causedBy($user)
      ->log('User Logged In');

    return true;
  }

  /**
   * Resend OTP — uses save() instead of update()
   */
  public function resendOtp(User $user): void
  {
    $otp = $this->generateOtp();

    $user->otp            = $otp;
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save(); // <<--- always save()

    Mail::to($user->email)->send(new SendOtpMail($user));

    activity()
      ->performedOn($user)
      ->causedBy($user)
      ->log('OTP Resent');
  }

  /**
   * Generate secure OTP
   */
  private function generateOtp(): int
  {
    return random_int(100000, 999999);
  }
}
