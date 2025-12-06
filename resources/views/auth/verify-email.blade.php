@php
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verify Email')

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/page-auth.scss')
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

          <h4 class="mb-1">Verify your email ✉️</h4>

          <p class="text-start mb-0">
            Account activation link sent to your email address:
            <span class="fw-medium">{{ $email }}</span>.
            Please follow the link inside to continue.
          </p>

          <a class="btn btn-primary w-100 my-6" href="{{ url('/') }}">
            Skip for now
          </a>

          <p class="text-center mb-0">
            Didn't get the mail?
            <a href="{{ route('password.email', ['email' => $email]) }}">
              Resend
            </a>
          </p>

        </div>
      </div>
    </div>
  </div>
@endsection
