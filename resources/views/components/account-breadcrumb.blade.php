<div class="nav-align-top">
  <ul class="nav nav-pills flex-column flex-md-row mb-6 row-gap-2 flex-wrap">
    <li class="nav-item">
      <a class="nav-link {{ \Illuminate\Support\Facades\Route::is('accounts.show')?'active':'' }}" href="{{ route('accounts.show',request('account')) }}">
        <i class="fa-light fa-wallet"></i>
        <i class="icon-base ti tabler-wallet icon-sm me-1_5"></i>PortFolio</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ \Illuminate\Support\Facades\Route::is('account.orders')?'active':'' }}" href="{{ route('account.orders',request('account')) }}">
        <i class="icon-base ti tabler-shopping-cart icon-sm me-1_5"></i> Orders
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ \Illuminate\Support\Facades\Route::is('account.positions')?'active':'' }}" href="{{ route('account.positions',request('account')) }}">
        <i class="icon-base ti tabler-chart-line icon-sm me-1_5"></i> Positions
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ \Illuminate\Support\Facades\Route::is('account.balance')?'active':'' }}" href="{{ route('account.balance',request('account')) }}">
        <i class="icon-base ti tabler-cash icon-sm me-1_5"></i> Balance
      </a>
    </li>
    <li class="nav-item">
      <a
        class="nav-link {{ \Illuminate\Support\Facades\Route::is('accounts.edit') ? 'active' : '' }}"
        href="{{ route('accounts.edit', request('account')) }}"
      >
        <i class="icon-base ti tabler-edit icon-sm me-1_5"></i>
        Account Settings
      </a>
    </li>
  </ul>
</div>
