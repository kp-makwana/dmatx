<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class AngelLoginException extends Exception
{
  public function __construct(
    string     $message,
    public int $accountId
  )
  {
    parent::__construct($message);
  }

  public function render($request): RedirectResponse
  {
    return redirect()
      ->route('accounts.edit', $this->accountId)
      ->with('error', $this->getMessage());
  }
}

