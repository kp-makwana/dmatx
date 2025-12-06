@php
  $customizerHidden = 'customizer-hide';
  // Mask email → show first 2 letters + **** + domain
  $email = $user->email ?? 'example@mail.com';
  [$name, $domain] = explode('@', $email);
  $maskedEmail = substr($name, 0, 2) . str_repeat('*', max(strlen($name)-2, 0)) . '@' . $domain;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Email Verification - Register')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/pages-auth.js', 'resources/assets/js/pages-auth-two-steps.js'])
@endsection

@section('content')
  <div class="authentication-wrapper authentication-basic px-6">
    <div class="authentication-inner py-6">

      <div class="card">
        <div class="card-body">

          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
            </a>
          </div>

          <h4 class="mb-1">Email Verification 🔐</h4>

          <p class="text-start mb-6">
            We have sent a verification code to your registered email address.
            <span class="fw-medium d-block mt-1 text-heading">{{ $maskedEmail }}</span>
          </p>

          <p class="mb-0">Enter the 6-digit code sent to your email</p>

          <form id="twoStepsForm"
                action="{{ route('otp.verify', $user->id) }}"
                method="POST">
            @csrf

            <div class="mb-6 form-control-validation">
              <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" autofocus />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" />
                <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2" maxlength="1" />
              </div>

              <input type="hidden" name="otp" />

              @error('otp')
              <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <button class="btn btn-primary d-grid w-100 mb-6">Verify Email</button>

            <div class="text-center">
              Didn't receive the code?
              <a href="{{ route('otp.resend', $user->id) }}">Resend Code</a>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
@endsection
