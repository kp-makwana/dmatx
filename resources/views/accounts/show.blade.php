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
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/modal-edit-user.js',
    'resources/assets/js/app-ecommerce-customer-detail.js',
    'resources/assets/js/app-ecommerce-customer-detail-overview.js',
    'resources/assets/js/app-user-view-account.js'
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
    function indianCurrency(number, decimal = 0) {
      if (number === null || number === undefined || isNaN(number)) {
        return '0';
      }

      const factor = Math.pow(10, decimal);
      const rounded = Math.round(number * factor) / factor;

      let [integerPart, decimalPart] = rounded
        .toFixed(decimal)
        .split('.');

      // Indian grouping
      let lastThree = integerPart.slice(-3);
      let otherNumbers = integerPart.slice(0, -3);

      if (otherNumbers !== '') {
        lastThree = ',' + lastThree;
      }

      otherNumbers = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ',');

      return decimal > 0
        ? otherNumbers + lastThree + '.' + decimalPart
        : otherNumbers + lastThree;
    }
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
            if (ltpEl) {
              ltpEl.innerText = `₹${indianCurrency(ltp, 2)}`;

              // reset old color
              ltpEl.classList.remove('text-success', 'text-danger');

              // color based on previous close
              if (prev) {
                ltpEl.classList.add(
                  ltp >= prev ? 'text-success' : 'text-danger'
                );
              }
            }

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
                `₹${indianCurrency(pnl, 2)}`;
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

      /* HIDDEN FIELDS */
      document.getElementById('om-symbol-token').value = order.symboltoken;
      document.getElementById('om-tradingsymbol').value = order.tradingsymbol;
      document.getElementById('om-transactiontype').value = side;

      /* HEADER */
      document.getElementById('om-symbol').innerText = order.tradingsymbol;

      const sideBadge = document.getElementById('om-side-badge');
      sideBadge.innerText = side;
      sideBadge.className = 'badge ' + (side === 'BUY' ? 'bg-success' : 'bg-danger');

      /* BADGES */
      document.getElementById('om-info-ordertype').innerText =
        document.getElementById('om-ordertype').value;

      document.getElementById('om-info-product').innerText = 'DELIVERY';
      document.getElementById('om-info-duration').innerText = 'DAY';

      /* PRICE CARDS */
      document.getElementById('om-avgprice').innerText =
        order.averageprice ? `₹${Number(order.averageprice).toFixed(2)}` : '—';

      document.getElementById('om-availableqty').innerText =
        order.quantity ?? '—';

      /* LTP */
      const ltp = ltpCache[order.symboltoken];
      document.getElementById('om-ltp').innerText =
        ltp ? ltp.toFixed(2) : '—';

      /* CIRCUIT */
      let limits = circuitCache[order.symboltoken];
      if (!limits) limits = fallbackCircuit(order.close);

      const circuitEl = document.getElementById('om-circuit');
      circuitEl.innerText = limits
        ? `₹${limits.lower} - ₹${limits.upper}`
        : '—';

      /* FORM RESET */
      const qtyInput = document.getElementById('om-qty');
      const priceInput = document.getElementById('om-price');
      const type = document.getElementById('om-ordertype').value;

      qtyInput.value = type === 'MARKET' ? 0 : (side === 'SELL' ? order.quantity : '');

      qtyInput.classList.remove('is-invalid');
      priceInput.classList.remove('is-invalid');
      document.getElementById('om-qty-error').innerText = '';
      document.getElementById('om-price-error').innerText = '';

      if (type === 'MARKET') {
        priceInput.value = '';
        priceInput.disabled = true;
        priceInput.placeholder = 'AT MARKET';
      } else {
        priceInput.disabled = false;
        priceInput.placeholder = 'Enter price';
        priceInput.value = ltp ? ltp.toFixed(2) : '';
      }

      /* RESET SUBMIT BUTTON */
      const btn = document.getElementById('om-submit-btn');
      btn.disabled = false;
      btn.querySelector('.btn-text').classList.remove('d-none');
      btn.querySelector('.spinner-border').classList.add('d-none');
      btn.querySelector('.btn-loading-text').classList.add('d-none');

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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modalEl = document.getElementById('orderModal');
      if (!modalEl) return;

      modalEl.addEventListener('hidden.bs.modal', () => {
        // clear inputs
        document.getElementById('om-qty')?.classList.remove('is-invalid');
        document.getElementById('om-price')?.classList.remove('is-invalid');

        // clear error messages
        const qtyErr   = document.getElementById('om-qty-error');
        const priceErr = document.getElementById('om-price-error');

        if (qtyErr) qtyErr.innerText = '';
        if (priceErr) priceErr.innerText = '';

        // OPTIONAL: reset price disabled state
        const priceInput = document.getElementById('om-price');
        if (priceInput) {
          priceInput.disabled = false;
          priceInput.value = '';
        }
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const orderForm = document.getElementById('orderForm');
      if (!orderForm) return;

      orderForm.addEventListener('submit', (e) => {

        // If validation prevented submit, don't spin
        if (e.defaultPrevented) return;

        const btn = document.getElementById('om-submit-btn');
        if (!btn) return;

        const spinner = btn.querySelector('.spinner-border');
        const text = btn.querySelector('.btn-text');
        const loadingText = btn.querySelector('.btn-loading-text');

        // Disable button
        btn.disabled = true;

        // Toggle UI
        text.classList.add('d-none');
        spinner.classList.remove('d-none');
        loadingText.classList.remove('d-none');
      });
    });
  </script>

