<?php

namespace App\Services;

use App\Mail\Auth\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
  /**
   * Send password reset link using your custom Mailable.
   *
   * This method:
   *  - finds the user by email
   *  - creates a password reset token
   *  - builds the reset URL (uses route named `password.reset`)
   *  - sends the email with App\Mail\Auth\ResetPasswordMail
   *
   * @param  string  $email
   * @return string  A password status constant (e.g. Password::RESET_LINK_SENT)
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function sendResetLink(string $email): string
  {
    // Find the user
    $user = User::where('email', $email)->first();

    if (empty($user)) {
      throw ValidationException::withMessages([
        'email' => trans('passwords.user'), // or custom message
      ]);
    }

    // Create a password reset token for the user
    $token = Password::broker()->createToken($user);

    // Build the reset URL (false => generates relative path; wrapped in url() to make absolute)
    $url = url(route('password.reset', [
      'token' => $token,
      'email' => $user->email,
    ], false));

    // Send the email using your custom Mailable
    // Use ->queue(...) if you have queues configured to avoid blocking
    Mail::to($user->email)->send(new ResetPasswordMail($url));

    // Optionally you can verify Mail failures; Laravel's Mail::send does not always throw on failure.
    // For consistency with Password::sendResetLink return value, return the constant:
    return Password::RESET_LINK_SENT;
  }

  /**
   * Reset the password.
   *
   * Expects $data to contain: token, email, password, password_confirmation
   *
   * @param  array  $data
   * @return string
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function resetPassword(array $data): string
  {
    $status = Password::reset(
      $data,
      function (User $user, string $password) {
        $user->password = Hash::make($password);
        $user->save(); // your rule: always use save()

        // Logout from previous tokens/devices (optional)
        Password::deleteToken($user);
      }
    );

    if ($status !== Password::PASSWORD_RESET) {
      throw ValidationException::withMessages([
        'email' => __($status),
      ]);
    }

    return $status;
  }
}
