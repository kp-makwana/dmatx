@extends('layouts/layoutMaster')

@section('title', __('auth.create_account'))

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">

        <div class="card">
          <div class="card-body">

            <!-- Logo -->
            <div class="app-brand justify-content-center mb-6">
              <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo">@include('_partials.macros')</span>
                <span class="app-brand-text demo text-heading fw-bold">
                {{ config('variables.templateName') }}
              </span>
              </a>
            </div>

            <h4 class="mb-1">{{ __('auth.register_title') }}</h4>
            <p class="mb-6">{{ __('auth.register_subtitle') }}</p>

            <!-- Register Form -->
            <form id="formAuthentication"
                  action="{{ route('register') }}"
                  method="POST"
                  class="mb-6">
              @csrf

              {{-- Name --}}
              <div class="mb-6 form-control-validation">
                <label for="name" class="form-label">{{ __('auth.name') }}</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="{{ __('auth.enter_name') }}"
                       required />
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Email --}}
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">{{ __('auth.email') }}</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="{{ __('auth.enter_email') }}"
                       required />
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Password --}}
              <div class="mb-6 form-password-toggle form-control-validation">
                <label for="password" class="form-label">{{ __('auth.password') }}</label>
                <div class="input-group input-group-merge">
                  <input type="password"
                         class="form-control @error('password') is-invalid @enderror"
                         id="password"
                         name="password"
                         placeholder="********"
                         required />
                  <span class="input-group-text cursor-pointer">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
                </div>
                @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              {{-- Terms --}}
              <div class="my-8">
                <div class="form-check mb-0 ms-2">
                  <input class="form-check-input" type="checkbox" id="terms" required>
                  <label class="form-check-label" for="terms">
                    {{ __('auth.agree_terms') }}
                    <a href="#">{{ __('auth.privacy_policy') }}</a>
                  </label>
                </div>
              </div>

              <button class="btn btn-primary d-grid w-100">
                {{ __('auth.sign_up') }}
              </button>

            </form>

            <p class="text-center">
              <span>{{ __('auth.already_account') }}</span>
              <a href="{{ route('login') }}">
                <span>{{ __('auth.sign_in') }}</span>
              </a>
            </p>

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
