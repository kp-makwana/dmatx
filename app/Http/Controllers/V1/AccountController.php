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
    $this->authorize('create',Account::class);
    $validatedData = $request->validated();
    $this->service->create($validatedData);
    return redirect()->route('accounts.index')->with('success', "Account added successfully.Take few second for angle login");
  }
}
