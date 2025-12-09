@extends('layouts/layoutMaster')

@section('title', 'Account List')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js'
  ])
@endsection

@section('page-script')
  @vite('resources/assets/js/app-user-accounts.js')
@endsection

@section('content')

  <!-- Filters Card -->
  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Filters</h5>

      <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
        <div class="col-md-4 user_role"></div>   <!-- Status Filter -->
        <div class="col-md-4 user_plan"></div>   <!-- Empty for UI alignment -->
        <div class="col-md-4 user_status"></div> <!-- Active Filter -->
      </div>
    </div>

    <div class="card-datatable table-responsive">
      <table class="datatables-users table">
        <thead class="border-top">
        <tr>
          <th></th>
          <th>Nickname</th>
          <th>Account</th>
          <th>Status</th>
          <th>Token Expiry</th>
          <th>Last Login</th>
          <th>Actions</th>
        </tr>
        </thead>
      </table>
    </div>
  </div>

  @include('components.add-account')

@endsection
