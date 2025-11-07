function toEnglishDigits(str) {
    return str.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
              .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
}

document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.getElementById('hostPhone');
    const sendBtn = document.getElementById('sendHostOtp');
    const resendBtn = document.getElementById('resendHostOtp');
    const otpInput = document.getElementById('hostOtpCode');
    const verifyBtn = document.getElementById('verifyHostOtp');
    const hostOtpStatus = document.getElementById('hostOtpStatus');
    const hostVerifyStatus = document.getElementById('hostVerifyStatus');

    phoneInput.addEventListener('input', e => {
        e.target.value = toEnglishDigits(e.target.value.replace(/[^\d]/g, ''));
    });

    sendBtn.addEventListener('click', () => {
        const phone = phoneInput.value;
        if (!/^09\d{9}$/.test(phone)) {
            hostOtpStatus.textContent = 'شماره معتبر نیست.';
            hostOtpStatus.classList.add('text-danger');
            return;
        }

        fetch('/otp/send', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type': 'application/json'},
            body: JSON.stringify({ phone })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('step1').classList.add('d-none');
                document.getElementById('step2').classList.remove('d-none');
                startTimer(resendBtn, 60);
                hostOtpStatus.textContent = 'کد ارسال شد ✅';
            } else {
                hostOtpStatus.textContent = 'خطا در ارسال کد';
            }
        });
    });

    verifyBtn.addEventListener('click', () => {
        const code = otpInput.value;
        const phone = phoneInput.value;
        fetch('/otp/verify', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type': 'application/json'},
            body: JSON.stringify({ phone, code })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hostVerifyStatus.textContent = 'کد تایید شد ✅';
                document.getElementById('step2').classList.add('d-none');
                document.getElementById('step3').classList.remove('d-none');
            } else {
                hostVerifyStatus.textContent = 'کد اشتباه است.';
            }
        });
    });

    function startTimer(btn, sec) {
        btn.disabled = true;
        let count = sec;
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