@endsection

@section('content')
  <x-account-header :account="$account" />
  <div class="row">
    <!-- Customer Content -->
    <div class="col-xl-12 col-lg-12 col-md-12 order-0 order-md-1">
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
                  <span class="fw-semibold">&#8377;{{ \App\Helpers\Helpers::indianCurrency($summary['totalholdingvalue'] ?? 0) }}</span>
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
                  <span class="fw-semibold">&#8377;{{ \App\Helpers\Helpers::indianCurrency($summary['totalinvvalue'] ?? 0) }}</span>
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
                    &#8377;{{ \App\Helpers\Helpers::indianCurrency($summary['totalprofitandloss'] ?? 0) }}
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
                  ₹{{ \App\Helpers\Helpers::indianCurrency($row['averageprice'],2) }}
                </td>

                <!-- LTP -->
                <td class="ltp-cell"
                    data-symboltoken="{{ (string) $row['symboltoken'] }}"
                    data-avgprice="{{ $row['averageprice'] }}"
                    data-quantity="{{ $row['quantity'] }}"
                    data-prevclose="{{ $row['close'] }}">

                    <span class="ltp-value fw-semibold">
                      ₹{{ \App\Helpers\Helpers::indianCurrency($row['ltp'], 2) }}
                    </span>
                  <div class="ltp-today-percent small
                  {{ ($row['close'] > 0 && $row['ltp'] >= $row['close']) ? 'text-success' : 'text-danger' }}">
                  {{ $row['close'] > 0
                        ? number_format((($row['ltp'] - $row['close']) / $row['close']) * 100, 2) . '%'
                        : '0.00%' }}
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header border-0 pb-0">
          <div>
            <h4 class="mb-1">
              <span id="om-symbol" class="fw-bold"></span>
              <span class="badge rounded-pill bg-label-primary ms-2"
                    style="font-size:0.65rem;font-weight:500;">
              NSE
            </span>
            </h4>

            <!-- BADGES -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span id="om-side-badge" class="badge"></span>
              <span class="badge bg-label-primary" id="om-info-ordertype">
              LIMIT
            </span>

              <span class="badge bg-label-success" id="om-info-product">
              DELIVERY
            </span>

              <span class="badge bg-label-secondary" id="om-info-duration">
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
                  ₹ <span id="om-ltp">—</span>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <small class="text-muted">Avg Price</small>
                <div class="fw-semibold fs-5" id="om-avgprice">—</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="border rounded p-3 text-center">
                <small class="text-muted">Available Qty</small>
                <div class="fw-semibold fs-5" id="om-availableqty">—</div>
              </div>
            </div>
          </div>

          <!-- FORM -->
          <form id="orderForm"
                method="POST"
                action="{{ route('account.place.order', $account->id) }}"
                class="row g-3">

            @csrf

            <!-- REQUIRED HIDDEN -->
            <input type="hidden" name="symboltoken" id="om-symbol-token">
            <input type="hidden" name="tradingsymbol" id="om-tradingsymbol">
            <input type="hidden" name="transactiontype" id="om-transactiontype">
            <input type="hidden" name="exchange" value="NSE">
            <input type="hidden" name="producttype" value="DELIVERY">
            <input type="hidden" name="duration" value="DAY">
            <input type="hidden" name="variety" value="NORMAL">

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
              <input type="number" class="form-control" name="quantity" id="om-qty">
              <div class="invalid-feedback" id="om-qty-error"></div>
            </div>

            <!-- PRICE -->
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" step="0.05" class="form-control" name="price" id="om-price">
              <div class="invalid-feedback" id="om-price-error"></div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Circuit</label>

              <div class="alert alert-info d-flex justify-content-between align-items-center py-2 mb-0"
                   id="om-circuit-alert"
                   style="height:38px">

                <div class="d-flex align-items-center">
                  <i class="ti tabler-info-circle me-1"></i>
                  <span class="fw-semibold small">Range</span>
                </div>

                <span class="fw-semibold small" id="om-circuit">—</span>
              </div>
            </div>

            <!-- ACTIONS -->
            <div class="col-12 text-center mt-4">
              <button id="om-submit-btn"
                      type="submit"
                      class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                <span class="btn-text">Place Order</span>
                <span class="spinner-border spinner-border-sm d-none ms-2"></span>
                <span class="btn-loading-text d-none ms-2">Placing...</span>
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

  <!-- /Modal -->
@endsection

