@extends('layouts/layoutMaster')
@section('title', 'Market')
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection
@section('content')
  <x-account-header :account="$account" />
  <div class="row">
    <div class="col-12">
      @include('components.account-breadcrumb')
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Live Market</h5>
        </div>
        {{-- FILTERS --}}
        <form method="GET">
          <div class="row px-3 py-2 border-bottom align-items-center g-2">
            <div class="col-md-4 d-flex align-items-center gap-2">
              <label class="small mb-0">Show</label>
              <select name="per_page" class="form-select form-select-sm w-auto"
                      onchange="this.form.submit()">
                @foreach([10,25,50,100] as $n)
                  <option value="{{ $n }}" {{ request('per_page',25)==$n?'selected':'' }}>
                    {{ $n }}
                  </option>
                @endforeach
              </select>
              <span class="small">entries</span>
            </div>
            <div class="col-md-8 d-flex justify-content-end gap-2 flex-wrap">
              <select name="exchange" class="form-select form-select-sm w-auto"
                      onchange="this.form.submit()">
                <option value="">All Exchanges</option>
                <option value="NSE" {{ request('exchange')==='NSE'?'selected':'' }}>NSE</option>
                <option value="BSE" {{ request('exchange')==='BSE'?'selected':'' }}>BSE</option>
              </select>
              <div class="input-group input-group-sm" style="max-width:250px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Search symbol..." value="{{ request('search') }}">
                <button class="input-group-text">
                  <i class="ti tabler-search"></i>
                </button>
              </div>
              @if(request()->hasAny(['search','exchange','per_page']))
                <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Clear</a>
              @endif
            </div>
          </div>
        </form>
        {{-- TABLE --}}
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
            <tr>
              <th>Symbol</th>
              <th>LTP</th>
              <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($instruments as $row)
              <tr>
                <td>
                  <strong>{{ $row['symbol'] }}</strong>
                  <div class="text-muted small">
                    {{ $row['name'] }} • {{ $row['exch_seg'] }}
                  </div>
                </td>
                <td class="ltp-cell"
                    data-symboltoken="{{ (string)$row['token'] }}">
                  <span class="ltp-value fw-semibold">—</span>
                </td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-sm btn-outline-success"
                            onclick="openOrderModal({
                          symboltoken:'{{ $row['token'] }}',
                          tradingsymbol:'{{ $row['symbol'] }}'
                        }, 'BUY')">
                      Buy
                    </button>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="openOrderModal({
                          symboltoken:'{{ $row['token'] }}',
                          tradingsymbol:'{{ $row['symbol'] }}'
                        }, 'SELL')">
                      Sell
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">
                  No instruments found
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
        @if($instruments->hasPages())
          <div class="card-footer d-flex justify-content-end">
            {{ $instruments->links('vendor.pagination.rounded') }}
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- ORDER MODAL --}}
  <div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <div>
            <h4 class="mb-1 fw-bold" id="om-symbol"></h4>
            <div class="d-flex gap-2 mt-1">
              <span id="om-side-badge" class="badge"></span>
              <span class="badge bg-label-success">DELIVERY</span>
              <span class="badge bg-label-secondary">DAY</span>
            </div>
          </div>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body pt-4">
          <div class="border rounded p-3 text-center mb-3">
            <small class="text-muted">Live Price</small>
            <div class="fw-semibold fs-4">
              ₹ <span id="om-ltp">—</span>
            </div>
          </div>
          <form id="orderForm"
                method="POST"
                action="{{ route('account.place.order',$account->id) }}"
                class="row g-3">
            @csrf
            <input type="hidden" id="om-symbol-token" name="symboltoken">
            <input type="hidden" id="om-tradingsymbol" name="tradingsymbol">
            <input type="hidden" id="om-transactiontype" name="transactiontype">
            <input type="hidden" name="exchange" value="NSE">
            <input type="hidden" name="producttype" value="DELIVERY">
            <input type="hidden" name="duration" value="DAY">
            <input type="hidden" name="variety" value="NORMAL">
            <div class="col-md-6">
              <label class="form-label">Order Type</label>
              <select class="form-select" id="om-ordertype" name="ordertype">
                <option value="MARKET">MARKET</option>
                <option value="LIMIT">LIMIT</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" id="om-qty" name="quantity">
              <div class="invalid-feedback" id="om-qty-error"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" step="0.01" class="form-control" id="om-price" name="price">
              <div class="invalid-feedback" id="om-price-error"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Circuit</label>
              <div class="alert alert-info d-flex justify-content-between align-items-center py-2 mb-0">
                <span class="fw-semibold small">Range</span>
                <span class="fw-semibold small" id="om-circuit">—</span>
              </div>
            </div>
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
@endsection
@section('page-script')
  <script>
    const clientCode = @json($account->client_id);
    const feedToken = @json($account->feed_token);
    const apiKey = @json($account->api_key);
    const tokenList = @json(
    $instruments->groupBy('exch_seg')
            ->map(function ($rows, $seg) {
                return [
                    'exchangeType' => $seg === 'NSE' ? 1 : 3,
                    'tokens' => $rows->pluck('token')
                                     ->map(function ($t) {
                                         return (string) $t;
                                     })
                                     ->values()
                                     ->toArray()
                ];
            })
            ->values()
            ->toArray()
    );

    let ltpCache = {};
    let activeToken = null;

    /* =====================================================
       SOCKET CONNECTION
    ===================================================== */
    document.addEventListener('DOMContentLoaded', function() {

      if (!tokenList.length) return;

      const socket = new WebSocket(
        `wss://smartapisocket.angelone.in/smart-stream` +
        `?clientCode=${encodeURIComponent(clientCode)}` +
        `&feedToken=${encodeURIComponent(feedToken)}` +
        `&apiKey=${encodeURIComponent(apiKey)}`
      );

      socket.binaryType = 'arraybuffer';
      socket.onopen = function() {
        socket.send(JSON.stringify({
          correlationID: clientCode,
          action: 1,
          params: {
            mode: 2,
            tokenList: tokenList
          }
        }));
      };

      socket.onmessage = function(event) {

        if (!(event.data instanceof ArrayBuffer)) return;

        const view = new DataView(event.data);
        if (view.getInt8(0) !== 2) return;

        const bytes = new Uint8Array(event.data);
        let token = '';

        for (let i = 2; i < 27; i++) {
          if (!bytes[i]) break;
          token += String.fromCharCode(bytes[i]);
        }
        token = token.trim();
        const ltp = view.getInt32(43, true) / 100;
        if (!ltp) return;
        ltpCache[token] = ltp;

        // Update table LTP
        document
          .querySelectorAll(`.ltp-cell[data-symboltoken="${token}"] .ltp-value`)
          .forEach(el => el.innerText = `₹${ltp.toFixed(2)}`);

        // Update modal LTP if active
        if (activeToken === token) {
          document.getElementById('om-ltp').innerText = ltp.toFixed(2);

          const orderType = document.getElementById('om-ordertype').value;
          if (orderType === 'MARKET') {
            document.getElementById('om-price').value = ltp.toFixed(2);
          }
        }
      };
    });


    /* =====================================================
       OPEN ORDER MODAL
    ===================================================== */
    function openOrderModal(order, side) {

      activeToken = order.symboltoken;

      const ltp = ltpCache[order.symboltoken];

      // Set symbol data
      document.getElementById('om-symbol').innerText = order.tradingsymbol;
      document.getElementById('om-symbol-token').value = order.symboltoken;
      document.getElementById('om-tradingsymbol').value = order.tradingsymbol;
      document.getElementById('om-transactiontype').value = side;

      // Side badge
      const badge = document.getElementById('om-side-badge');
      badge.innerText = side;
      badge.className = 'badge ' + (side === 'BUY' ? 'bg-success' : 'bg-danger');

      // Show live price
      document.getElementById('om-ltp').innerText =
        ltp ? ltp.toFixed(2) : '—';

      // Default = MARKET
      const orderTypeSelect = document.getElementById('om-ordertype');
      orderTypeSelect.value = 'MARKET';

      const priceInput = document.getElementById('om-price');

      // Show LTP but disable editing
      if (ltp) {
        priceInput.value = ltp.toFixed(2);
      } else {
        priceInput.value = '';
      }

      priceInput.disabled = true;

      // Clear quantity
      document.getElementById('om-qty').value = '';

      new bootstrap.Modal(document.getElementById('orderModal')).show();
    }


    /* =====================================================
       ORDER TYPE CHANGE
    ===================================================== */
    document.getElementById('om-ordertype')
      ?.addEventListener('change', function() {

        const priceInput = document.getElementById('om-price');

        if (this.value === 'MARKET') {

          // Show LTP but disable
          if (activeToken && ltpCache[activeToken]) {
            priceInput.value = ltpCache[activeToken].toFixed(2);
          }

          priceInput.disabled = true;

        } else {

          priceInput.disabled = false;

          if (activeToken && ltpCache[activeToken]) {
            priceInput.value = ltpCache[activeToken].toFixed(2);
          }
        }
      });


    /* =====================================================
       VALIDATION + BUTTON LOADING
    ===================================================== */
    function showError(id, message) {
      const input = document.getElementById(id);
      const error = document.getElementById(id + '-error');
      input.classList.add('is-invalid');
      error.innerText = message;
    }

    function clearError(id) {
      const input = document.getElementById(id);
      const error = document.getElementById(id + '-error');
      input.classList.remove('is-invalid');
      error.innerText = '';
    }

    document.getElementById('orderForm')
      ?.addEventListener('submit', function(e) {

        let hasError = false;

        const qty = parseInt(document.getElementById('om-qty').value, 10);
        const price = parseFloat(document.getElementById('om-price').value);
        const type = document.getElementById('om-ordertype').value;

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
        }

        if (hasError) {
          e.preventDefault();
          return;
        }

        const btn = document.getElementById('om-submit-btn');
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.btn-loading-text').classList.remove('d-none');
      });
  </script>
@endsection
