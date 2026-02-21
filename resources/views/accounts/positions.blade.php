@extends('layouts/layoutMaster')
@section('title', 'Positions')
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  <script>
    const clientCode = @json($account->client_id);
    const feedToken  = @json($account->feed_token);
    const apiKey     = @json($account->api_key);
    const tokens     = @json(collect($positions)->pluck('symboltoken')->values());

    document.addEventListener('DOMContentLoaded', () => {
      if (!tokens.length) return;
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

        token = token.trim();
        if (!token) return;

        const ltp = view.getInt32(43, true) / 100;
        if (!ltp) return;

        document.querySelectorAll(`tr[data-token="${token}"]`)
          .forEach(row => updateRow(row, ltp));
      };
    });

    /* =========================================
       UPDATE ROW
    ========================================= */
    function updateRow(row, ltp) {

      const qty = parseFloat(row.dataset.netqty);
      const avg = parseFloat(row.dataset.avgprice);

      const invested = qty * avg;
      const current  = qty * ltp;
      const pnl      = current - invested;

      row.querySelector('.ltp').innerText = ltp.toFixed(2);
      row.querySelector('.current').innerText = current.toFixed(2);
      row.querySelector('.pnl').innerText = pnl.toFixed(2);

      const pnlEl = row.querySelector('.pnl');
      pnlEl.classList.remove('text-success','text-danger');
      pnlEl.classList.add(pnl >= 0 ? 'text-success' : 'text-danger');
    }
  </script>
@endsection
@section('content')

  {{-- Account Header --}}
  <x-account-header :account="$account" />

  <div class="row">
    <div class="col-12">

      {{-- Account Breadcrumb --}}
      @include('components.account-breadcrumb')
      <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">My Positions</h5>
          <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
            Refresh
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
              <th>Symbol</th>
              <th>Net Qty</th>
              <th>Avg Price</th>
              <th>LTP</th>
              <th>Invested</th>
              <th>Current</th>
              <th>P&L</th>
            </tr>
            </thead>
            <tbody>
            @forelse($positions as $pos)
              @php
                $buyQty  = (float) $pos['buyqty'];
                $sellQty = (float) $pos['sellqty'];
                $netQty = $buyQty - $sellQty;
                $buyAmount  = (float) $pos['buyamount'];
                $sellAmount = (float) $pos['sellamount'];
                $netAmount = $buyAmount - $sellAmount;
                $avgPrice = $netQty != 0 ? $netAmount / $netQty : 0;
              @endphp
              @if($netQty != 0)
                <tr data-token="{{ $pos['symboltoken'] }}" data-netqty="{{ $netQty }}" data-avgprice="{{ $avgPrice }}" >
                  <td>
                    <div class="fw-semibold">{{ $pos['tradingsymbol'] }}</div>
                    <small class="text-muted">{{ $pos['exchange'] }}</small>
                  </td>
                  <td>{{ $netQty }}</td>
                  <td>₹{{ number_format($avgPrice,2) }}</td>
                  <td class="ltp text-muted">—</td>
                  <td>₹{{ number_format($netAmount,2) }}</td>
                  <td class="current text-muted">—</td>
                  <td class="pnl fw-semibold">—</td>
                </tr>
              @endif
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  No Open Positions
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection
