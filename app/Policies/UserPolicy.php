<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
  /**
   * Admin can view all users.
   */
  public function viewAny(User $user): bool
  {
    return $user->hasRole('admin');
  }

  /**
   * User can view themselves, admin can view anyone.
   */
  public function view(User $user, User $model): bool
  {
    return $user->id === $model->id || $user->hasRole('admin');
  }

  /**
   * Only admin can create users.
   */
  public function create(User $user): bool
  {
    return $user->hasRole('admin');
  }

  /**
   * User can update themselves, admin can update any user.
   */
  public function update(User $user, User $model): bool
  {
    return $user->id === $model->id || $user->hasRole('admin');
  }

  /**
   * Only admin can delete users.
   */
  public function delete(User $user, User $model): bool
  {
    return $user->hasRole('admin');
  }

  /**
   * Only admin can restore users.
   */
  public function restore(User $user, User $model): bool
  {
    return $user->hasRole('admin');
  }

  /**
   * Only admin can force-delete users.
   */
  public function forceDelete(User $user, User $model): bool
  {
    return $user->hasRole('admin');
  }
}
