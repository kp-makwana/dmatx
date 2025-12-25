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

            {{-- Full Name --}}
            <div class="col-md-6">
              <label class="form-label" for="name">
                Full Name <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="name"
                  name="name"
                  class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}"
                  placeholder="e.g. Kiran Kumar"
                  autocomplete="off"
                />
                <span class="input-group-text cursor-pointer">
        <i
          class="icon-base ti tabler-help-circle text-body-secondary"
          data-bs-toggle="tooltip"
          title="Enter your full name as registered with AngelOne"
        ></i>
      </span>
              </div>

              @error('name')
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
                  autocomplete="off"
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
              <label class="form-label" for="mobileno">
                Mobile Number <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="mobileno"
                  name="mobileno"
                  maxlength="10"
                  class="form-control @error('mobileno') is-invalid @enderror"
                  value="{{ old('mobileno') }}"
                  placeholder="e.g. 9876543210"
                  autocomplete="off"
                />
                <span class="input-group-text cursor-pointer">
        <i
          class="icon-base ti tabler-help-circle text-body-secondary"
          data-bs-toggle="tooltip"
          title="Enter the mobile number registered with AngelOne"
        ></i>
      </span>
              </div>

              @error('mobileno')
              <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Client Code --}}
            <div class="col-md-6">
              <label class="form-label" for="clientcode">
                Client Code <span class="text-danger">*</span>
              </label>

              <div class="input-group input-group-merge">
                <input
                  type="text"
                  id="clientcode"
                  name="clientcode"
                  class="form-control @error('clientcode') is-invalid @enderror"
                  value="{{ old('clientcode') }}"
                  placeholder="e.g. AABBCC11"
                  autocomplete="off"
                />
                <span class="input-group-text cursor-pointer">
        <i
          class="icon-base ti tabler-help-circle text-body-secondary"
          data-bs-toggle="tooltip"
          title="Enter the Client Code provided by AngelOne (Profile / Dashboard)"
        ></i>
      </span>
              </div>

              @error('clientcode')
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

            {{-- Submit --}}
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">
                Create AngleOne SmartAPI Account
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
