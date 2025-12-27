<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sessionMap = {
      success: {
        type: 'success',
        effect: 'animate__slideInDown',
        bg: 'bg-success',
        icon: 'tabler-check'
      },
      error: {
        type: 'error',
        effect: 'animate__shakeX',
        bg: 'bg-danger',
        icon: 'tabler-x'
      },
      info: {
        type: 'info',
        effect: 'animate__bounceIn',
        bg: 'bg-info',
        icon: 'tabler-info-circle'
      },
      warning: {
        type: 'warning',
        effect: 'animate__headShake',
        bg: 'bg-warning',
        icon: 'tabler-alert-triangle'
      }
    };

    @foreach (['success', 'error', 'info', 'warning'] as $key)
    @if(session()->has($key))
    showToast(
      `{!! nl2br(e(session($key))) !!}`,
      sessionMap['{{ $key }}']
    );
    @endif
    @endforeach

  });

  /* ===== Toast Helper ===== */
  function showToast(message, config) {

    const html = `
    <div class="bs-toast toast toast-ex animate__animated ${config.effect} ${config.bg} my-2 fade"
         role="alert"
          style="margin-top: -1% !important;"
         aria-live="assertive"
         aria-atomic="true"
         data-bs-delay="3000">

      <div class="toast-header ${config.bg}">
        <i class="icon-base ti ${config.icon} me-2 text-white"></i>
        <div class="me-auto fw-medium text-white text-capitalize">
          ${config.type}
        </div>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="toast"></button>
      </div>

      <div class="toast-body text-white">
        ${message}
      </div>
    </div>
  `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    const toastEl = wrapper.firstChild;

    document.body.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl);
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  }
</script>
