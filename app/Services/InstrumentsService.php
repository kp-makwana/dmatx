<?php

namespace App\Services;

use App\Http\Resources\V1\Instrument\ListResource;
use App\Models\V1\Instrument;

class InstrumentsService
{

  public function index($request, $account)
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    $query = Instrument::query();
    // GLOBAL SEARCH
    if ($search = $request->input('search')) {
      $query->where(function ($q) use ($search) {
        $q->where('token', 'like', "%{$search}%")
          ->orWhere('symbol', 'like', "%{$search}%")
          ->orWhere('name', 'like', "%{$search}%");
      });
    }
    // SORTING
    $sortable = ['token', 'symbol', 'name'];

    $sortBy = $request->input('sort_by');
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
    $instruments = $query->paginate($perPage)->appends($request->query());

    // MAP RESULTS
    $mapped = ListResource::collection($instruments->items())->resolve();

    $instruments = $instruments->setCollection(collect($mapped));
    return ['pageConfigs' => $pageConfigs,
      'account' => $account,
      'instruments' => $instruments,
      'sortBy' => $sortBy,
      'sortDir' => $sortDir];
  }
}
