@props(['account'])

<style>
  .gradient-banner {
    height: 45px;
    background: linear-gradient(
      to right,
      color-mix(in sRGB, var(--bs-primary) 50%, var(--bs-paper-bg)),
      color-mix(in sRGB, var(--bs-success) 50%, var(--bs-paper-bg))
    );
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card mb-6">

      {{-- Gradient Banner --}}
      <div class="user-profile-header-banner gradient-banner rounded-top"></div>

      {{-- Profile Header --}}
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">

        {{-- Avatar --}}
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img
            src="{{ $account->avatar_url ?? asset('assets/img/avatars/1.png') }}"
            alt="user image"
            class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img"
          />
        </div>

        {{-- User Info --}}
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div
            class="d-flex align-items-md-end align-items-sm-start align-items-center
                   justify-content-md-between justify-content-start
                   mx-5 flex-md-row flex-column gap-4">

            <div class="user-profile-info">

              {{-- Nickname --}}
              <h4 class="mb-1 mt-lg-6">
                {{ $account->nickname ?? '—' }}
              </h4>

              {{-- Full Name --}}
              <p class="text-muted mb-2">
                {{ $account->account_name ?? '—' }}
              </p>

              <ul
                class="list-inline mb-0 d-flex align-items-center flex-wrap
                       justify-content-sm-start justify-content-center gap-4 my-2">

                {{-- Client ID --}}
                @if(!empty($account->client_id))
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-id icon-lg"></i>
                    <span class="fw-medium">
                      {{ $account->client_id }}
                    </span>
                  </li>
                @endif

                {{-- Email --}}
                @if(!empty($account->email))
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-mail icon-lg"></i>
                    <span class="fw-medium">{{ $account->email }}</span>
                  </li>
                @endif

                {{-- Mobile --}}
                @if(!empty($account->mobile))
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-phone icon-lg"></i>
                    <span class="fw-medium">{{ $account->mobile }}</span>
                  </li>
                @endif

                {{-- Joined Date --}}
                @if(!empty($account->created_at))
                  <li class="list-inline-item d-flex gap-2 align-items-center">
                    <i class="icon-base ti tabler-calendar icon-lg"></i>
                    <span class="fw-medium">
                      Joined {{ $account->created_at->format('d F Y') }}
                    </span>
                  </li>
                @endif

              </ul>
            </div>

            {{-- Status Button --}}
            <a href="#"
               class="btn btn-primary mb-1">
              <i class="icon-base ti tabler-edit icon-xs me-2"></i>
              Edit Account
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
