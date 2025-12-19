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
    document.addEventListener('DOMContentLoaded', function () {
      const deleteBtn = document.getElementById('btn-delete-account');
      if (!deleteBtn) return;

      deleteBtn.addEventListener('click', function () {
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
    let activeToken    = null;

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

    /* fallback circuit: ±10% */
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

        /* ===============================
           CIRCUIT READ (SAFE)
        =============================== */
        const upper = readInt32(view, 123);
        const lower = readInt32(view, 127);

        if (upper && lower) {
          circuitCache[token] = { upper, lower };
        } else {
          document
            .querySelectorAll(`.ltp-cell[data-symboltoken="${token}"]`)
            .forEach(td => {
              const prev = parseFloat(td.dataset.prevclose);
              const fb = fallbackCircuit(prev);
              if (fb) circuitCache[token] = fb;
            });
        }

        /* ===============================
           TABLE UPDATE
        =============================== */
        document
          .querySelectorAll(`.ltp-cell[data-symboltoken="${token}"]`)
          .forEach(td => {

            const avg  = parseFloat(td.dataset.avgprice);
            const qty  = parseFloat(td.dataset.quantity);
            const prev = parseFloat(td.dataset.prevclose);

            /* LTP */
            const ltpEl = td.querySelector('.ltp-value');
            if (ltpEl) ltpEl.innerText = `₹${ltp.toFixed(2)}`;

            /* TODAY % */
            const todayEl = td.querySelector('.ltp-today-percent');
            if (todayEl && prev) {
              const pct = ((ltp - prev) / prev) * 100;
              todayEl.innerText = `${pct.toFixed(2)}%`;
              todayEl.className =
                `ltp-today-percent small ${pct >= 0 ? 'text-success' : 'text-danger'}`;
            }

            /* ROW P&L */
            const pnlTd = document.querySelector(
              `.pnl-cell[data-symboltoken="${token}"]`
            );

            if (pnlTd && avg && qty) {
              const pnl    = (ltp - avg) * qty;
              const pnlPct = ((ltp - avg) / avg) * 100;

              pnlTd.querySelector('.pnl-value').innerText =
                `₹${pnl.toFixed(2)}`;
              pnlTd.querySelector('.pnl-percent').innerText =
                `${pnlPct.toFixed(2)}%`;

              const cls = pnl >= 0 ? 'text-success' : 'text-danger';
              pnlTd.querySelector('.pnl-value').className =
                `pnl-value fw-semibold ${cls}`;
              pnlTd.querySelector('.pnl-percent').className =
                `pnl-percent small ${cls}`;
            }
          });

        /* ===============================
           MODAL LIVE LTP
        =============================== */
        if (activeToken && token === String(activeToken)) {
          const modalLtp = document.getElementById('om-ltp');
          if (modalLtp) modalLtp.innerText = ltp.toFixed(2);
        }
      };
    });

    /* =====================================================
       MODAL OPEN
    ===================================================== */
    function openOrderModal(order, side = 'BUY') {
      activeToken = order.symboltoken;

      document.getElementById('om-symbol-token').value  = order.symboltoken;
      document.getElementById('om-tradingsymbol').value = order.tradingsymbol;
      document.getElementById('om-transactiontype').value = side;

      document.getElementById('om-symbol').value = order.tradingsymbol;
      document.getElementById('om-side').value   = side;
      document.getElementById('om-qty').value =
        side === 'SELL' ? order.quantity : '';

      const priceInput = document.getElementById('om-price');
      const orderType  = document.getElementById('om-ordertype').value;
      const ltp        = ltpCache[order.symboltoken];

      /* MARKET */
      if (orderType === 'MARKET') {
        priceInput.disabled = true;
        priceInput.value = ltp ? ltp.toFixed(2) : '';
      } else {
        priceInput.disabled = false;
        priceInput.value = ltp ? ltp.toFixed(2) : '';
      }

      const limits = circuitCache[order.symboltoken];
      if (limits) {
        priceInput.title = `LC ₹${limits.lower} | UC ₹${limits.upper}`;
      }

      document.getElementById('om-ltp').innerText =
        ltp ? ltp.toFixed(2) : '—';

      new bootstrap.Modal(document.getElementById('orderModal')).show();
    }

    document.addEventListener('change', e => {
      if (e.target.id !== 'om-ordertype') return;

      const priceInput = document.getElementById('om-price');

      if (e.target.value === 'MARKET') {
        priceInput.value = '';
        priceInput.disabled = true;
      } else {
        priceInput.disabled = false;
        if (activeToken && ltpCache[activeToken]) {
          priceInput.value = ltpCache[activeToken].toFixed(2);
        }
      }
    });

    /* =====================================================
       VALIDATION HELPERS
    ===================================================== */
    function showError(id, msg) {
      const el = document.getElementById(id);
      const er = document.getElementById(`${id}-error`);
      if (!el || !er) return;
      el.classList.add('is-invalid');
      er.innerText = msg;
    }
    function clearError(id) {
      const el = document.getElementById(id);
      const er = document.getElementById(`${id}-error`);
      if (!el || !er) return;
      el.classList.remove('is-invalid');
      er.innerText = '';
    }

    document.addEventListener('input', e => {
      if (e.target.id === 'om-qty') clearError('om-qty');
      if (e.target.id === 'om-price') clearError('om-price');
    });

    /* =====================================================
       FORM SUBMIT VALIDATION
    ===================================================== */
    document
      .getElementById('orderForm')
      ?.addEventListener('submit', function (e) {

        let hasError = false;

        const qty   = parseInt(document.getElementById('om-qty').value, 10);
        const price = parseFloat(document.getElementById('om-price').value);
        const type  = document.getElementById('om-ordertype').value;

        clearError('om-qty');
        clearError('om-price');

        if (!qty || qty <= 0) {
          showError('om-qty', 'Quantity is required');
          hasError = true;
        }

        if (type === 'LIMIT') {
          if (!price || price <= 0) {
            showError('om-price', 'Price is required');
            hasError = true;
          }

          const limits = circuitCache[activeToken];
          if (limits) {
            if (price > limits.upper) {
              showError('om-price', `Above Upper Circuit ₹${limits.upper}`);
              hasError = true;
            }
            if (price < limits.lower) {
              showError('om-price', `Below Lower Circuit ₹${limits.lower}`);
              hasError = true;
            }
          }
        }
        if (hasError) e.preventDefault();
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
      <div class="card">
        <div class="card-header border-bottom">
          <div class="row align-items-center g-3">

            <!-- Holding Value -->
            <div class="col-md-2 col-6">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <div class="avatar-initial rounded bg-label-primary">
                    <i class="icon-base ti tabler-wallet"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted d-block">Current Value</small>
                  <span class="fw-semibold">&#8377;{{ $summary['totalholdingvalue'] ?? 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Investment -->
            <div class="col-md-2 col-6">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <div class="avatar-initial rounded bg-label-info">
                    <i class="icon-base ti tabler-currency-rupee"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted d-block">Investment</small>
                  <span class="fw-semibold">&#8377;{{ $summary['totalinvvalue'] ?? 0 }}</span>
                </div>
              </div>
            </div>

            <!-- P&L -->
            <div class="col-md-2 col-6">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <div class="avatar-initial rounded
            {{ ($summary['totalprofitandloss'] ?? 0) >= 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                    <i class="icon-base ti tabler-trending-up"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted d-block">P&amp;L</small>
                  <span class="fw-semibold
            {{ ($summary['totalprofitandloss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
            &#8377;{{ $summary['totalprofitandloss'] ?? 0 }}
          </span>
                </div>
              </div>
            </div>

            <!-- P&L % -->
            <div class="col-md-2 col-6">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <div class="avatar-initial rounded
            {{ ($summary['totalpnlpercentage'] ?? 0) >= 0 ? 'bg-label-success' : 'bg-label-danger' }}">
                    <i class="icon-base ti tabler-percentage"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted d-block">P&amp;L %</small>
                  <span class="fw-semibold
            {{ ($summary['totalpnlpercentage'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
            {{ $summary['totalpnlpercentage'] ?? 0 }}%
          </span>
                </div>
              </div>
            </div>

            <!-- Refresh -->
            <div class="col-md-4 text-md-end text-start">
              <button type="button"
                      class="btn btn-sm btn-outline-primary"
                      onclick="window.location.reload()">
                <i class="icon-base ti tabler-refresh me-1"></i> Refresh
              </button>
            </div>

          </div>
        </div>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle">
            <thead>
            <tr>
              <th>Symbol</th>
              <th>Quantity</th>
              <th>Avg Price</th>
              <th>LTP</th>
              <th>P&amp;L</th>
              <th class="text-end">Actions</th>
            </tr>
            </thead>

            <tbody class="table-border-bottom-0">
            @forelse($holdings as $row)
              <tr>

                <!-- Symbol -->
                <td>
                  <span class="fw-semibold">{{ $row['tradingsymbol'] }}</span>
                  <div class="text-muted small">{{ $row['exchange'] ?? 'NSE' }}</div>
                </td>

                <!-- Quantity -->
                <td>
                  <span class="fw-semibold">{{ $row['quantity'] }}</span>
                  <div class="text-muted small">
                    T1: {{ $row['t1quantity'] ?? 0 }}
                  </div>
                </td>

                <!-- Avg Price -->
                <td>
                  ₹{{ number_format($row['averageprice'], 2) }}
                </td>

                <!-- LTP -->
                <td class="ltp-cell"
                    data-symboltoken="{{ (string) $row['symboltoken'] }}"
                    data-avgprice="{{ $row['averageprice'] }}"
                    data-quantity="{{ $row['quantity'] }}"
                    data-prevclose="{{ $row['close'] }}">

                    <span class="ltp-value fw-semibold">
                      ₹{{ number_format($row['ltp'], 2) }}
                    </span>
                  <div class="ltp-today-percent small
                    {{ $row['ltp'] >= $row['close'] ? 'text-success' : 'text-danger' }}">
                    {{ number_format((($row['ltp'] - $row['close']) / $row['close']) * 100, 2) }}%
                  </div>
                </td>


                <td class="pnl-cell" data-symboltoken="{{ $row['symboltoken'] }}">
                  <span class="pnl-value fw-semibold">₹0.00</span>
                  <div class="pnl-percent small">0.00%</div>
                </td>

                <!-- Actions -->
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-2">

                    <!-- BUY / MODIFY -->
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-outline-success"
                       data-order='@json($row)'
                       onclick="openOrderModal(JSON.parse(this.dataset.order), 'BUY')">
                      Buy
                    </a>

                    <!-- SELL -->
                    <a href="javascript:void(0)"
                       class="btn btn-sm btn-outline-danger"
                       data-order='@json($row)'
                       onclick="openOrderModal(JSON.parse(this.dataset.order), 'SELL')">
                      Sell
                    </a>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  No holdings found
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <!-- /Invoice table -->
    </div>
    <!--/ Customer Content -->
  </div>

  <!-- Modal -->
  @include('_partials/_modals/modal-edit-user')
  <div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-simple">
      <div class="modal-content">
        <div class="modal-body">

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

          <h4 class="text-center mb-3" id="om-title">Place Order</h4>

          <form id="orderForm"
                method="POST"
                action="{{ route('account.place.order', request('account')->id) }}"
                class="row g-4">

            @csrf

            <!-- REQUIRED HIDDEN FIELDS -->
            <input type="hidden" name="symboltoken" id="om-symbol-token">
            <input type="hidden" name="tradingsymbol" id="om-tradingsymbol">
            <input type="hidden" name="transactiontype" id="om-transactiontype">
            <input type="hidden" name="variety" id="om-variety" value="NORMAL">
            <input type="hidden" name="exchange" value="NSE">
            <input type="hidden" name="producttype" value="DELIVERY">
            <input type="hidden" name="duration" value="DAY">

            <!-- SYMBOL -->
            <div class="col-md-6">
              <label class="form-label">Symbol</label>
              <input type="text" class="form-control" id="om-symbol" readonly>
            </div>

            <!-- SIDE -->
            <div class="col-md-6">
              <label class="form-label">Side</label>
              <input type="text" class="form-control" id="om-side" readonly>
            </div>

            <!-- ORDER TYPE -->
            <div class="col-md-6">
              <label class="form-label">Order Type</label>
              <select class="form-select" name="ordertype" id="om-ordertype">
                <option value="MARKET">MARKET</option>
                <option value="LIMIT">LIMIT</option>
              </select>
            </div>

            <!-- QUANTITY -->
            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" id="om-qty">
              <div class="invalid-feedback" id="om-qty-error"></div>
            </div>

            <!-- PRICE -->
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" step="0.01" name="price" class="form-control" id="om-price">
              <div class="invalid-feedback" id="om-price-error"></div>
            </div>

            <!-- LIVE LTP -->
            <div class="col-md-6">
              <label class="form-label">Live Price</label>
              <div class="form-control bg-light">
                LTP ₹<span id="om-ltp">—</span>
              </div>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary" id="om-submit-btn">
                Submit Order
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

  <!-- /Modal -->
@endsection

