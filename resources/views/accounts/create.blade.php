@extends('layouts/layoutMaster')

@section('title', 'Create Account')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  {{--  @vite(['resources/assets/js/form-validation.js'])--}}
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">

        <div class="card-header">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
              <li class="breadcrumb-item">
                <a href="{{ route('accounts.index') }}">Accounts</a>
                <i class="breadcrumb-icon ti tabler-chevron-right"></i>
              </li>
              <li class="breadcrumb-item active">Create</li>
            </ol>
          </nav>
        </div>

        <div class="card-body">
          <form method="POST" action="{{ route('accounts.store') }}" class="row g-4">
            @csrf

            {{-- Nickname --}}
            <div class="col-md-6">
              <label class="form-label">Nickname <span class="text-danger">*</span></label>
              <input type="text"
                     name="nickname"
                     class="form-control @error('nickname') is-invalid @enderror"
                     value="{{ old('nickname') }}"
                     placeholder="My Trading Account">
              @error('nickname')
              <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Client ID --}}
            <div class="col-md-6">
              <label class="form-label" for="client_id">
                Client ID <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="client_id"
                  name="client_id"
                  class="form-control @error('client_id') is-invalid @enderror"
                  placeholder="e.g. ABC12345"
                  value="{{ old('client_id') }}"
                  autocomplete="off"
                />

                <span class="input-group-text cursor-pointer">
                <i
                  class="icon-base ti tabler-help-circle text-body-secondary"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="Enter the Client ID provided by AngelOne (available in AngelOne Profile/dashboard)"
                ></i>
              </span>
              </div>

              @error('client_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>


            {{-- MPIN --}}
            <div class="col-md-6">
              <label class="form-label" for="pin">
                MPIN <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge form-password-toggle">
                <input
                  type="password"
                  id="pin"
                  name="pin"
                  maxlength="4"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  class="form-control @error('pin') is-invalid @enderror"
                  placeholder="4-digit AngleOne MPIN"
                />
                <span class="input-group-text cursor-pointer" id="toggleMpin">
                  <i class="icon-base ti tabler-eye-off"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="Enter your 4-digit PIN used for logging into the AngleOne broker account"
                  ></i>
                </span>
              </div>

              @error('pin')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>


            {{-- TOTP Secret --}}
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="totp_secret">
                  TOTP Secret <span class="text-danger">*</span>
                </label>
                <a
                  href="https://smartapi.angelbroking.com/enable-totp"
                  target="_blank"
                  class="small text-primary text-decoration-none"
                >
                  Enable TOTP & Generate secrete
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="totp_secret"
                  name="totp_secret"
                  class="form-control @error('totp_secret') is-invalid @enderror"
                  value="{{ old('totp_secret') }}"
                  placeholder="Enter AngleOne TOTP secret"
                  autocomplete="off"
                />

                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Enter the TOTP secret from AngleOne used to generate time-based OTPs"
                  ></i>
                </span>
              </div>

              @error('totp_secret')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- API Key --}}
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0" for="api_key">
                  API Key <span class="text-danger">*</span>
                </label>

                <div class="small">
                  <a
                    href="https://smartapi.angelbroking.com/signup"
                    target="_blank"
                    class="text-primary text-decoration-none"
                    title="Create a new account and generate your API key"
                  >
                    New Signup
                  </a>

                  <span class="mx-1 text-muted">|</span>

                  <a
                    href="https://smartapi.angelbroking.com/signin#"
                    target="_blank"
                    class="text-primary text-decoration-none"
                    title="Login to your Angel One account to view or regenerate your API key"
                  >
                    Existing User
                  </a>
                </div>
              </div>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="api_key"
                  name="api_key"
                  class="form-control @error('api_key') is-invalid @enderror"
                  value="{{ old('api_key') }}"
                  placeholder="Paste your AngleOne API key here"
                  autocomplete="off"
                />

                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Generate this API key from AngleOne → Developer / API Settings section"
                  ></i>
                </span>
              </div>

              @error('api_key')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Client Secret (Optional) --}}
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="client_secret">
                  Client Secret <span class="text-muted">(optional)</span>
                </label>
                <a
                  href="https://smartapi.angelbroking.com/apps"
                  target="_blank"
                  class="small text-primary text-decoration-none"
                >
                  Get Client Secrete
                </a>
              </div>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="client_secret"
                  name="client_secret"
                  class="form-control @error('client_secret') is-invalid @enderror"
                  value="{{ old('client_secret') }}"
                  placeholder="Paste AngleOne client secret (if applicable)"
                  autocomplete="off"
                />

                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Some AngleOne integrations require a client secret. Leave this empty if not provided by your broker."
                  ></i>
                </span>
              </div>

              @error('client_secret')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>


            {{-- Submit --}}
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">
                Create Account
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
