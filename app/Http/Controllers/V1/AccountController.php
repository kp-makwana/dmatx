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
    $result = $this->service->getHoldings($account);
    if (!isset($result['data'])){
      return redirect()->route('accounts.index')->with('error','Rate limit exceeded');
    }
    $data =  $result['data'];
    $summary = $data['totalholding'] ?? [];
    $holdings = $data['holdings'] ?? [];
    return view('accounts.show', compact('account','pageConfigs','summary','holdings'));
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
      dd($request->all(),$account);
  }
}
