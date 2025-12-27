@extends('layouts/layoutMaster')

@section('title', 'Account settings - Account')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

<!-- Page Scripts -->
@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {

      document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.js-delete-account');
        if (!btn) return;

        const form = btn.closest('form');
        const name = btn.dataset.name || 'this account';

        Swal.fire({
          title: 'Are you sure?',
          text: `Are you sure you want to delete ${name}?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel',
          customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });

    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      const uploadInput = document.getElementById('upload');
      const avatarImg = document.getElementById('uploadedAvatar');

      if (!uploadInput || !avatarImg) return;

      const originalSrc = avatarImg.src;
      const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

      uploadInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.size > MAX_SIZE) {
          Swal.fire({
            icon: 'error',
            title: 'Image too large',
            text: 'Please upload an image smaller than 5 MB.',
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false
          });
          this.value = '';
          avatarImg.src = originalSrc;
          return;
        }

        if (!file.type.startsWith('image/')) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid file',
            text: 'Please upload a valid image file.',
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false
          });
          this.value = '';
          avatarImg.src = originalSrc;
          return;
        }

        const reader = new FileReader();
        reader.onload = e => avatarImg.src = e.target.result;
        reader.readAsDataURL(file);
      });
    });
  </script>
@endsection

@section('content')
  <x-account-header :account="$account" />

  <div class="row">
    <div class="col-md-12">
      @include('components.account-breadcrumb')

      <div class="card mb-6">
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-6">
            <img
              src="{{ asset('assets/img/avatars/1.png') }}"
              alt="user-avatar"
              class="d-block w-px-100 h-px-100 rounded"
              id="uploadedAvatar"
            />

            <div class="button-wrapper">
              <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                <span class="d-none d-sm-block">Upload new photo</span>
                <i class="icon-base ti tabler-upload d-block d-sm-none"></i>
              </label>

              <a href="{{ url()->current() }}" type="button" class="btn btn-label-secondary account-image-reset mb-4">
                <i class="icon-base ti tabler-reset d-block d-sm-none"></i>
                <span class="d-none d-sm-block">Reset</span>
              </a>

              <div>Allowed JPG, GIF or PNG. Max size of 5 MB</div>
            </div>
          </div>
        </div>

        <div class="card-body pt-4">
          <form
            id="formAccountSettings"
            method="POST"
            action="{{ route('accounts.update', $account->id) }}"
            enctype="multipart/form-data"
          >
            @csrf
            @method('PUT')

            {{-- FILE INPUT MOVED INSIDE FORM (CRITICAL FIX) --}}
            <input
              type="file"
              id="upload"
              name="profile_image"
              class="account-file-input"
              hidden
              accept="image/png, image/jpeg"
            />

            <div class="row gy-4 gx-6 mb-6">

              {{-- Nickname --}}
              <div class="col-md-6">
                <label class="form-label">Nickname <span class="text-danger">*</span></label>
                <input
                  type="text"
                  name="nickname"
                  class="form-control @error('nickname') is-invalid @enderror"
                  value="{{ old('nickname', $account->nickname) }}"
                />
                @error('nickname')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              {{-- PIN --}}
              <div class="col-md-6">
                <label class="form-label">PIN</label>
                <input
                  type="password"
                  name="pin"
                  maxlength="4"
                  class="form-control @error('pin') is-invalid @enderror"
                />
                <small class="text-muted">Leave blank to keep existing PIN</small>
              </div>

              {{-- API Key --}}
              <div class="col-md-6">
                <label class="form-label">API Key</label>
                <input
                  type="text"
                  name="api_key"
                  class="form-control @error('api_key') is-invalid @enderror"
                />
                <small class="text-muted">Leave blank to keep existing API Key</small>
              </div>

              {{-- Client Secret --}}
              <div class="col-md-6">
                <label class="form-label">Client Secret</label>
                <input
                  type="password"
                  name="client_secret"
                  class="form-control @error('client_secret') is-invalid @enderror"
                />
                <small class="text-muted">Leave blank to keep existing secret</small>
              </div>

            </div>

            <div class="d-flex justify-content-end gap-2">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      {{-- DELETE ACCOUNT (UNCHANGED) --}}
      <div class="card">
        <h5 class="card-header">Delete Account</h5>
        <div class="card-body">
          <div class="alert alert-warning">
            <h5 class="alert-heading mb-1">Are you sure?</h5>
            <p class="mb-0">This action cannot be undone.</p>
          </div>

          <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="w-50">
            @csrf
            @method('DELETE')
            <button
              type="button"
              class="btn btn-danger w-25 js-delete-account"
              data-name="{{ $account->nickname ?: $account->client_id }}"
            >
              Delete Customer
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
@endsection
