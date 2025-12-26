@extends('layouts/layoutMaster')

@section('title', 'Two Factor Verification')

@section('page-script')
  @vite(['resources/assets/js/pages-two-factor-otp.js'])
@endsection

@section('content')
  <div class="row justify-content-center">
    <div class="col-xl-10">
      <form method="POST"
            action="{{ route('angle-one.submit.step.two', $account->id) }}"
            id="twoFactorForm">
        @csrf

        <div class="row g-6">

          <!-- EMAIL OTP -->
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-header text-center">
                <h5>Email Verification</h5>
                <p class="text-muted mb-0">
                  OTP sent to <strong>{{ $account->email }}</strong>
                </p>
              </div>
              <div class="card-body text-center">
                <div class="d-flex justify-content-center gap-2 mb-3 numeral-mask-wrapper"
                     data-type="email">
                  @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1"
                           class="form-control auth-input h-px-50 text-center numeral-mask otp-input">
                  @endfor
                </div>

                <input type="hidden" name="email_otp">

                <small class="text-muted">
                  Did not receive Email OTP?
                  <span class="fw-semibold countdown" data-type="email"></span>
                </small>

                <div class="mt-1">
                  <button type="button"
                          class="btn btn-link p-0 resend-btn"
                          data-type="email"
                          data-url="{{ route('angle-one.email.otp.resend', $account->id) }}">
                    Resend OTP
                  </button>
                </div>

              </div>
            </div>
          </div>

          <!-- MOBILE OTP -->
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-header text-center">
                <h5>Mobile Verification</h5>
                <p class="text-muted mb-0">
                  OTP sent to <strong>+91 {{ $account->mobile }}</strong>
                </p>
              </div>

              <div class="card-body text-center">

                <div class="d-flex justify-content-center gap-2 mb-3 numeral-mask-wrapper"
                     data-type="mobile">
                  @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1"
                           class="form-control auth-input h-px-50 text-center numeral-mask otp-input">
                  @endfor
                </div>

                <input type="hidden" name="mobile_otp">

                <small class="text-muted">
                  Did not receive Mobile OTP?
                  <span class="fw-semibold countdown" data-type="mobile"></span>
                </small>

                <div class="mt-1">
                  <button type="button"
                          class="btn btn-link p-0 resend-btn"
                          data-type="mobile"
                          data-url="{{ route('angle-one.mobile.otp.resend', $account->id) }}">
                    Resend OTP
                  </button>
                </div>

              </div>
            </div>
          </div>

        </div>

        <div class="text-center mt-6">
          <button type="submit" class="btn btn-primary px-6">
            Verify & Continue
          </button>
        </div>

      </form>

    </div>
  </div>
@endsection
