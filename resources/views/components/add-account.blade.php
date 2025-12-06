<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-add-account">
    <div class="modal-content">
      <div class="modal-body">

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        <div class="text-center mb-6">
          <h4 class="mb-2">Add New Smart-API Account</h4>
          <p>Enter your Angel Smart-API credentials securely.</p>
        </div>

        <form id="addAccountForm"
              class="row g-6"
              method="POST"
              action="{{ route('accounts.store') }}">
          @csrf

          <div class="col-12 col-md-6">
            <label class="form-label" for="nickname">Account Nickname <span class="text-danger">*</span></label>
            <input type="text" id="nickname" name="nickname" class="form-control" placeholder="My Trading Account"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="client_id">Client ID <span class="text-danger">*</span></label>
            <input type="text" id="client_id" name="client_id" class="form-control" placeholder="Enter Client ID"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="pin">Angel 4 Digit Login Pin <span class="text-danger">*</span></label>
            <input type="text" id="pin" name="pin" class="form-control" placeholder="Enter 4 Digit Pin"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="api_key">API Key <span class="text-danger">*</span></label>
            <input type="text" id="api_key" name="api_key" class="form-control" placeholder="Enter API Key"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="client_secret">Client Secret</label>
            <input type="text" id="client_secret" name="client_secret" class="form-control" placeholder="Enter Client Secret"/>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="totp_secret">TOTP Secret</label>
            <input type="text" id="totp_secret" name="totp_secret" class="form-control" placeholder="Enter TOTP Secret"/>
          </div>

          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-3">Save Account</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>
