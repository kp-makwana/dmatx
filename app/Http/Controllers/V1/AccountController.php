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
  public function index(Request $request)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];

    $query = Account::query();

    // USER FILTER
    if (!auth()->user()->hasRole('admin')) {
      $query->where('user_id', auth()->id());
    }

    // GLOBAL SEARCH
    if ($search = $request->input('search')) {
      $query->where(function ($q) use ($search) {
        $q->where('account_name', 'like', "%{$search}%")
          ->orWhere('client_id', 'like', "%{$search}%")
          ->orWhere('status', 'like', "%{$search}%");
      });
    }

    // STATUS FILTER
    if ($status = $request->input('status')) {
      $query->where('status', $status);
    }

    // SORTING
    $sortable = ['nickname', 'client_id', 'last_login_at'];

    $sortBy  = $request->input('sort_by');
    $sortDir = $request->input('sort_dir');

    // validate column
    if (!in_array($sortBy, $sortable)) {
      $sortBy = null; // ignore sorting
    }

    // validate direction
    if (!in_array($sortDir, ['asc', 'desc'])) {
      $sortDir = 'asc'; // default
    }

    // apply sorting only if valid
    if ($sortBy) {
      $query->orderBy($sortBy, $sortDir);
    }

    // PAGINATION
    $perPage = $request->input('per_page', 10);
    $accounts = $query->paginate($perPage)->appends($request->query());

    // MAP RESULTS
    $mapped = ListResource::collection($accounts->items())->resolve();

    $accounts->setCollection(collect($mapped));

    return view('accounts.index', compact('pageConfigs', 'accounts', 'sortBy', 'sortDir'));
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
