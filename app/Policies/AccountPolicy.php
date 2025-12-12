<?php

namespace App\Policies;

use App\Models\User;
use App\Models\V1\Account;

class AccountPolicy
{
  /**
   * Admin can view all accounts.
   */
  public function viewAny(User $user): bool
  {
    return $user->hasPermissionTo('view_accounts') || $user->hasRole('admin');
  }

  /**
   * User can view only their own account; admin can view all.
   */
  public function view(User $user, Account $account): bool
  {
    return $user->id == $account->user_id || $user->hasRole('admin');
  }

  /**
   * User can create their own accounts; admin can also create.
   */
  public function create(User $user): bool
  {
    return $user->hasRole('user') || $user->hasRole('admin');
  }

  /**
   * User can update only their own account; admin can update all.
   */
  public function update(User $user, Account $account): bool
  {
    return $user->id === $account->user_id || $user->hasRole('admin');
  }

  /**
   * User can delete only their own account; admin can delete all.
   */
  public function delete(User $user, Account $account): bool
  {
    return $user->id == $account->user_id || $user->hasRole('admin');
  }

  /**
   * Only admin can restore accounts.
   */
  public function restore(User $user, Account $account): bool
  {
    return $user->hasRole('admin');
  }

  /**
   * Only admin can force delete accounts.
   */
  public function forceDelete(User $user, Account $account): bool
  {
    return $user->hasRole('admin');
  }
}
