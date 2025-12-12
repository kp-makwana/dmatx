@extends('layouts/layoutMaster')

@section('title', 'Accounts')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('content')
  @php
    function sortUrl($column): string
    {
        $current = request('sort_by');
        $direction = request('sort_dir') === 'asc' ? 'desc' : 'asc';

        if ($current !== $column) $direction = 'asc';

        return request()->fullUrlWithQuery([
            'sort_by' => $column,
            'sort_dir' => $direction
        ]);
    }
  @endphp

  <div class="card">

    <!-- Header + Buttons -->
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
          <li class="breadcrumb-item">
            <a href="{{ route('accounts.index') }}">Accounts</a>
            <i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
          </li>
          <li class="breadcrumb-item active">List</li>
        </ol>
      </nav>

      <div class="d-flex gap-2">
        <!-- Add New -->
        <a href="{{ route('accounts.create') }}">
          <button class="btn btn-primary">
            <i class="ti tabler-plus icon-sm"></i> Add New Account
          </button>
        </a>
      </div>
    </div>

    <!-- Filter Row -->
    <form method="GET" action="">
      <div class="row px-3 py-2 border-bottom align-items-center">

        <!-- LEFT SIDE: Show Entries -->
        <div class="col-md-4 d-flex align-items-center gap-2">
          <label class="mb-0">Show</label>

          <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
          </select>

          <span class="mb-0">entries</span>
        </div>

        <!-- RIGHT SIDE: Status + Search -->
        <div class="col-md-8 d-flex justify-content-end gap-2">

          <!-- Status Filter -->
          <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>

          <!-- Search -->
          <div class="input-group input-group-sm" style="max-width:250px;">
            <input type="text" name="search" class="form-control"
                   placeholder="Search..." value="{{ request('search') }}">
            <button class="input-group-text"><i class="ti tabler-search"></i></button>
          </div>

          @if(request()->hasAny(['search', 'status', 'per_page']))
            <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">
              Clear
            </a>
          @endif

        </div>

      </div>
    </form>

    <!-- STATIC TABLE -->
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
        <tr>
          <th class="sortable">
            <a href="{{ sortUrl('nickname') }}" class="text-primary">
              Nickname
              @if($sortBy === 'nickname')
                <span class="sort-arrow {{ $sortDir }}"></span>
              @endif
            </a>
          </th>

          <th class="sortable text-primary">
            <a href="{{ sortUrl('client_id') }}">
              Client ID
              @if($sortBy === 'client_id')
                <span class="sort-arrow {{ $sortDir }}"></span>
              @endif
            </a>
          </th>

          <th class="sortable text-primary"><span>Status</span></th>
          <th class="sortable text-primary"><span>Token Expiry</span></th>
          <th class="sortable text-primary">
            <a href="{{ sortUrl('last_login_at') }}">
              Last Login
              @if($sortBy === 'last_login_at')
                <span class="sort-arrow {{ $sortDir }}"></span>
              @endif
            </a>
          </th>

          <th class="text-primary" style="width:80px;">Action</th>
        </tr>
        </thead>


        <tbody>
        @forelse($accounts as $account)
          <tr>
            <td>
              <div class="d-flex align-items-center">

                <!-- Avatar -->
                <div class="avatar me-2">
                  @php
                    $avatar = strtoupper(substr($account['nickname'] ?: $account['client_id'], 0, 2));
                  @endphp

                  <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ $avatar }}
                    </span>
                </div>

                <div>
                  <span class="fw-medium">{{ $account['nickname'] ?: $account['client_id'] }}</span><br>
                  <small class="text-muted">{{ $account['account_name'] ?: 'N/A' }}</small>
                </div>
              </div>
            </td>
            <td>{{ $account['client_id'] }}</td>
            <td>
              <span class="badge bg-label-primary">
                {{ ucfirst($account['status']) }}
            </span>
            </td>
            <td>{!! $account['token_expiry'] !!}</td>
            <td>{{ $account['last_login_at'] ?? '-' }}</td>
            <td class="justify-content-end">
              <div class="dropdown">
                <button class="btn btn-icon btn-text-secondary rounded-pill" data-bs-toggle="dropdown">
                  <i class="ti tabler-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="#">Details</a></li>
                  <li><a class="dropdown-item" href="#">Edit</a></li>
                  <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">
              No Accounts Found.
            </td>
          </tr>
        @endforelse
        </tbody>

      </table>
    </div>

    @if ($accounts->hasPages())
      <div class="card-footer d-flex justify-content-end">
        {{ $accounts->links('vendor.pagination.rounded') }}
      </div>
    @endif
  </div>

  <!-- DataTable with Buttons -->
  <style>
    /* Make <th> behave like a flex row */
    th.sortable a {
      display: flex;
      align-items: center;
      justify-content: space-between; /* push icon to far right */
      width: 100%;
      color: inherit;
      text-decoration: none;
      font-weight: 600;
      padding-right: 4px;
    }

    /* Sort arrow base style */
    .sort-arrow {
      border: solid #333;
      border-width: 0 2px 2px 0;
      display: inline-block;
      padding: 3px;
    }

    /* Up arrow (ASC) */
    .sort-arrow.asc {
      transform: rotate(-135deg);
    }

    /* Down arrow (DESC) */
    .sort-arrow.desc {
      transform: rotate(45deg);
    }

  </style>
@endsection
