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
    /* =====================================================
       CONFIG
    ===================================================== */
    const clientCode = @json($account->client_id);
    const feedToken  = @json($account->feed_token);
    const apiKey     = @json($account->api_key);
    const tokens     = @json($tokens);

    /* =====================================================
       STATE
    ===================================================== */
    const ltpCache     = {}; // token => ltp
    const circuitCache = {}; // token => { upper, lower }
    let activeModifyToken = null;

    /* =====================================================
       SAFE READ HELPERS
    ===================================================== */
    function canRead(view, offset, size) {
      return view.byteLength >= offset + size;
    }

    function readInt32(view, offset) {
      return canRead(view, offset, 4)
        ? view.getInt32(offset, true) / 100
        : null;
    }

    /* fallback ±10% */
    function fallbackCircuit(prevClose) {
      if (!prevClose) return null;
      return {
        upper: +(prevClose * 1.10).toFixed(2),
        lower: +(prevClose * 0.90).toFixed(2)
      };
    }

    /* =====================================================
       SOCKET INIT
    ===================================================== */
    document.addEventListener('DOMContentLoaded', () => {

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

        if (event.data.byteLength < 60) return;
        if (view.getInt8(0) !== 2) return;

        /* TOKEN */
        let token = '';
        for (let i = 2; i < 27 && i < bytes.length; i++) {
          if (bytes[i] === 0) break;
          token += String.fromCharCode(bytes[i]);
        }
        token = token.trim();
        if (!token) return;

        /* LTP */
        const ltp = readInt32(view, 43);
        if (!ltp) return;

        ltpCache[token] = ltp;

        /* PREV CLOSE */
        const close = readInt32(view, 115);

        /* CIRCUIT */
        const upper = readInt32(view, 123);
        const lower = readInt32(view, 127);

        if (upper && lower) {
          circuitCache[token] = { upper, lower };
        } else if (close) {
          const fb = fallbackCircuit(close);
          if (fb) circuitCache[token] = fb;
        }

        /* LIVE UPDATE INSIDE MODIFY MODAL */
        if (token === activeModifyToken) {

          const ltpEl = document.getElementById('mo-ltp');
          if (ltpEl) ltpEl.innerText = ltp.toFixed(2);

          if (circuitCache[token]) {
            const limits = circuitCache[token];
            document.getElementById('mo-circuit').innerText =
              `₹${limits.lower} - ₹${limits.upper}`;
          }
        }
      };

      socket.onerror = err => console.error('Socket error', err);
      socket.onclose = () => console.warn('Socket closed');
    });

    /* =====================================================
       OPEN MODIFY ORDER MODAL
    ===================================================== */
    function openModifyOrder(btn) {
      const order = JSON.parse(btn.dataset.order);
      activeModifyToken = order.symboltoken;

      /* HIDDEN */
      document.getElementById('mo-order-id').value = order.orderid;
      document.getElementById('mo-symbol-token').value = order.symboltoken;
      document.getElementById('mo-variety').value = order.variety;
      document.getElementById('mo-tradingsymbol').value = order.tradingsymbol;

      /* HEADER */
      document.getElementById('mo-symbol-title').innerText = order.tradingsymbol;

      const sideBadge = document.getElementById('mo-side-badge');
      sideBadge.innerText = order.transactiontype;
      sideBadge.className =
        'badge ' + (order.transactiontype === 'BUY'
          ? 'bg-success'
          : 'bg-danger');

      document.getElementById('mo-info-ordertype').innerText = order.ordertype;

      /* PRICE CARDS */
      document.getElementById('mo-order-price').innerText =
        order.ordertype === 'MARKET'
          ? 'AT MARKET'
          : `₹${Number(order.price).toFixed(2)}`;

      document.getElementById('mo-qty-card').innerText = order.quantity;

      /* FORM */
      document.getElementById('mo-ordertype').value = order.ordertype;
      document.getElementById('mo-qty').value = order.quantity;

      const priceInput = document.getElementById('mo-price');

      if (order.ordertype === 'MARKET') {
        priceInput.value = '';
        priceInput.disabled = true;
        priceInput.placeholder = 'AT MARKET';
      } else {
        priceInput.disabled = false;
        priceInput.placeholder = 'Enter price';
        priceInput.value = order.price;
      }

      /* LTP */
      document.getElementById('mo-ltp').innerText =
        ltpCache[order.symboltoken]
          ? ltpCache[order.symboltoken].toFixed(2)
          : '—';

      /* CIRCUIT */
      document.getElementById('mo-circuit').innerText =
        circuitCache[order.symboltoken]
          ? `₹${circuitCache[order.symboltoken].lower} - ₹${circuitCache[order.symboltoken].upper}`
          : '—';

      /* RESET SUBMIT BUTTON */
      const btnSubmit = document.getElementById('modifySubmitBtn');
      btnSubmit.disabled = false;
      btnSubmit.querySelector('.btn-text').classList.remove('d-none');
      btnSubmit.querySelector('.spinner-border').classList.add('d-none');
      btnSubmit.querySelector('.btn-loading-text').classList.add('d-none');

      new bootstrap.Modal(document.getElementById('editOrderModal')).show();
    }

    /* =====================================================
       ORDER TYPE CHANGE
    ===================================================== */
    document.addEventListener('change', (e) => {
      if (e.target.id !== 'mo-ordertype') return;

      const priceInput = document.getElementById('mo-price');

      if (e.target.value === 'MARKET') {
        priceInput.value = '';
        priceInput.disabled = true;
        priceInput.placeholder = 'AT MARKET';
      } else {
        priceInput.disabled = false;
        priceInput.placeholder = 'Enter price';

        if (activeModifyToken && ltpCache[activeModifyToken]) {
          priceInput.value = ltpCache[activeModifyToken].toFixed(2);
        }
      }
    });

    /* =====================================================
       SUBMIT SPINNER
    ===================================================== */
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('editOrderForm');
      if (!form) return;

      form.addEventListener('submit', () => {
        const btn = document.getElementById('modifySubmitBtn');
        if (!btn) return;

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.btn-loading-text').classList.remove('d-none');
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
                            <div class="d-inline-flex align-items-center gap-1">

                              <!-- VIEW (ALL ORDERS) -->
                              <button
                                type="button"
                                class="btn btn-sm btn-icon btn-outline-primary"
                                data-bs-toggle="tooltip"
                                title="View Order"
                                data-order='@json($order)'
                                onclick="openViewOrder(this)">
                                <i class="ti tabler-eye"></i>
                              </button>

                              @if($key == 'Pending')
                                <!-- MODIFY -->
                                <button
                                  type="button"
                                  class="btn btn-sm btn-icon btn-outline-warning"
                                  data-bs-toggle="tooltip"
                                  title="Modify Order"
                                  data-order='@json($order)'
                                  onclick="openModifyOrder(this)">
                                  <i class="ti tabler-edit"></i>
                                </button>

                                <!-- CANCEL -->
                                <button
                                  type="button"
                                  class="btn btn-sm btn-icon btn-outline-danger"
                                  data-bs-toggle="tooltip"
                                  title="Cancel Order"
                                  onclick="cancelOrder('{{ route('account.cancel.order', [
          'account' => request('account')->id,
          'order' => $order['orderid']
        ]) }}')">
                                  <i class="ti tabler-x"></i>
                                </button>
                              @endif

                            </div>
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header border-0 pb-0">
          <div>
            <h4 class="mb-1">
              <span id="mo-symbol-title" class="fw-bold"></span>
              <span class="badge rounded-pill bg-label-primary ms-2"
                    style="font-size:0.65rem;font-weight:500;">
              NSE
            </span>
            </h4>

            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span id="mo-side-badge" class="badge"></span>

              <span class="badge bg-label-primary" id="mo-info-ordertype">
              LIMIT
            </span>

              <span class="badge bg-label-success">
              DELIVERY
            </span>

              <span class="badge bg-label-secondary">
              DAY
            </span>
            </div>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body pt-4">

          <!-- PRICE CARDS -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <small class="text-muted">Live Price</small>
                <div class="fw-semibold fs-5">
                  ₹ <span id="mo-ltp">—</span>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <small class="text-muted">Order Price</small>
                <div class="fw-semibold fs-5" id="mo-order-price">—</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <small class="text-muted">Quantity</small>
                <div class="fw-semibold fs-5" id="mo-qty-card">—</div>
              </div>
            </div>
          </div>

          <!-- FORM -->
          <form id="editOrderForm"
                method="POST"
                action="{{ route('account.modify.order',request('account')->id) }}"
                class="row g-3">

            @csrf

            <!-- REQUIRED HIDDEN -->
            <input type="hidden" name="orderid" id="mo-order-id">
            <input type="hidden" name="symboltoken" id="mo-symbol-token">
            <input type="hidden" name="variety" id="mo-variety">
            <input type="hidden" name="tradingsymbol" id="mo-tradingsymbol">

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
              <input type="number"
                     name="quantity"
                     class="form-control"
                     id="mo-qty"
                     required>
            </div>

            <!-- PRICE -->
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number"
                     step="0.05"
                     name="price"
                     class="form-control"
                     id="mo-price">
            </div>

            <!-- CIRCUIT -->
            <div class="col-md-6">
              <label class="form-label">Circuit</label>
              <div class="alert alert-info d-flex justify-content-between align-items-center py-2 mb-0"
                   style="height:38px">
                <span class="small fw-semibold">Range</span>
                <span class="small fw-semibold" id="mo-circuit">—</span>
              </div>
            </div>

            <!-- ACTIONS -->
            <div class="col-12 text-center mt-4">
              <button type="submit"
                      class="btn btn-primary me-2 d-inline-flex align-items-center justify-content-center"
                      id="modifySubmitBtn">
                <span class="btn-text">Modify Order</span>
                <span class="spinner-border spinner-border-sm d-none ms-2"></span>
                <span class="btn-loading-text d-none ms-2">Modifying...</span>
              </button>

              <button type="button"
                      class="btn btn-label-secondary"
                      data-bs-dismiss="modal">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header border-0 pb-0">
          <div>
            <h4 class="mb-1">
              <span id="vo-symbol" class="fw-bold"></span>
              <span
                id="vo-exchange"
                class="badge rounded-pill bg-label-primary ms-2"
                style="font-size: 0.65rem; font-weight: 500;">
              </span>
            </h4>

            <div class="d-flex align-items-center gap-2">
              <span id="vo-side" class="badge"></span>
              <span id="vo-status" class="badge"></span>
            </div>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body pt-4">

          <!-- PRICE CARDS -->
          <div class="row g-3 mb-4">

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <div class="text-muted small">Order Price</div>
                <div class="fw-semibold fs-5" id="vo-price">—</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <div class="text-muted small">Executed Price</div>
                <div class="fw-semibold fs-5" id="vo-executed-price">—</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <div class="text-muted small">Quantity</div>
                <div class="fw-semibold fs-5">
                  <span id="vo-filled"></span> /
                  <span id="vo-qty"></span>
                </div>
              </div>
            </div>

          </div>

          <!-- DETAILS GRID -->
          <div class="row g-3">

            <div class="col-md-6">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Order ID</span>
                <span class="fw-semibold" id="vo-order-id"></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Order Type</span>
                <span class="fw-semibold" id="vo-ordertype"></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Product</span>
                <span class="fw-semibold" id="vo-product"></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Duration</span>
                <span class="fw-semibold" id="vo-duration"></span>
              </div>
            </div>

            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Time</span>
                <span class="fw-semibold" id="vo-time"></span>
              </div>
            </div>

          </div>

          <!-- REJECTION MESSAGE -->
          <div class="alert alert-danger mt-4 d-none" id="vo-reject-box">
            <strong>Rejected Reason:</strong>
            <div class="mt-1" id="vo-reject-text"></div>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer border-0">
          <button class="btn btn-label-secondary" data-bs-dismiss="modal">
            Close
          </button>
        </div>

      </div>
    </div>
  </div>
  <script>
    function openViewOrder(btn) {
      const o = JSON.parse(btn.dataset.order);

      // BASIC
      document.getElementById('vo-symbol').innerText = o.tradingsymbol;
      document.getElementById('vo-exchange').innerText = o.exchange;
      document.getElementById('vo-order-id').innerText = o.orderid;
      document.getElementById('vo-ordertype').innerText = o.ordertype;
      document.getElementById('vo-product').innerText = o.producttype;
      document.getElementById('vo-duration').innerText = o.duration;
      document.getElementById('vo-qty').innerText = o.quantity;
      document.getElementById('vo-filled').innerText = o.filledshares;
      document.getElementById('vo-time').innerText =
        o.exchtime || o.updatetime || '—';

      // PRICE
      document.getElementById('vo-price').innerText =
        o.ordertype === 'MARKET'
          ? 'AT MARKET'
          : `₹${Number(o.price).toFixed(2)}`;

      document.getElementById('vo-executed-price').innerText =
        o.averageprice && o.averageprice > 0
          ? `₹${Number(o.averageprice).toFixed(2)}`
          : '—';

      // SIDE BADGE
      const sideEl = document.getElementById('vo-side');
      sideEl.innerText = o.transactiontype;
      sideEl.className =
        'badge ' + (o.transactiontype === 'BUY'
          ? 'bg-success'
          : 'bg-danger');

      // STATUS BADGE
      const statusEl = document.getElementById('vo-status');
      const status = o.status.toLowerCase();

      statusEl.innerText = status.toUpperCase();
      statusEl.className =
        'badge ' +
        (status === 'complete'
          ? 'bg-success'
          : status === 'rejected'
            ? 'bg-danger'
            : 'bg-secondary');

      // REJECTION
      const rejectBox = document.getElementById('vo-reject-box');
      if (status === 'rejected' && o.text) {
        rejectBox.classList.remove('d-none');
        document.getElementById('vo-reject-text').innerText = o.text;
      } else {
        rejectBox.classList.add('d-none');
      }

      new bootstrap.Modal(
        document.getElementById('viewOrderModal')
      ).show();
    }

    document.addEventListener('DOMContentLoaded', () => {
      const modifyForm = document.getElementById('editOrderForm');
      if (!modifyForm) return;

      modifyForm.addEventListener('submit', () => {
        const btn = document.getElementById('modifySubmitBtn');
        if (!btn) return;

        const spinner = btn.querySelector('.spinner-border');
        const text = btn.querySelector('.btn-text');
        const loadingText = btn.querySelector('.btn-loading-text');

        // Disable button
        btn.disabled = true;

        // Hide original text
        text.classList.add('d-none');

        // Show spinner + loading text
        spinner.classList.remove('d-none');
        loadingText.classList.remove('d-none');
      });
    });
  </script>
@endsection

