@extends('layouts.admin-login')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container d-flex justify-content-center align-items-center">
  <div class="card shadow-lg border-0 p-4 position-relative overflow-hidden login-card">
      <h4 class="text-center mb-4 text-primary fw-bold fade-in">ورود مدیر</h4>

      {{-- پیام‌ها --}}
      <div id="messageBox" class="mb-3">
          @if(session('error'))
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  if (window.showError) {
                    window.showError('{{ addslashes(session('error')) }}');
                  }
                });
              </script>
          @endif
          @if(session('success'))
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  if (window.showSuccess) {
                    window.showSuccess('{{ addslashes(session('success')) }}');
                  }
                });
              </script>
          @endif
          @if ($errors->any())
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  @foreach ($errors->all() as $error)
                    if (window.showError) {
                      window.showError('{{ addslashes($error) }}');
                    }
                  @endforeach
                });
              </script>
          @endif
      </div>

      {{-- مرحله ۱: درخواست کد --}}
      <form id="requestOtpForm" class="step-form">
          @csrf
          <div class="mb-3">
              <label for="phone" class="form-label fw-semibold">شماره تلفن</label>
              <input type="text" name="phone" id="phone" class="form-control text-center" placeholder="مثلاً 09123456789" required autocomplete="tel">
          </div>
          <button type="submit" id="sendBtn" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
              <span class="btn-text">ارسال کد تایید</span>
              <span class="spinner-border spinner-border-sm d-none" id="sendSpinner"></span>
          </button>
      </form>

      {{-- مرحله ۲: ورود کد و تأیید (نمایش پس از ارسال موفق) --}}
      <form id="verifyForm" method="POST" action="{{ route('login.verify', ['role' => $role]) }}" class="mt-4 step-form d-none">
          @csrf
          <input type="hidden" name="phone" id="verify_phone">
          <div class="mb-3 position-relative">
              <label for="code" class="form-label fw-semibold">کد تأیید ارسال‌شده</label>
              <input type="text" name="code" id="code" class="form-control text-center tracking-wider" placeholder="******" required maxlength="6" autocomplete="one-time-code">
              <div class="form-text mt-2 small d-flex justify-content-between align-items-center">
                  <span id="countdown" class="text-secondary fw-semibold">03:00</span>
                  <button type="button" id="resendBtn" class="btn btn-link px-0 text-decoration-none disabled" disabled>ارسال مجدد</button>
              </div>
          </div>
          <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
              <span class="btn-text">تأیید و ورود</span>
              <span class="verify-anim d-none" id="verifyAnim">
                  <span class="spinner-grow spinner-grow-sm"></span>
              </span>
          </button>
      </form>

      {{-- لایه‌های تزئینی --}}
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
  </div>
</div>

