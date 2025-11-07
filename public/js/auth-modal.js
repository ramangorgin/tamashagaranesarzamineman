function toEnglishDigits(str) {
    return str.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
              .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
}

document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.getElementById('phoneInput');
    const sendBtn = document.getElementById('sendOtpBtn');
    const otpSection = document.getElementById('otpSection');
    const otpInput = document.getElementById('otpInput');
    const verifyBtn = document.getElementById('verifyOtpBtn');
    const resendBtn = document.getElementById('resendOtpBtn');
    const otpStatus = document.getElementById('otpStatus');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');

    // 🔹 تبدیل خودکار ارقام فارسی/عربی به انگلیسی
    phoneInput.addEventListener('input', e => {
        const val = e.target.value;
        e.target.value = toEnglishDigits(val.replace(/[^\d]/g, ''));
    });

    // 🔹 ارسال OTP
    sendBtn.addEventListener('click', () => {
        const phone = phoneInput.value;
        if (!/^09\d{9}$/.test(phone)) {
            otpStatus.textContent = "شماره تلفن معتبر نیست.";
            otpStatus.classList.add("text-danger");
            return;
        }
        otpStatus.textContent = "در حال ارسال کد...";
        fetch('/otp/send', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type': 'application/json'},
            body: JSON.stringify({phone})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                otpSection.classList.remove('d-none');
                startTimer(resendBtn, 60);
                otpStatus.textContent = "کد تایید ارسال شد ✅";
                otpStatus.classList.remove("text-danger");
            } else {
                otpStatus.textContent = "خطا در ارسال کد";
                otpStatus.classList.add("text-danger");
            }
        });
    });

    // 🔹 بررسی OTP
    verifyBtn.addEventListener('click', () => {
        const code = otpInput.value;
        const phone = phoneInput.value;
        fetch('/otp/verify', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Content-Type': 'application/json'},
            body: JSON.stringify({phone, code})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                otpStatus.textContent = "کد تایید شد ✅";
                finalSubmitBtn.classList.remove('d-none');
            } else {
                otpStatus.textContent = "کد اشتباه است.";
            }
        });
    });

    // 🔹 تایمر ارسال مجدد
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
