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
          <form method="POST" action="{{ route('angle-one.submit.step.one') }}" class="row g-4">
            @csrf

            {{-- Full Name --}}
            <div class="col-md-6">
              <label class="form-label" for="account_name">
                Full Name <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="account_name"
                  name="account_name"
                  class="form-control @error('account_name') is-invalid @enderror"
                  value="{{ old('account_name') }}"
                  placeholder="e.g. John Deo"
{{--                  autocomplete="off"--}}
                />
                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    title="Enter your full name as registered with AngelOne"
                  ></i>
                </span>
              </div>

              @error('account_name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
              <label class="form-label" for="email">
                Email <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}"
                  placeholder="e.g. abc@test.com"
{{--                  autocomplete="off"--}}
                />
                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    title="Enter the email linked with your AngelOne account"
                  ></i>
                </span>
              </div>

              @error('email')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Mobile Number --}}
            <div class="col-md-6">
              <label class="form-label" for="mobile">
                Mobile Number <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="mobile"
                  name="mobile"
                  maxlength="10"
                  class="form-control @error('mobile') is-invalid @enderror"
                  value="{{ old('mobile') }}"
                  placeholder="e.g. 9876543210"
{{--                  autocomplete="off"--}}
                />
                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    title="Enter the mobile number registered with AngelOne"
                  ></i>
                </span>
              </div>

              @error('mobile')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Client Code --}}
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
                  value="{{ old('client_id') }}"
                  placeholder="e.g. AABBCC11"
{{--                  autocomplete="off"--}}
                />
                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    title="Enter the Client Code provided by AngelOne (Profile / Dashboard)"
                  ></i>
                </span>
              </div>

              @error('client_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Password --}}
            <div class="col-md-6">
              <label class="form-label" for="password">
                Password <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control @error('password') is-invalid @enderror"
                  placeholder="Enter strong password"
                />
                <span class="input-group-text cursor-pointer">
                  <i
                    class="icon-base ti tabler-help-circle text-body-secondary"
                    data-bs-toggle="tooltip"
                    title="Password must be at least 8 characters and include uppercase, lowercase, number, and special character"
                  ></i>
                </span>
              </div>

              @error('password')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="col-md-6">
              <label class="form-label" for="password_confirmation">
                Confirm Password <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="password"
                  id="password_confirmation"
                  name="password_confirmation"
                  class="form-control"
                  placeholder="Re-enter password"
                />
                <span class="input-group-text cursor-pointer">
        <i
          class="icon-base ti tabler-help-circle text-body-secondary"
          data-bs-toggle="tooltip"
          title="Re-enter the same password to confirm"
        ></i>
      </span>
              </div>
            </div>

            {{-- Actions --}}
            <div class="col-12">
              <div class="d-flex justify-content-end gap-2 w-100">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
                  <i class="ti tabler-arrow-left me-1"></i> Back
                </a>
                <button
                  type="submit"
                  class="btn btn-primary d-inline-flex align-items-center justify-content-center"
                  id="createAccountSubmitBtn">
                  <span class="btn-text">Create AngleOne SmartAPI Account</span>
                  <span class="spinner-border spinner-border-sm d-none ms-2"
                        role="status"
                        aria-hidden="true"></span>
                  <span class="btn-loading-text d-none ms-2">
                    Creating...
                  </span>
                </button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('form[action="{{ route('angle-one.submit.step.one') }}"]');
      if (!form) return;
      form.addEventListener('submit', () => {
        const btn = document.getElementById('createAccountSubmitBtn');
        if (!btn) return;

        btn.disabled = true;

        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.btn-loading-text').classList.remove('d-none');
      });
    });
  </script>
@endsection
