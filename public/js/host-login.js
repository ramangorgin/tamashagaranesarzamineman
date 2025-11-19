// ===============================================
// Convert Persian/Arabic digits to English
// ===============================================
function toEnglishDigits(str) {
  return str.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
}

// ===============================================
// DOM Ready
// ===============================================
document.addEventListener('DOMContentLoaded', () => {

  // Elements
  const phoneInput = document.getElementById('hostPhone');
  const sendBtn = document.getElementById('sendHostOtp');
  const resendBtn = document.getElementById('resendHostOtp');
  const otpInput = document.getElementById('hostOtpCode');
  const verifyBtn = document.getElementById('verifyHostOtp');
  const hostOtpStatus = document.getElementById('hostOtpStatus');
  const hostVerifyStatus = document.getElementById('hostVerifyStatus');
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // Ensure digits are English
  phoneInput.addEventListener('input', e => {
    e.target.value = toEnglishDigits(e.target.value.replace(/[^\d]/g, ''));
  });

  // ===============================================
  //  ارسال کد تأیید
  // ===============================================
  sendBtn.addEventListener('click', () => {
    const phone = phoneInput.value.trim();
    hostOtpStatus.textContent = '';
    hostOtpStatus.classList.remove('text-danger', 'text-success');

    if (!/^09\d{9}$/.test(phone)) {
      hostOtpStatus.textContent = 'شماره معتبر نیست.';
      hostOtpStatus.classList.add('text-danger');
      return;
    }

    fetch(sendOtpUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf
      },
      body: JSON.stringify({ phone })
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (data.success) {
        hostOtpStatus.textContent = 'کد تایید ارسال شد ✅';
        hostOtpStatus.classList.add('text-success');
        document.getElementById('step1').classList.add('d-none');
        document.getElementById('step2').classList.remove('d-none');
        startTimer(resendBtn, 60);
      } else {
        hostOtpStatus.textContent = data.message || 'خطا در ارسال کد.';
        hostOtpStatus.classList.add('text-danger');
      }
    })
    .catch(err => {
      console.error('OTP Send Error:', err);
      hostOtpStatus.textContent = 'خطای ارتباط با سرور.';
      hostOtpStatus.classList.add('text-danger');
    });
  });

  // ===============================================
  //  تایید کد
  // ===============================================
  verifyBtn.addEventListener('click', () => {
    const code = otpInput.value.trim();
    const phone = phoneInput.value.trim();
    hostVerifyStatus.textContent = '';
    hostVerifyStatus.classList.remove('text-danger', 'text-success');

    if (code.length !== 6) {
      hostVerifyStatus.textContent = 'کد ۶ رقمی را وارد کنید.';
      hostVerifyStatus.classList.add('text-danger');
      return;
    }

    fetch(verifyOtpUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf
      },
      body: JSON.stringify({ phone, code })
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (data.success) {
        hostVerifyStatus.textContent = 'کد تایید شد ✅';
        hostVerifyStatus.classList.add('text-success');
        document.getElementById('step2').classList.add('d-none');
        document.getElementById('step3').classList.remove('d-none');
      } else {
        hostVerifyStatus.textContent = data.message || 'کد اشتباه است.';
        hostVerifyStatus.classList.add('text-danger');
      }
    })
    .catch(err => {
      console.error('OTP Verify Error:', err);
      hostVerifyStatus.textContent = 'خطای ارتباط با سرور.';
      hostVerifyStatus.classList.add('text-danger');
    });
  });

  // ===============================================
  //  تایمر ارسال مجدد
  // ===============================================
  function startTimer(btn, sec) {
    btn.disabled = true;
    let count = sec;
    btn.textContent = `ارسال مجدد (${count}s)`;

    const interval = setInterval(() => {
      count--;
      btn.textContent = `ارسال مجدد (${count}s)`;
      if (count <= 0) {
        clearInterval(interval);
        btn.disabled = false;
        btn.textContent = "ارسال مجدد";
      }
    }, 1000);
  }
});
