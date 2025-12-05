<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordService
{
  /**
   * Send password reset link
   */
  public function sendResetLink(string $email): string
  {
    $status = Password::sendResetLink(['email' => $email]);

    if ($status !== Password::RESET_LINK_SENT) {
      throw ValidationException::withMessages([
        'email' => __($status),
      ]);
    }

    return $status;
  }

  /**
   * Reset the password
   */
  public function resetPassword(array $data): string
  {
    $status = Password::reset(
      $data,
      function (User $user, $password) {
        $user->password = Hash::make($password);
        $user->save(); // your rule: always use save()

        // Logout from previous tokens/devices (optional)
        $user->tokens()?->delete();
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
