<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\StoreRequest;
use App\Http\Requests\V1\Accounts\UpdateRequest;
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
    $this->service->create($request->validated());
    return redirect()
      ->route('accounts.index')
      ->with('success', 'Account added successfully');
  }

  public function show(Account $account)
  {
    $this->authorize('view',$account);
    $pageConfigs = ['myLayout' => 'horizontal'];
    $status = $account->status;
    if ($status == Account::STATUS_SIGNUP_FORM_SUBMITTED){
      return redirect()->route('angle-one.create.step.two', $account->id);
    } else if ($status == Account::STATUS_SIGNUP_SUCCESS){
      return redirect()->route('angle-one.create.step.three', $account->id);
    } else if ($status == Account::STATUS_TOTP_ENABLE){
      return redirect()->route('angle-one.create.step.five', $account->id);
    }
    if ($status != Account::STATUS_ACTIVE){
      return redirect()->route('accounts.index')->with('error','Account setup not properly. Delete account and try again');
    }
    $previousUrl = url()->previous();
    $previousRouteName = app('router')
      ->getRoutes()
      ->match(app('request')->create($previousUrl))
      ->getName();
    if ($previousRouteName == 'angle-one.submit.step.five'){
      $this->service->refresh($account);
    }
    $result = $this->service->getHoldings($account);
    if (!isset($result['data'])){
      return redirect()->route('accounts.index')->with('error','Rate limit exceeded');
    }
    $data =  $result['data'];
    $summary = $data['totalholding'] ?? [];
    $holdings = $data['holdings'] ?? [];
    $tokens = collect($holdings)->pluck('symboltoken')
      ->filter()->unique()->values()->toArray();
    return view('accounts.show', compact('account','pageConfigs','summary','holdings','tokens'));
  }

  public function edit(Account $account)
  {
    $this->authorize('view',$account);
    if ($account->status != Account::STATUS_ACTIVE){
      return redirect()->route('accounts.index')->with('error','Account setup not properly. Delete account and try again');
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('accounts.edit',compact('account','pageConfigs'));
  }

  public function update(UpdateRequest $request,Account $account)
  {
    $this->authorize('update', $account);

    $response = $this->service->accountUpdate(
      $request->validated(),
      $account
    );

    return back()->with(
      $response['success'] ? 'success' : 'error',
      $response['message']
    );
  }

  public function destroy(Account $account)
  {
    $this->authorize('delete', $account);
    $this->service->destroy($account);
    return redirect()
      ->route('accounts.index')
      ->with('success', 'Account deleted successfully');
  }

  public function refresh(Account $account)
  {
    $this->authorize('update', $account);
    $this->service->refresh($account);
    return redirect()->back()->with('success', 'Account refreshed successfully');
  }

  public function orders(Account $account)
  {
    $this->authorize('view',$account);
    $pageConfigs = ['myLayout' => 'horizontal'];
    $response = $this->service->getOrderBook($account);
    $orders = [];
    $tokens = [];
    if ($response['success']){
      $flashType = 'success';
      $orders = $response['data']['data'] ?? [];
      $tokens = $response['data']['tokens'] ?? [];
      $message = 'Order fetched successfully';
    } else {
      $flashType = 'error';
      $message = $response['message'];
    }
    return view('accounts.orders',compact('account','pageConfigs','orders','tokens'))
      ->with($flashType,$message);
  }

  public function cancelOrder(Account $account,$order)
  {
    $response = $this->service->cancelOrder($account,$order);
    $message = $response['message'];
    if ($response['success']){
      $flashType = 'success';
    } else {
      $flashType = 'error';
    }
    return redirect()->back()->with($flashType,$message);
  }

  public function modifyOrder(Request $request, Account $account)
  {
    $payload = $request->all();
    $response = $this->service->modifyOrder($account,$payload);
    $message = $response['message'];
    if ($response['success']){
      $flashType = 'success';
    } else {
      $flashType = 'error';
    }
    return redirect()->back()->with($flashType,$message);
  }

  public function placeOrder(Request $request, Account $account)
  {
    $payload = $request->all();
    $response = $this->service->placeOrder($account,$payload);
    $message = $response['message'];
    if ($response['success']){
      $flashType = 'success';
    } else {
      $flashType = 'error';
    }
    return redirect()->back()->with($flashType,$message);
  }
}
