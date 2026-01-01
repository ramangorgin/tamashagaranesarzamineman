@extends('layouts.app')

@section('title', 'ورود / ثبت‌نام میزبان | تماشاگران سرزمین من')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card shadow-lg border-0 rounded-4 position-relative overflow-hidden host-card">
        <div class="card-header bg-primary text-white text-center rounded-top-4 py-3">
          <h4 class="fw-bold mb-0"><i class="bi bi-house-door"></i> ورود / ثبت‌نام میزبان</h4>
        </div>

        <div class="card-body p-4">

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
            @if($errors->any())
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  @foreach($errors->all() as $e)
                    if (window.showError) {
                      window.showError('{{ addslashes($e) }}');
                    }
                  @endforeach
                });
              </script>
            @endif
          </div>

          <!-- مرحله ۱: ارسال کد -->
          <form id="requestOtpForm">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">شماره موبایل</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                <input type="tel" id="phone" name="phone" class="form-control text-center" placeholder="مثلاً 09123456789" required autocomplete="tel">
              </div>
            </div>
            <button type="submit" id="sendBtn" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
              <span>ارسال کد تایید</span>
              <span class="spinner-border spinner-border-sm d-none" id="sendSpinner"></span>
            </button>
          </form>

          <!-- مرحله ۲: ورود کد -->
          <form id="verifyForm" method="POST" action="{{ route('login.verify',['role'=>'host']) }}" class="mt-4 d-none">
            @csrf
            <input type="hidden" name="phone" id="verify_phone">
            <div class="mb-3">
              <label class="form-label fw-semibold">کد تایید</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="text" name="code" id="code" maxlength="6" class="form-control text-center" placeholder="******" required autocomplete="one-time-code">
              </div>
              <div class="d-flex justify-content-between align-items-center mt-2 small">
                <span id="countdown" class="text-secondary fw-semibold">03:00</span>
                <button type="button" id="resendBtn" class="btn btn-link px-0 text-decoration-none disabled" disabled>ارسال مجدد</button>
              </div>
            </div>
            <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
              <span>تایید و ورود</span>
              <span class="spinner-grow spinner-grow-sm d-none" id="verifySpinner"></span>
            </button>
          </form>

        </div>

        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.host-card{background:linear-gradient(135deg,#ffffff,#f5f9ff 55%,#eef4ff);border-radius:22px;}
.alert{animation:fadeIn .4s ease;border-radius:12px;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.floating-shape{position:absolute;width:110px;height:110px;border-radius:50%;filter:blur(18px);opacity:.35;animation:float 9s linear infinite;z-index:-1;}
.shape-1{top:-40px;right:-30px;background:#6fa8ff;}
.shape-2{bottom:-40px;left:-30px;background:#ffca6f;}
@keyframes float{0%{transform:translateY(0) scale(1)}50%{transform:translateY(25px) scale(1.08)}100%{transform:translateY(0) scale(1)}}
#code{letter-spacing:.35em;font-weight:600;}
.btn{border-radius:14px;transition:.25s;}
.btn-primary:hover,.btn-success:hover{transform:translateY(-2px);}
.disabled{opacity:.5!important;pointer-events:none;}
#countdown{min-width:60px;text-align:center;}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const requestForm = document.getElementById('requestOtpForm');
  const verifyForm   = document.getElementById('verifyForm');
  const phoneInput   = document.getElementById('phone');
  const verifyPhone  = document.getElementById('verify_phone');
  const sendBtn      = document.getElementById('sendBtn');
  const sendSpinner  = document.getElementById('sendSpinner');
  const messageBox   = document.getElementById('messageBox');
  const countdownEl  = document.getElementById('countdown');
  const resendBtn    = document.getElementById('resendBtn');
  const verifySpinner= document.getElementById('verifySpinner');
  const codeInput    = document.getElementById('code');

  const COUNTDOWN_SECONDS = 180;
  let timerId = null;

  function showMessage(type,text,list){
    const d=document.createElement('div');
    d.className='alert alert-'+type;
    if(list && Array.isArray(list)){
      const ul=document.createElement('ul');ul.className='mb-0';
      list.forEach(t=>{const li=document.createElement('li');li.textContent=t;ul.appendChild(li);});
      d.appendChild(ul);
    } else { d.textContent=text; }
    messageBox.innerHTML='';
    messageBox.appendChild(d);
  }

  function formatTime(sec){
    return String(Math.floor(sec/60)).padStart(2,'0')+':'+String(sec%60).padStart(2,'0');
  }

  function startCountdown(){
    clearInterval(timerId);
    let remain=COUNTDOWN_SECONDS;
    countdownEl.textContent=formatTime(remain);
    resendBtn.classList.add('disabled');resendBtn.disabled=true;
    timerId=setInterval(()=>{
      remain--; countdownEl.textContent=formatTime(remain);
      if(remain<=0){clearInterval(timerId);countdownEl.textContent='00:00';resendBtn.classList.remove('disabled');resendBtn.disabled=false;}
    },1000);
  }

  function sendOtp(phone){
    const csrf='{{ csrf_token() }}';
    sendBtn.disabled=true; sendSpinner.classList.remove('d-none');
    fetch("{{ route('login.sendOtp',['role'=>'host']) }}",{
      method:'POST',
      headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      body:JSON.stringify({phone})
    })
    .then(r=>r.json().catch(()=>({})).then(data=>({ok:r.ok,data})))
    .then(res=>{
      if(res.ok){
        showMessage('success','کد تایید ارسال شد. لطفاً وارد کنید.');
        verifyPhone.value=phone;
        verifyForm.classList.remove('d-none');
        startCountdown();
        phoneInput.disabled=true;
      } else {
        showMessage('danger', res.data.message || 'خطا در ارسال کد.');
      }
    })
    .catch(()=>showMessage('danger','ارتباط با سرور برقرار نشد.'))
    .finally(()=>{sendSpinner.classList.add('d-none');sendBtn.disabled=false;});
  }

  requestForm.addEventListener('submit', e=>{
    e.preventDefault();
    const phone=phoneInput.value.trim();
    if(!/^09\d{9}$/.test(phone)){
      showMessage('danger','شماره معتبر نیست (مثال: 09123456789).');return;
    }
    sendOtp(phone);
  });

  resendBtn.addEventListener('click', ()=>{
    if(resendBtn.disabled) return;
    showMessage('info','در حال ارسال مجدد کد...');
    sendOtp(verifyPhone.value);
  });

  codeInput.addEventListener('focus',()=>codeInput.select());

  verifyForm.addEventListener('submit', ()=>{
    verifySpinner.classList.remove('d-none');
  });
});
</script>
@endpush
