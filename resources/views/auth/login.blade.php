@php
  use \Illuminate\Support\Facades\Route;
  $customizerHidden = 'customizer-hide';

  // Dynamic app name
  $appName = config('variables.templateName', config('app.name', 'Application'));

  // Social login availability
  $socialLogins = [
      'facebook' => config('services.facebook.client_id'),
      'twitter'  => config('services.twitter.client_id'),
      'github'   => config('services.github.client_id'),
      'google'   => config('services.google.client_id'),
  ];
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Login') . ' - ' . $appName)

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
              <a href="{{ route('home') }}" class="app-brand-link">
                <span class="app-brand-logo demo">@include('_partials.macros')</span>
                <span class="app-brand-text demo text-heading fw-bold">{{ $appName }}</span>
              </a>
            </div>

            <h4 class="mb-1">Welcome to {{ $appName }}! 👋</h4>
            <p class="mb-6">Please login to continue</p>

            <!-- LOGIN FORM -->
            <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
              @csrf

              <!-- Email / Username -->
              <div class="mb-6 form-control-validation">
                <label class="form-label">Email or Username</label>
                <input
                  type="text"
                  class="form-control @error('email') is-invalid @enderror"
                  name="email"
                  placeholder="Enter your email or username"
                  value="{{ old('email') }}"
                  autofocus
                />
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              <!-- Password -->
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label">Password</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    placeholder="********"
                  />
                  <span class="input-group-text cursor-pointer">
                                    <i class="icon-base ti tabler-eye-off"></i>
                                </span>
                </div>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              <!-- Remember + Forgot -->
              <div class="my-8">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="remember">
                    <label class="form-check-label">Remember Me</label>
                  </div>

                  @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                      <p class="mb-0">Forgot Password?</p>
                    </a>
                  @endif
                </div>
              </div>

              <!-- Submit -->
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">
                  Login
                </button>
              </div>
            </form>

            <!-- Register Link -->
            <p class="text-center">
              <span>New here?</span>
              @if(Route::has('register'))
                <a href="{{ route('register') }}">
                  <span>Create an account</span>
                </a>
              @endif
            </p>

            <!-- Divider -->
            @if(array_filter($socialLogins))
              <div class="divider my-6">
                <div class="divider-text">or</div>
              </div>

              <!-- Social Login Buttons -->
              <div class="d-flex justify-content-center">

                @if($socialLogins['facebook'])
                  <a href="{{ route('social.login', 'facebook') }}"
                     class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
                    <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
                  </a>
                @endif

                @if($socialLogins['twitter'])
                  <a href="{{ route('social.login', 'twitter') }}"
                     class="btn btn-icon rounded-circle btn-text-twitter me-1_5">
                    <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
                  </a>
                @endif

                @if($socialLogins['github'])
                  <a href="{{ route('social.login', 'github') }}"
                     class="btn btn-icon rounded-circle btn-text-github me-1_5">
                    <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
                  </a>
                @endif

                @if($socialLogins['google'])
                  <a href="{{ route('social.login', 'google') }}"
                     class="btn btn-icon rounded-circle btn-text-google-plus">
                    <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
                  </a>
                @endif

              </div>
            @endif

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
