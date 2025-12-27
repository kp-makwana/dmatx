@extends('layouts/layoutMaster')

@section('title', 'Two Factor Verification')

@section('page-script')
  @vite(['resources/assets/js/pages-two-factor-otp.js'])
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
              <li class="breadcrumb-item active">
                Two Factor Verification
              </li>
            </ol>
          </nav>
        </div>

        {{-- ================= CARD BODY ================= --}}
        <div class="card-body">

          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">

              <form method="POST"
                    action="{{ route('angle-one.submit.step.four', $account->id) }}"
                    id="twoFactorForm">
                @csrf

                {{-- ================= OTP CARD ================= --}}
                <div class="card border shadow-none mb-6">

                  <div class="card-header text-center">
                    <h5>Verification Code</h5>
                    <p class="text-muted mb-0">
                      A verification code has been sent to your registered
                      <strong>Email and Mobile Number</strong>
                    </p>
                  </div>

                  <div class="card-body text-center">

                    <div class="d-flex justify-content-center gap-2 mb-3 numeral-mask-wrapper"
                         data-type="email_mobile">
                      @for ($i = 0; $i < 6; $i++)
                        <input type="text"
                               maxlength="1"
                               class="form-control auth-input h-px-50 text-center numeral-mask otp-input">
                      @endfor
                    </div>

                    @error('email_mobile_otp')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror

                    <input type="hidden" name="email_mobile_otp">

                    <small class="text-muted">
                      Did not receive the verification code?
                      <span class="fw-semibold countdown" data-type="email_mobile"></span>
                    </small>

                    <div class="mt-1">
                      <button type="button"
                              class="btn btn-link p-0 resend-btn"
                              data-type="email_mobile"
                              data-url="{{ route('angle-one.resend.totp.otp', $account->id) }}">
                        Resend Code
                      </button>
                    </div>

                  </div>
                </div>

                {{-- ================= ACTION BUTTONS ================= --}}
                <div class="d-flex justify-content-end gap-2">

                  <button type="submit"
                          class="btn btn-primary d-inline-flex align-items-center"
                          id="verifySubmitBtn">
                    <span class="btn-text">Verify & Continue</span>
                    <span class="spinner-border spinner-border-sm d-none ms-2"
                          role="status"
                          aria-hidden="true"></span>
                    <span class="btn-loading-text d-none ms-2">
                    Verifying...
                  </span>
                  </button>

                  <a href="{{ route('accounts.index') }}"
                     class="btn btn-outline-secondary">
                    <i class="ti tabler-arrow-left me-1"></i> Back
                  </a>

                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- ================= SUBMIT SPINNER SCRIPT ================= --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('twoFactorForm');
      const btn = document.getElementById('verifySubmitBtn');

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