<style>
/* زیبا سازی و انیمیشن‌ها */
.login-card {
  max-width: 420px;
  width: 100%;
  background: linear-gradient(135deg,#ffffff,#f7fbff 55%,#eef6ff);
  border-radius: 20px;
}
.fade-in { animation: fadeIn .6s ease; }
@keyframes fadeIn { from {opacity:0; transform:translateY(10px)} to {opacity:1; transform:translateY(0)} }
.step-form { animation: scaleIn .5s ease; }
@keyframes scaleIn { from {opacity:0; transform:scale(.95)} to {opacity:1; transform:scale(1)} }
.floating-shape {
  position:absolute;
  width:110px;
  height:110px;
  border-radius:50%;
  filter:blur(18px);
  opacity:.35;
  animation: float 9s linear infinite;
  z-index:-1;
}
.shape-1 { top:-40px; right:-30px; background:#6fa8ff; animation-delay:0s; }
.shape-2 { bottom:-40px; left:-30px; background:#ffca6f; animation-delay:3s; }
@keyframes float {
  0% { transform:translateY(0) scale(1); }
  50% { transform:translateY(25px) scale(1.08); }
  100% { transform:translateY(0) scale(1); }
}
#code { letter-spacing: .35em; font-weight: 600; }
.btn { border-radius: 12px; transition: background .3s, transform .25s; }
.btn-primary:hover { transform: translateY(-2px); }
.btn-success:hover { transform: translateY(-2px); }
.disabled { opacity:.5 !important; pointer-events:none; }
.alert { animation: fadeIn .4s ease; border-radius: 12px; }
#countdown { min-width: 60px; text-align:center; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const requestForm = document.getElementById('requestOtpForm');
    const verifyForm = document.getElementById('verifyForm');
    const phoneInput = document.getElementById('phone');
    const verifyPhoneInput = document.getElementById('verify_phone');
    const sendBtn = document.getElementById('sendBtn');
    const sendSpinner = document.getElementById('sendSpinner');
    const messageBox = document.getElementById('messageBox');
    const countdownEl = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');
    let timerId = null;
    const COUNTDOWN_SECONDS = 180;

    function showMessage(type, text, list) {
        const el = document.createElement('div');
        el.className = 'alert alert-' + type;
        if (list && Array.isArray(list)) {
            const ul = document.createElement('ul');
            ul.className = 'mb-0';
            list.forEach(li => {
                const item = document.createElement('li');
                item.textContent = li;
                ul.appendChild(item);
            });
            el.appendChild(ul);
        } else {
            el.textContent = text;
        }
        messageBox.innerHTML = '';
        messageBox.appendChild(el);
    }

    function formatTime(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2,'0');
        const s = (sec % 60).toString().padStart(2,'0');
        return m + ':' + s;
    }

    function startCountdown() {
        clearInterval(timerId);
        let remaining = COUNTDOWN_SECONDS;
        countdownEl.textContent = formatTime(remaining);
        resendBtn.classList.add('disabled');
        resendBtn.setAttribute('disabled','disabled');

        timerId = setInterval(() => {
            remaining--;
            countdownEl.textContent = formatTime(remaining);
            if (remaining <= 0) {
                clearInterval(timerId);
                countdownEl.textContent = '00:00';
                resendBtn.classList.remove('disabled');
                resendBtn.removeAttribute('disabled');
            }
        }, 1000);
    }

    function sendOtp(phone) {
        sendBtn.disabled = true;
        sendSpinner.classList.remove('d-none');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('login.sendOtp', ['role' => $role]) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json, text/html',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ phone })
        })
        .then(async resp => {
            const contentType = resp.headers.get('Content-Type') || '';
            if (contentType.includes('application/json')) {
                return resp.json().then(data => ({ ok: resp.ok, data }));
            } else {
                return resp.text().then(text => ({ ok: resp.ok, text }));
            }
        })
        .then(result => {
            // Handle JSON response
            if (result.data) {
                if (result.data.success) {
                    const message = result.data.message || 'کد تایید با موفقیت ارسال شد. لطفا آن را وارد کنید.';
                    showMessage('success', message);
                    verifyPhoneInput.value = phone;
                    verifyForm.classList.remove('d-none');
                    verifyForm.classList.add('fade-in');
                    startCountdown();
                    phoneInput.setAttribute('disabled','disabled');
                    return;
                } else if (result.data.error) {
                    showMessage('danger', result.data.error);
                    return;
                } else if (result.data.errors) {
                    const errorList = Array.isArray(result.data.errors) 
                        ? result.data.errors 
                        : Object.values(result.data.errors).flat();
                    showMessage('danger', '', errorList);
                    return;
                }
            }
            
            // Handle HTML response (fallback)
            if (result.text) {
                let success = false;
                const errors = [];
                
                // More permissive checks for success
                if (result.ok) { // If HTTP status is 200-299, assume success if no obvious error
                     success = true;
                }
                
                // Specific text checks just in case
                if (result.text.includes('کد تایید') || result.text.includes('success') || result.text.includes('موفقیت')) {
                    success = true;
                }
                
                if (result.text.includes('ارسال پیامک با خطا')) {
                    success = false;
                    errors.push('ارسال پیامک با خطا مواجه شد.');
                }
                if (result.text.includes('نقش نامعتبر')) {
                    success = false;
                    errors.push('نقش نامعتبر است.');
                }
                
                if (success) {
                    // Try to extract code from message if visible in text (for local dev convenience)
                    const match = result.text.match(/کد:?\s*(\d{6})/);
                    const msg = match ? 'کد تایید: ' + match[1] : 'کد تایید با موفقیت ارسال شد.';
                    
                    showMessage('success', msg);
                    verifyPhoneInput.value = phone;
                    verifyForm.classList.remove('d-none');
                    verifyForm.classList.add('fade-in');
                    startCountdown();
                    phoneInput.setAttribute('disabled','disabled');
                } else if (errors.length) {
                    showMessage('danger', '', errors);
                } else {
                    // Show raw text if short, to help debugging
                    const raw = result.text.length < 100 ? result.text : 'خطای ناشناخته.';
                    showMessage('danger', 'خطای ناشناخته در ارسال کد: ' + raw);
                }
                return;
            }
            
            // If we get here, something unexpected happened
            showMessage('danger', 'خطای ناشناخته در ارسال کد.');
        })
        .catch(() => {
            showMessage('danger', 'ارتباط با سرور برقرار نشد.');
        })
        .finally(() => {
            sendSpinner.classList.add('d-none');
            sendBtn.disabled = false;
        });
    }

    requestForm.addEventListener('submit', e => {
        e.preventDefault();
        const phone = phoneInput.value.trim();
        // Removed regex validation to allow any input in local/debug
        if (phone.length < 10) {
            showMessage('danger', 'لطفا شماره تلفن معتبر وارد کنید.');
            return;
        }
        sendOtp(phone);
    });

    resendBtn.addEventListener('click', () => {
        if (resendBtn.classList.contains('disabled')) return;
        showMessage('info', 'در حال ارسال مجدد کد تایید...');
        sendOtp(verifyPhoneInput.value);
    });

    // انتخاب خودکار کل فیلد کد هنگام فوکوس برای سهولت
    document.getElementById('code').addEventListener('focus', function() {
        this.select();
    });

    // انیمیشن کوچک روی دکمه تأیید
    verifyForm.addEventListener('submit', () => {
        document.getElementById('verifyAnim').classList.remove('d-none');
    });
});
</script>
@endsection
