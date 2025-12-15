<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\StoreRequest;
use App\Models\V1\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{

  protected AccountService $service;

  public function __construct(AccountService $service)
  {
    $this->service = $service;
  }
  public function index(Request $request)
  {
    $this->authorize('viewAny',Account::class);
    $response = $this->service->index($request);
    return view('accounts.index', $response);
  }

  public function create()
  {
    $this->authorize('create',Account::class);
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('accounts.create',compact('pageConfigs'));
  }

  public function store(StoreRequest $request)
  {
    $this->authorize('create', Account::class);
    try {
      $this->service->create($request->validated());
      return redirect()
        ->route('accounts.index')
        ->with('success', 'Account added successfully');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    }
  }

  public function show(Account $account)
  {
    $this->authorize('view',$account);
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('accounts.show', compact('account','pageConfigs'));
  }

  public function destroy(Account $account)
  {
    $this->authorize('delete', $account);
    $this->service->destroy($account);
    return redirect()
      ->route('accounts.index')
      ->with('success', 'Account deleted successfully');
  }
}
