<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Accounts\StoreRequest;
use App\Http\Resources\V1\Account\ListResource;
use App\Models\V1\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

  protected AccountService $service;

  public function __construct(AccountService $service)
  {
    $this->service = $service;
  }
  public function index()
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('accounts.index',compact('pageConfigs'));
  }

  public function list(Request $request)
  {
    $query = Account::query();

    if (!auth()->user()->hasRole('admin')) {
      $query->where('user_id', auth()->id());
    }

    // GLOBAL SEARCH
    if ($search = $request->input('search.value')) {
      $query->where(function ($q) use ($search) {
        $q->where('account_name', 'like', "%{$search}%")
          ->orWhere('client_id', 'like', "%{$search}%")
          ->orWhere('status', 'like', "%{$search}%");
      });
    }

    // 🔹 COLUMN FILTER: STATUS (hidden column index 3 in JS)
    $statusFilter = $request->input('columns.3.search.value');
    if (!empty($statusFilter)) {
      $query->where('status', $statusFilter);
    }

    $recordsTotal = $query->count();

    $accounts = $query
      ->skip($request->input('start', 0))
      ->take($request->input('length', 10))
      ->get();

    return response()->json([
      "draw" => intval($request->input('draw')),
      "recordsTotal" => $recordsTotal,
      "recordsFiltered" => $recordsTotal,
      "data" => ListResource::collection($accounts),
    ]);
  }

  public function store(StoreRequest $request)
  {
    $this->authorize('create',Account::class);
    $validatedData = $request->validated();
    $exists = Account::where('user_id',Auth::id())->where('client_id',$validatedData['client_id'])->exists();
    if ($exists){
      return redirect()->back()->with('error', "Account already added");
    }
    $this->service->create($validatedData);

    return redirect()->back()->with('success', "Account added successfully,\n Take few second for angle login");
  }
}
