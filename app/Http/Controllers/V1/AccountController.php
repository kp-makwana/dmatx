<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\StoreRequest;
use App\Http\Requests\V1\Accounts\UpdateRequest;
use App\Models\V1\Account;
use App\Services\AccountService;
use App\Services\InstrumentsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountController extends Controller
{

  protected AccountService $service;
  protected InstrumentsService $instrumentsService;

  public function __construct(AccountService $service,InstrumentsService $instrumentsService)
  {
    $this->service = $service;
    $this->instrumentsService = $instrumentsService;
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

  public function positions(Account $account)
  {
    $this->authorize('view',$account);
    $response = $this->service->getPosition($account);
    $positions = [];
    if ($response['success']){
      $positions = $response['data'] ?? (object)[];
    }
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('accounts.positions',compact('account','pageConfigs','positions'));
  }

  public function balance(Account $account)
  {
    $this->authorize('view',$account);
    $pageConfigs = ['myLayout' => 'horizontal'];
    $response = $this->service->refresh($account);
    if ($response['success']){
      $data = $response['data'];
      $balance = [
        'available'   => (float) $data['availablecash'],
        'utilised'    => (float) $data['utiliseddebits'],
        'opening'     => (float) $data['net'],
        'payin'       => (float) $data['availableintradaypayin'],
        'payout'      => (float) $data['utilisedpayout'],
        'collateral'  => (float) $data['collateral'],
        'limit'       => (float) $data['availablelimitmargin'],
        'm2m_r'       => (float) $data['m2mrealized'],
        'm2m_u'       => (float) $data['m2munrealized'],
        'updated_at'  => Carbon::now()->format('d M Y, h:i A'),
      ];
    } else {
      $balance = [
        'available' => 0.00,
        'utilised'  => 0.00,
        'opening'   => 0.00,
        'payin'     => 0.00,
        'payout'    => 0.00,
        'collateral'=> 0.00,
        'limit'     => 0.00,
        'm2m_r'     => 0.00,
        'm2m_u'     => 0.00,
        'updated_at'=> $account->updated_at,
      ];
    }

    return view('accounts.balance',compact('account','pageConfigs','balance'));
  }

  public function market(Request $request,Account $account)
  {
    $this->authorize('view',$account);
    $instruments = $this->instrumentsService->index($request,$account);
    return view('accounts.market',[...$instruments]);
  }
}
