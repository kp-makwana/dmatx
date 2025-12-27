@extends('layouts/layoutMaster')

@section('title', 'Profile Almost Completed')

@section('content')
  <div class="row justify-content-center">
    <div class="col-xl-6">

      <div class="card text-center">
        <div class="card-header">
          <h5 class="mb-1">Almost There! 🎉</h5>
          <p class="text-muted mb-0">
            Your profile is nearly complete
          </p>
        </div>

        <div class="card-body">

          <div class="mb-4">
            <p class="fs-5 mb-2">
              You’ve successfully verified your details.
            </p>

            <p class="text-muted">
              To finish setting up your account, we need to create secure API access.
              This will allow seamless and safe integration with our platform.
            </p>
          </div>

          <div class="alert alert-info text-start mb-4">
            <ul class="mb-0">
              <li>🔐 Your data remains fully encrypted</li>
              <li>⚡ API setup takes only a few seconds</li>
              <li>✅ Required to complete your profile</li>
            </ul>
          </div>

          <form method="POST"
                action="{{ route('angle-one.submit.step.five',$account->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary px-6">
              Create API & Continue
            </button>
          </form>

        </div>
      </div>

    </div>
  </div>
@endsection
