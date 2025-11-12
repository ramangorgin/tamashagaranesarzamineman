@extends('layouts.app')

@section('title', 'ورود / ثبت‌نام میزبان')

@section('content')
<div class="container d-flex align-items-center justify-content-center min-vh-100 bg-light">
  <div class="card shadow-lg border-0 rounded-4 p-4 animate__animated animate__fadeIn" style="max-width: 420px; width: 100%;">
    <div class="text-center mb-4">
      <i class="bi bi-house-door-fill text-primary fs-1 mb-2"></i>
      <h4 class="fw-bold text-primary">ورود میزبان</h4>
      <p class="text-muted small">شماره تلفن خود را وارد کنید تا کد ورود برایتان ارسال شود.</p>
    </div>

    <div id="step-phone">
      <div class="mb-3">
        <label class="form-label fw-semibold">شماره تلفن</label>
        <input type="text" id="phone" class="form-control text-center" placeholder="مثلاً 09123456789">
        <div id="phone-error" class="text-danger small mt-1"></div>
      </div>
      <button id="sendOtpBtn" class="btn btn-primary w-100">
        <i class="bi bi-send"></i> ارسال کد ورود
      </button>
    </div>

    <div id="step-otp" class="d-none">
      <div class="mb-3">
        <label class="form-label fw-semibold">کد ارسال‌شده</label>
        <input type="text" id="otp" class="form-control text-center" placeholder="******">
        <div id="otp-error" class="text-danger small mt-1"></div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <button id="resendOtpBtn" class="btn btn-outline-secondary btn-sm" disabled>ارسال مجدد (<span id="countdown">60</span>)</button>
        <button id="verifyOtpBtn" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> تایید کد</button>
      </div>
    </div>

    <div id="loading" class="text-center d-none">
      <div class="spinner-border text-primary mt-3" role="status"></div>
      <p class="text-muted mt-2">در حال ارسال...</p>
    </div>

    <div id="success-msg" class="alert alert-success d-none mt-3 text-center fw-semibold"></div>
  </div>
</div>

{{-- JavaScript --}}
@push('scripts')
<script>
$(document).ready(function(){

  const $phone = $('#phone');
  const $otp = $('#otp');
  const $loading = $('#loading');
  const $stepPhone = $('#step-phone');
  const $stepOtp = $('#step-otp');
  const $countdown = $('#countdown');
  const $resendBtn = $('#resendOtpBtn');
  let timer;

  // ارسال OTP
  $('#sendOtpBtn').on('click', function(){
    const phone = $phone.val();
    $('#phone-error').text('');
    if(!/^09\d{9}$/.test(phone)){
      $('#phone-error').text('شماره تلفن معتبر نیست');
      return;
    }

    $loading.removeClass('d-none');
    $.ajax({
      method: 'POST',
      url: "{{ route('host.sendOtp') }}",
      data: { phone: phone, _token: "{{ csrf_token() }}" },
      success: function(){
        $loading.addClass('d-none');
        $stepPhone.addClass('d-none');
        $stepOtp.removeClass('d-none').hide().fadeIn(500);
        startCountdown();
      },
      error: function(){
        $loading.addClass('d-none');
        $('#phone-error').text('خطا در ارسال کد. لطفاً دوباره تلاش کنید.');
      }
    });
  });

  // تایید OTP
  $('#verifyOtpBtn').on('click', function(){
    const phone = $phone.val();
    const code = $otp.val();
    $('#otp-error').text('');

    if(code.length !== 6){
      $('#otp-error').text('کد باید ۶ رقم باشد');
      return;
    }

    $loading.removeClass('d-none');
    $.ajax({
      method: 'POST',
      url: "{{ route('host.verifyOtp') }}",
      data: { phone: phone, code: code, _token: "{{ csrf_token() }}" },
      success: function(res){
        $loading.addClass('d-none');
        $('#success-msg').removeClass('d-none').text('✅ ورود موفق! در حال هدایت...');
        setTimeout(() => window.location.href = "{{ route('host.completeProfile') }}", 1500);
      },
      error: function(){
        $loading.addClass('d-none');
        $('#otp-error').text('کد وارد شده اشتباه است.');
      }
    });
  });

  // تایمر ارسال مجدد
  function startCountdown(){
    let seconds = 60;
    $resendBtn.prop('disabled', true);
    timer = setInterval(() => {
      seconds--;
      $countdown.text(seconds);
      if(seconds <= 0){
        clearInterval(timer);
        $resendBtn.prop('disabled', false).text('ارسال مجدد');
      }
    }, 1000);
  }

});
</script>
@endpush
@endsection
