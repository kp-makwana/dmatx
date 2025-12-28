<div class="container-xxl container-p-y py-4">
  <div class="row justify-content-center align-items-center min-vh-75">

    <div class="col-xl-12">

      <div class="card shadow-sm text-center">
        <div class="card-body py-8 px-6">

          <h4 class="mb-2">
            We are launching soon 🚀
          </h4>

          <p class="mb-4 text-muted">
            Our website is opening soon.In case of any query, please contact us.
          </p>

          {{-- Contact Us Button --}}
          <a href="{{ route('home') }}#contact" class="btn btn-primary">
            Contact Us
          </a>

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
