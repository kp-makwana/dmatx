<div class="nav-align-top">
  <ul class="nav nav-pills flex-column flex-md-row mb-6 row-gap-2 flex-wrap">
    <li class="nav-item">
      <a class="nav-link active" href="{{ route('accounts.show',request('account')) }}">
        <i class="fa-light fa-wallet"></i>
        <i class="icon-base ti tabler-wallet icon-sm me-1_5"></i>PortFolio</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('app/ecommerce/customer/details/security') }}"><i
          class="icon-base ti tabler-lock icon-sm me-1_5"></i>Security</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('app/ecommerce/customer/details/billing') }}"><i
          class="icon-base ti tabler-map-pin icon-sm me-1_5"></i>Address & Billing</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('app/ecommerce/customer/details/notifications') }}"><i
          class="icon-base ti tabler-bell icon-sm me-1_5"></i>Notifications</a>
    </li>
  </ul>
</div>
