'use strict';

document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     OTP INPUT HANDLING
  =============================== */

  document.querySelectorAll('.numeral-mask-wrapper').forEach(wrapper => {
    const inputs = wrapper.querySelectorAll('.numeral-mask');
    const type = wrapper.dataset.type;
    const hiddenInput = document.querySelector(`input[name="${type}_otp"]`);

    inputs.forEach((input, index) => {

      input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '');
        if (input.value && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
        updateOtp();
      });

      input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          inputs[index - 1].focus();
        }
      });

      input.addEventListener('paste', e => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData)
          .getData('text')
          .replace(/\D/g, '')
          .slice(0, inputs.length);

        inputs.forEach((inp, i) => inp.value = pasted[i] || '');
        updateOtp();
      });
    });

    function updateOtp() {
      hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
    }
  });

  /* ===============================
     RESEND OTP COUNTDOWN (FIXED)
  =============================== */

  const RESEND_TIME = 60;

  document.querySelectorAll('.resend-btn').forEach(btn => {

    const type = btn.dataset.type;
    const url = btn.dataset.url;
    const countdownEl = document.querySelector(`.countdown[data-type="${type}"]`);

    let timeLeft = RESEND_TIME;
    let timer = null;

    const formatTime = t => `00:${t < 10 ? '0' + t : t}`;

    const startCountdown = () => {
      clearInterval(timer); // 🔥 FIX
      countdownEl.textContent = `Resend OTP in ${formatTime(timeLeft)}`;

      timer = setInterval(() => {
        timeLeft--;

        countdownEl.textContent = `Resend OTP in ${formatTime(timeLeft)}`;

        if (timeLeft <= 0) {
          clearInterval(timer);
          countdownEl.textContent = 'Resend OTP';
          btn.disabled = false;
          btn.classList.remove('disabled');
        }
      }, 1000);
    };

    // Initial state
    btn.disabled = true;
    btn.classList.add('disabled');
    startCountdown();

    // Resend click
    btn.addEventListener('click', () => {
      timeLeft = RESEND_TIME;
      btn.disabled = true;
      btn.classList.add('disabled');
      startCountdown();

      $.ajax({
        url: url,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',

        success: function (response) {
          // ✅ SUCCESS TOAST
          showToast(
            response.message || 'OTP resent successfully',
            {
              type: 'success',
              effect: 'animate__slideInDown',
              bg: 'bg-success',
              icon: 'tabler-check'
            }
          );
        },

        error: function (xhr) {
          let message = 'Something went wrong';

          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }

          // ❌ ERROR TOAST
          showToast(
            message,
            {
              type: 'error',
              effect: 'animate__shakeX',
              bg: 'bg-danger',
              icon: 'tabler-x'
            }
          );
        }
      });
    });
  });
});
