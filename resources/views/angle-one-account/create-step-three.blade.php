@extends('layouts/layoutMaster')

@section('title', 'Create Account')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection
@section('content')
  <div class="row">
    <div class="col-12">

      {{-- ================= MAIN WRAPPER CARD ================= --}}
      <div class="card">

        {{-- ================= BREADCRUMB ================= --}}
        <div class="card-header">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon mb-0">
              <li class="breadcrumb-item">
                <a href="{{ route('accounts.index') }}">Accounts</a>
                <i class="breadcrumb-icon ti tabler-chevron-right"></i>
              </li>
              <li class="breadcrumb-item active">Generate TOTP</li>
            </ol>
          </nav>
        </div>

        {{-- ================= CARD BODY ================= --}}
        <div class="card-body">

          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">

              {{-- ================= INNER FORM CARD ================= --}}
              <div class="card border shadow-none">
                <div class="card-body">

                  <form method="POST"
                        action="{{ route('angle-one.submit.step.three', $account->id) }}"
                        id="generateTotpForm"
                        class="row g-4">
                    @csrf

                    {{-- Client ID --}}
                    <div class="col-12">
                      <label class="form-label" for="client_id">
                        Client ID <span class="text-danger">*</span>
                      </label>

                      <div class="input-group input-group-merge">
                        <input
                          type="text"
                          id="client_id"
                          class="form-control"
                          value="{{ $account->client_id }}"
                          disabled
                        />
                        <span class="input-group-text cursor-pointer">
                        <i
                          class="icon-base ti tabler-help-circle text-body-secondary"
                          data-bs-toggle="tooltip"
                          title="Client ID provided by AngelOne"
                        ></i>
                      </span>
                      </div>
                    </div>

                    {{-- PIN --}}
                    <div class="col-12">
                      <label class="form-label" for="pin">
                        PIN <span class="text-danger">*</span>
                      </label>

                      <div class="input-group input-group-merge">
                        <input
                          type="password"
                          id="pin"
                          name="pin"
                          maxlength="4"
                          class="form-control @error('pin') is-invalid @enderror"
                          placeholder="Enter 4-digit PIN"
                        />
                        <span class="input-group-text cursor-pointer">
                        <i
                          class="icon-base ti tabler-help-circle text-body-secondary"
                          data-bs-toggle="tooltip"
                          title="Enter your AngelOne account PIN"
                        ></i>
                      </span>
                      </div>

                      @error('pin')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>

                    {{-- ================= ACTION BUTTONS ================= --}}
                    <div class="col-12">
                      <div class="d-flex justify-content-end gap-2">

                        <button type="submit"
                                class="btn btn-primary d-inline-flex align-items-center"
                                id="generateTotpSubmitBtn">
                          <span class="btn-text">Submit</span>
                          <span class="spinner-border spinner-border-sm d-none ms-2"
                                role="status"
                                aria-hidden="true"></span>
                          <span class="btn-loading-text d-none ms-2">
                          Processing...
                        </span>
                        </button>

                        <a href="{{ route('accounts.index') }}"
                           class="btn btn-outline-secondary">
                          <i class="ti tabler-arrow-left me-1"></i> Back
                        </a>

                      </div>
                    </div>

                  </form>
                </div>
              </div>
              {{-- ================= END INNER CARD ================= --}}

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- ================= SUBMIT SPINNER SCRIPT ================= --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('generateTotpForm');
      const btn = document.getElementById('generateTotpSubmitBtn');

      if (!form || !btn) return;

      form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.btn-loading-text').classList.remove('d-none');
      });
    });
  </script>
@endsection
