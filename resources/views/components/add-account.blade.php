<!-- Add AngleOne Account Modal -->
<div class="modal fade" id="addAngleOneAccount" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

        <div class="text-center mb-4">
          <h4 class="mb-2">Add AngleOne Account</h4>
          <p>Select how you want to add your account</p>
        </div>

        <form id="addAccountMethodForm">

          <!-- SINGLE ROW OPTIONS -->
          <div class="row g-4 mb-4">

            <!-- AUTO ADD VIA OTP -->
            <div class="col-md-6">
              <div class="form-check custom-option custom-option-icon h-100">
                <label class="form-check-label custom-option-content h-100">
                  <input
                    class="form-check-input d-none"
                    type="radio"
                    name="accountMethod"
                    value="otp"
                    checked
                  />
                  <span class="custom-option-body text-center">
                    <!-- ICON -->
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                         xmlns="http://www.w3.org/2000/svg" class="mb-2">
                      <path
                        d="M3.5 7C3.5 5.343 4.843 4 6.5 4H21.5C23.157 4 24.5 5.343 24.5 7V21C24.5 22.657 23.157 24 21.5 24H6.5C4.843 24 3.5 22.657 3.5 21V7Z"
                        stroke="currentColor" stroke-width="2" />
                      <path
                        d="M8 14H20M8 10H20M8 18H14"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" />
                    </svg>

                    <span class="custom-option-title d-block">
                      Auto Add via OTP
                    </span>
                    <small class="d-block">
                      Login using mobile and email OTP
                    </small>
                  </span>
                </label>
              </div>
            </div>

            <!-- ADD VIA API + TOTP -->
            <div class="col-md-6">
              <div class="form-check custom-option custom-option-icon h-100">
                <label class="form-check-label custom-option-content h-100">
                  <input
                    class="form-check-input d-none"
                    type="radio"
                    name="accountMethod"
                    value="api"
                  />
                  <span class="custom-option-body text-center">
                    <!-- ICON -->
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                         xmlns="http://www.w3.org/2000/svg" class="mb-2">
                      <path
                        d="M14 3.5L22.5 8.5V19.5L14 24.5L5.5 19.5V8.5L14 3.5Z"
                        stroke="currentColor" stroke-width="2" />
                      <path
                        d="M14 14V24.5M22.5 8.5L14 14L5.5 8.5"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" />
                    </svg>

                    <span class="custom-option-title d-block">
                      API Key & TOTP Secrete
                    </span>
                    <small class="d-block">
                      Use existing API key & TOTP Secrete
                    </small>
                  </span>
                </label>
              </div>
            </div>

          </div>

          <!-- NEXT BUTTON -->
          <div class="text-center">
            <button type="button" class="btn btn-primary px-5" id="nextBtn">
              Next
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById("nextBtn").addEventListener("click", function () {
    const selected = document.querySelector(
      'input[name="accountMethod"]:checked'
    ).value;

    if (selected === "otp") {
      window.location.href = "{{ route('angle-one.create.step.one') }}";
    } else {
      window.location.href = "{{ route('accounts.create') }}";
    }
  });
</script>
