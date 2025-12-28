@extends('layouts/layoutMaster')

@section('title', 'eCommerce Customer Details Overview - Apps')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('content')

  {{-- Account Header --}}
  <x-account-header :account="$account" />

  <div class="row">
    <div class="col-12">

      {{-- Account Breadcrumb --}}
      @include('components.account-breadcrumb')

      {{-- Launching Soon Card --}}
      <div class="container-xxl container-p-y py-4">
        <div class="row justify-content-center align-items-center min-vh-75">

          <div class="col-xl-12 col-lg-7 col-md-9 col-sm-11">

            <div class="card shadow-sm text-center">
              <div class="card-body py-8 px-6">

                <h4 class="mb-2">
                  We are launching soon 🚀
                </h4>

                <p class="mb-6 text-muted">
                  Our website is opening soon. Please register to get notified when it's ready!
                </p>

                {{-- Illustration --}}
                <div class="mt-6">
                  <img
                    src="{{ asset('assets/img/illustrations/page-misc-launching-soon.png') }}"
                    alt="Launching Soon"
                    width="260"
                    class="img-fluid"
                  />
                </div>

              </div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>

@endsection


