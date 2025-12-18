@extends('layouts/layoutMaster')

@section('title', 'eCommerce Customer Details Overview - Apps')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/modal-edit-user.js',
    'resources/assets/js/app-ecommerce-customer-detail.js',
    'resources/assets/js/app-ecommerce-customer-detail-overview.js'
  ])
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const deleteBtn = document.getElementById('btn-delete-account');
      if (!deleteBtn) return;

      deleteBtn.addEventListener('click', function() {
        const form = this.closest('form');

        Swal.fire({
          title: 'Are you sure?',
          text: `Are you sure to delete this account?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel',
          customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });

    function cancelOrder(url) {
      Swal.fire({
        title: 'Cancel Order?',
        text: 'Are you sure you want to cancel this order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel',
        cancelButtonText: 'No',
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.getElementById('cancelOrderForm');
          form.action = url;
          form.submit();
        }
      });
    }
  </script>

  <script>
    const clientCode = @json($account->client_id);
    const feedToken  = @json($account->feed_token);
    const apiKey     = @json($account->api_key);
    const tokens     = @json($tokens);

    const ltpCellMap = {};
    const ltpCache   = {};
    let activeModifyToken = null;

    document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.ltp-cell').forEach(td => {
        const token = td.dataset.token;
        if (!token) return;
        if (!ltpCellMap[token]) ltpCellMap[token] = [];
        ltpCellMap[token].push(td);
      });

      const socket = new WebSocket(
        `wss://smartapisocket.angelone.in/smart-stream` +
        `?clientCode=${encodeURIComponent(clientCode)}` +
        `&feedToken=${encodeURIComponent(feedToken)}` +
        `&apiKey=${encodeURIComponent(apiKey)}`
      );

      socket.binaryType = 'arraybuffer';

      socket.onopen = () => {
        socket.send(JSON.stringify({
          correlationID: clientCode,
          action: 1,
          params: {
            mode: 2,
            tokenList: [{
              exchangeType: 1,
              tokens: tokens
            }]
          }
        }));
      };

      socket.onmessage = (event) => {
        if (!(event.data instanceof ArrayBuffer)) return;

        const view  = new DataView(event.data);
        const bytes = new Uint8Array(event.data);

        if (view.getInt8(0) !== 2) return;

        let token = '';
        for (let i = 2; i < 27; i++) {
          if (bytes[i] === 0) break;
          token += String.fromCharCode(bytes[i]);
        }

        const ltp   = view.getInt32(43, true) / 100;
        const close = Number(view.getBigInt64(115, true)) / 100;
        if (!ltp || !close) return;

        ltpCache[token] = ltp;

        const diff = ltp - close;
        const pct  = (diff / close) * 100;
        const cls  = diff >= 0 ? 'text-success' : 'text-danger';
        const sign = diff >= 0 ? '+' : '';

        (ltpCellMap[token] || []).forEach(td => {

          const orderType  = td.dataset.orderType;
          const orderPrice = parseFloat(td.dataset.orderPrice);

          td.querySelector('.order-price').innerHTML =
            orderType === 'market'
              ? 'AT MARKET'
              : `₹${orderPrice.toFixed(2)}`;

          td.querySelector('.ltp-live').innerHTML = `
        LTP ₹${ltp.toFixed(2)}
        <span class="${cls}">
          (${sign}${pct.toFixed(2)}%)
        </span>
      `;
        });

        if (token === activeModifyToken) {
          const ltpSpan = document.getElementById('mo-ltp');
          if (ltpSpan) {
            ltpSpan.innerText = ltp.toFixed(2);
          }
        }
      };

      socket.onerror = err => console.error('❌ Socket Error', err);
      socket.onclose = () => console.warn('⚠️ Socket Closed');
    });

    function openModifyOrder(btn) {
      const order = JSON.parse(btn.dataset.order);
      activeModifyToken = order.symboltoken;
      document.getElementById('mo-order-id').value     = order.orderid;
      document.getElementById('mo-symbol-token').value = order.symboltoken;
      document.getElementById('mo-variety').value      = order.variety;
      document.getElementById('mo-symbol').value       = order.tradingsymbol;
      document.getElementById('mo-side').value         = order.transactiontype;
      document.getElementById('mo-ordertype').value    = order.ordertype;
      document.getElementById('mo-qty').value          = order.quantity;
      const priceInput = document.getElementById('mo-price');

      if (order.ordertype === 'MARKET') {
        priceInput.value = '';
        priceInput.disabled = true;
        priceInput.placeholder = 'AT MARKET';
      } else {
        priceInput.disabled = false;
        priceInput.value = order.price;
      }

      const ltpSpan = document.getElementById('mo-ltp');
      if (ltpCache[order.symboltoken]) {
        ltpSpan.innerText = ltpCache[order.symboltoken].toFixed(2);
      } else {
        ltpSpan.innerText = '—';
      }
      new bootstrap.Modal(document.getElementById('editOrderModal')).show();
    }

    function handleOrderTypeChange(type) {

      const priceInput = document.getElementById('mo-price');

      if (type === 'MARKET') {
        // MARKET → disable price
        priceInput.value = '';
        priceInput.disabled = true;
        priceInput.placeholder = 'AT MARKET';
        return;
      }

      // LIMIT → enable price
      priceInput.disabled = false;
      priceInput.placeholder = 'Enter limit price';

      // ✅ AUTO-FILL FROM LIVE PRICE (ANGEL ONE STYLE)
      if (
        activeModifyToken &&
        ltpCache[activeModifyToken]
      ) {
        priceInput.value = ltpCache[activeModifyToken].toFixed(2);
      } else {
        priceInput.value = '';
      }
    }

    // Attach listener
    document.addEventListener('DOMContentLoaded', () => {
      const orderTypeSelect = document.getElementById('mo-ordertype');
      if (!orderTypeSelect) return;

      orderTypeSelect.addEventListener('change', (e) => {
        handleOrderTypeChange(e.target.value);
      });
    });
  </script>
@endsection

@section('content')
  <div class="row">
    <!-- Customer-detail Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
      <!-- Customer-detail Card -->
      <div class="card mb-6">
        <a href="{{ route('account.refresh',request('account')) }}"
           type="button"
           class="btn btn-lg btn-icon btn-outline-secondary position-absolute top-0 end-0 m-3"
           title="Refresh"
        >
          <i class="ti tabler-refresh"></i>
        </a>
        <div class="card-body pt-12">
          <div class="customer-avatar-section">
            <div class="d-flex align-items-center flex-column">
              <img class="img-fluid rounded mb-4" src="{{ asset('assets/img/avatars/1.png') }}" height="120" width="120"
                   alt="User avatar" />
              <div class="customer-info text-center mb-6">
                <h5 class="mb-0">{{ $account->nickname }}</h5>
                <span>#{{ $account->client_id }}</span>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-around flex-wrap mb-6 gap-0 gap-md-3 gap-lg-4">
            <div class="d-flex align-items-center gap-4 me-5">
              <div class="avatar">
                <div class="avatar-initial rounded bg-label-success">
                  <i class="icon-base ti tabler-currency-rupee icon-lg"></i>
                </div>
              </div>
              <div>
                <h5 class="mb-0">{{ $account->net ?? '0' }}</h5>
                <span>Net Amount</span>
              </div>
            </div>
            <div class="d-flex align-items-center gap-4">
              <div class="avatar">
                <div class="avatar-initial rounded bg-label-danger">
                  <i class="icon-base ti tabler-currency-rupee icon-lg"></i>
                </div>
              </div>
              <div>
                <h5 class="mb-0">{{ $account->amount_used ?? '0' }}</h5>
                <span>Used Amount</span>
              </div>
            </div>
          </div>

          <div class="info-container">
            <hr>
            {{--            <h5 class="pb-4 border-bottom text-capitalize mt-6 mb-4">Details</h5>--}}
            <ul class="list-unstyled mb-6">
              <li class="mb-2">
                <span class="h6 me-1">Name:</span>
                <span>{{ $account->account_name }}</span>
              </li>
            </ul>
            <div class="d-flex justify-content-center gap-2">
              <a href="javascript:;" class="btn btn-primary w-50" data-bs-target="#editUser" data-bs-toggle="modal">Edit
                Details</a>
              <form action="{{ route('accounts.destroy', request('account')) }}"
                    method="POST"
                    class="w-50 delete-account-form">
                @csrf
                @method('DELETE')

                <button type="button" id="btn-delete-account"
                        class="btn btn-danger w-100 btn-delete-account"
                        data-name="{{ $account->nickname ?: $account->client_id }}">
                  Delete Customer
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- /Customer-detail Card -->
      <!-- Plan Card -->

    </div>
    <!--/ Customer Sidebar -->

    <!-- Customer Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
      <!-- Customer Pills -->
      @include('components.account-breadcrumb')
      <!--/ Customer Pills -->

      <!-- / Customer cards -->
      <!-- Invoice table -->
      <div class="col-md-12 col-xxl-12">
        <div class="card h-100">

          <!-- Header -->
          <div class="card-header d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-1">Today’s Orders</h5>
              {{--              <p class="text-muted mb-0">Equity orders (static demo)</p>--}}
            </div>

            <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
              <i class="icon-base ti tabler-refresh me-1"></i> Refresh
            </button>
          </div>

          <!-- Tabs -->
          <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill border-bottom" role="tablist">
              @foreach(array_keys($orders) as $k => $title)
                <li class="nav-item">
                  <button class="nav-link {{ $k==0?'active':'' }}" data-bs-toggle="tab"
                          data-bs-target="#{{ strtolower($title) }}">
                    {{ $title }}
                  </button>
                </li>
              @endforeach
            </ul>

            <div class="tab-content" style="padding: 0 !important;">
              <!-- ================= NEW ORDERS ================= -->
              @foreach($orders as $key => $orderList)

                <div class="card tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="{{ strtolower($key) }}">

                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                      <thead>
                      <tr>
                        <th>Symbol</th>
                        <th>Order Type</th>
                        <th>Order Price</th>
                        <th>Executed Price</th>
                        <th>Side</th>
                        <th>Executed Time</th>
                        <th class="text-end">Action</th>
                      </tr>
                      </thead>

                      <tbody>
                      @forelse($orderList as $order)

                        @php
                          // Side
                          $side = $order['transactiontype']; // BUY / SELL
                          $sideClass = $side === 'BUY' ? 'success' : 'danger';

                          // Order Type
                          $isMarket = $order['ordertype'] === 'MARKET';
                          $orderTypeBadge = $isMarket ? 'secondary' : 'info';

                          // Prices
                          $orderPrice = $isMarket
                              ? 'AT MARKET'
                              : '₹' . number_format($order['price'], 2);

                          $executedPrice = (float) $order['averageprice'];

                          // Time with seconds
                          $executedTime = $order['exchtime'] ?: $order['updatetime'];
                        @endphp

                        <tr class="order-row cursor-pointer" >

                          {{-- 1️⃣ Symbol + Share --}}
                          <td>
                            <div class="fw-semibold">{{ $order['tradingsymbol'] }}</div>
                            <div class="text-muted small">
                              {{ $order['filledshares'] }}/{{ $order['quantity'] }} Share
                            </div>
                          </td>

                          {{-- 2️⃣ Side --}}

                          {{-- 3️⃣ Order Type --}}
                          <td>
                            <span class="badge bg-label-gray">
                              {{ $order['ordertype'] }}
                            </span>
                          </td>

                          <td class="ltp-cell"
                              data-token="{{ (string) $order['symboltoken'] }}"
                              data-order-price="{{ (float) $order['price'] }}"
                              data-order-type="{{ strtolower($order['ordertype']) }}">

                            <div class="fw-semibold order-price">
                              @if(strtolower($order['ordertype']) === 'market')
                                AT MARKET
                              @else
                                ₹{{ $orderPrice }}
                              @endif
                            </div>

                            <div class="small ltp-live">
                              LTP …
                            </div>
                          </td>

                          {{-- 5️⃣ Executed Price --}}
                          <td>
                            <span class="fw-semibold">
                              ₹{{ number_format($executedPrice, 2) }}
                            </span>
                          </td>
                          <td>
                            <span class="small badge bg-label-{{ $sideClass }}">
                              {{ $side }}
                            </span>
                          </td>
                          {{-- 6️⃣ Executed Time --}}
                          <td>
                            <span class="text-muted small">
                              {{ \Carbon\Carbon::parse($executedTime)->format('d M, h:i:s A') }}
                            </span>
                          </td>

                          {{-- 7️⃣ Action --}}
                          <td class="text-end">
                            @if($key == 'Pending')
                              <div class="d-inline-flex gap-2">
                                <!-- Modify -->
                                <a href="javascript:void(0)"
                                   class="btn btn-sm btn-outline-warning"
                                   data-order='@json($order)'
                                   onclick="openModifyOrder(this)">
                                  <i class="ti tabler-edit me-1"></i> Modify
                                </a>

                                <!-- Cancel -->
                                <button
                                  type="button"
                                  class="btn btn-sm btn-outline-danger btn-cancel-order"
                                  onclick="cancelOrder('{{ route('account.cancel.order', ['account' => request('account')->id, 'order' => $order['orderid']]) }}')"
                                  data-order-id="{{ $order['orderid'] }}">
                                  <i class="ti tabler-x me-1"></i> Cancel
                                </button>
                              </div>
                            @else
                              <button class="btn btn-sm btn-outline-primary">
                                View
                              </button>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="7" class="text-center text-muted py-4">
                            No {{ strtolower($key) }} orders found
                          </td>
                        </tr>
                      @endforelse
                      </tbody>


                    </table>
                  </div>
                </div>
              @endforeach

            </div>
          </div>
        </div>
      </div>


      <!-- /Invoice table -->
    </div>
    <!--/ Customer Content -->
  </div>
  <form id="cancelOrderForm" method="POST" style="display:none;">
    @csrf
  </form>

  <!-- Modal -->
  @include('_partials/_modals/modal-edit-user')
  <!-- /Modal -->
  <div class="modal fade" id="editOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-simple">
      <div class="modal-content">
        <div class="modal-body">

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

          <h4 class="text-center mb-3">Modify Order</h4>

          <form id="editOrderForm"
                method="POST"
                action="{{ route('account.modify.order',request('account')->id) }}"
                class="row g-4">

            @csrf

            <input type="hidden" name="orderid" id="mo-order-id">
            <input type="hidden" name="symboltoken" id="mo-symbol-token">
            <input type="hidden" name="variety" id="mo-variety">

            <!-- SYMBOL -->
            <div class="col-md-6">
              <label class="form-label">Symbol</label>
              <input type="text" class="form-control" id="mo-symbol" disabled>
            </div>

            <!-- SIDE -->
            <div class="col-md-6">
              <label class="form-label">Side</label>
              <input type="text" class="form-control" id="mo-side" disabled>
            </div>

            <!-- ORDER TYPE -->
            <div class="col-md-6">
              <label class="form-label">Order Type</label>
              <select class="form-select" id="mo-ordertype" name="ordertype">
                <option value="MARKET">MARKET</option>
                <option value="LIMIT">LIMIT</option>
              </select>
            </div>

            <!-- QUANTITY -->
            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" id="mo-qty" required>
            </div>

            <!-- PRICE -->
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" step="0.05" name="price" class="form-control" id="mo-price">
            </div>

            <!-- LIVE LTP -->
            <div class="col-md-6">
              <label class="form-label">Live Price</label>
              <div class="form-control bg-light">
                LTP ₹<span id="mo-ltp">—</span>
              </div>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary me-2">
                Modify Order
              </button>
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

