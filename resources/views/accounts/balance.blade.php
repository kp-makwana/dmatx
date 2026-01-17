@extends('layouts/layoutMaster')

@section('title', 'Market')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('content')

  {{-- Account Header --}}
  <x-account-header :account="$account" />

  <div class="row">
    <div class="col-12">

      {{-- Account Breadcrumb --}}
      @include('components.account-breadcrumb')

      {{-- ================= BALANCE OVERVIEW ================= --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Balance Overview</h5>
        </div>

        <div class="card-body">
          <div class="row g-3">

            {{-- Available Balance --}}
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <small class="text-muted">Available Balance</small>
                <h4 class="mb-0 text-success">
                  ₹ {{ number_format($balance['available'], 2) }}
                </h4>
              </div>
            </div>

            {{-- Utilised Margin --}}
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <small class="text-muted">Utilised Margin</small>
                <h4 class="mb-0 text-danger">
                  ₹ {{ number_format($balance['utilised'], 2) }}
                </h4>
              </div>
            </div>

            {{-- Opening Balance --}}
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <small class="text-muted">Opening Balance</small>
                <h4 class="mb-0">
                  ₹ {{ number_format($balance['opening'], 2) }}
                </h4>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ================= BALANCE DETAILS ================= --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Balance Details</h5>
        </div>

        <div class="card-body">

          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Payin</span>
            <strong class="text-success">
              ₹ {{ number_format($balance['payin'], 2) }}
            </strong>
          </div>

          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Payout</span>
            <strong class="text-danger">
              ₹ {{ number_format($balance['payout'], 2) }}
            </strong>
          </div>

          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Collateral</span>
            <span>
            ₹ {{ number_format($balance['collateral'], 2) }}
          </span>
          </div>

          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Limit Margin</span>
            <span>
            ₹ {{ number_format($balance['limit'], 2) }}
          </span>
          </div>

          <div class="d-flex justify-content-between py-2">
            <span class="text-muted">M2M (Realized / Unrealized)</span>
            <span>
            ₹ {{ number_format($balance['m2m_r'], 2) }}
            /
            ₹ {{ number_format($balance['m2m_u'], 2) }}
          </span>
          </div>

        </div>
      </div>

      {{-- ================= LAST UPDATED ================= --}}
      <div class="text-end text-muted small mb-4">
        Last updated at {{ $balance['updated_at'] }}
      </div>

    </div>
  </div>

@endsection
