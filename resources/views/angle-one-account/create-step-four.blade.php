@extends('layouts/layoutMaster')

@section('title', 'Two Factor Verification')

@section('page-script')
  @vite(['resources/assets/js/pages-two-factor-otp.js'])
@endsection

@section('content')
  <div class="row justify-content-center">
    <div class="col-xl-6">
      <form method="POST"
            action="{{ route('angle-one.submit.step.four', $account->id) }}"
            id="twoFactorForm">
        @csrf

        <div class="card">
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

        <div class="text-center mt-6">
          <button type="submit" class="btn btn-primary px-6">
            Verify & Continue
          </button>
        </div>

      </form>
    </div>
  </div>
@endsection
