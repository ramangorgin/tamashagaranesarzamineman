@extends('layouts.app')
@section('title', 'نمایش اقامت‌گاه')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-modal.css') }}">
@endpush
@section('content')


<!-- دکمه رزرو -->
<button class="btn btn-primary btn-lg mt-3 w-100 shadow-sm"
        data-bs-toggle="modal" data-bs-target="#bookingModal">
    <i class="bi bi-calendar-check me-2"></i> رزرو و اجاره اقامت‌گاه
</button>

<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-door-open me-2"></i> رزرو اقامت‌گاه
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <!-- مرحله 1: شماره موبایل -->
        <div id="step-phone">
          <h6 class="fw-bold text-dark mb-3">۱️⃣ وارد کردن شماره موبایل</h6>
          <div class="mb-3">
            <label class="form-label">شماره موبایل</label>
            <input type="text" id="phoneInput" class="form-control" placeholder="مثلاً 09123456789">
            <div id="phoneError" class="invalid-feedback"></div>
          </div>
          <button class="btn btn-primary w-100" id="sendOtpBtn">
            ارسال کد تأیید <i class="bi bi-send ms-1"></i>
          </button>
          <!-- 🔄 Loading Overlay -->
            <div id="loadingOverlay" class="d-none">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 fw-semibold text-primary">لطفاً صبر کنید...</p>
                </div>
            </div>

        </div>

        <!-- مرحله 2: کد تایید -->
        <div id="step-otp" class="d-none">
          <h6 class="fw-bold text-dark mb-3">۲️⃣ تأیید شماره موبایل</h6>
          <div class="mb-3 text-center">
            <input type="text" id="otpCode" class="form-control text-center fs-5 fw-bold"
                   maxlength="6" placeholder="کد ۶ رقمی پیامک‌شده را وارد کنید">
            <div id="otpError" class="invalid-feedback text-center"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span id="otpTimer" class="text-muted small"></span>
            <button id="resendOtpBtn" class="btn btn-outline-secondary btn-sm" disabled>
              ارسال مجدد <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
          <button class="btn btn-success w-100 mt-3" id="verifyOtpBtn">
            بررسی کد <i class="bi bi-check-circle ms-1"></i>
          </button>
        </div>

        <!-- مرحله 3: مشخصات -->
        <div id="step-info" class="d-none">
          <h6 class="fw-bold text-dark mb-3">۳️⃣ وارد کردن اطلاعات شخصی</h6>
          <form id="bookingForm">
            @csrf
            <input type="hidden" name="stay_id" value="{{ $stay->id }}">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام</label>
                <input type="text" name="first_name" class="form-control" required>
                <div class="invalid-feedback">نام را وارد کنید.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">نام خانوادگی</label>
                <input type="text" name="last_name" class="form-control" required>
                <div class="invalid-feedback">نام خانوادگی را وارد کنید.</div>
              </div>
              <div class="col-md-12">
                <label class="form-label">کد ملی</label>
                <input type="text" name="national_id" class="form-control" required>
                <div class="invalid-feedback">کد ملی را وارد کنید.</div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">
              بررسی تخفیف و مبلغ نهایی
            </button>
          </form>
        </div>

        <!-- مرحله 4: نتیجه -->
        <div id="step-result" class="d-none text-center">
          <i class="bi bi-gift-fill text-success fs-1"></i>
          <h5 class="mt-3 fw-bold text-dark" id="discountMsg"></h5>
          <p class="fs-5 text-primary mt-2">مبلغ قابل پرداخت: <span id="finalPrice"></span> تومان</p>
          <button id="goToPayment" class="btn btn-success w-100 mt-3">
            پرداخت <i class="bi bi-credit-card ms-1"></i>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>




@push('scripts')
<script>
let verifiedPhone = null;
let timerInterval = null;
const otpDuration = 120; // ثانیه
let remainingTime = otpDuration;

// مرحله ۱: ارسال OTP
$('#sendOtpBtn').on('click', function(){
    const phone = $('#phoneInput').val().trim();
    $('#phoneError').text('');

    if(!phone.match(/^09\d{9}$/)) {
        $('#phoneInput').addClass('is-invalid');
        $('#phoneError').text('شماره موبایل معتبر نیست.');
        return;
    }

    $(this).prop('disabled', true).text('در حال ارسال...');
    showLoading();
    $.post('{{ route("otp.send") }}', {_token:'{{ csrf_token() }}', phone}, res => {
        hideLoading();
        if(res.success){
            $('#step-phone').addClass('d-none');
            $('#step-otp').removeClass('d-none');
            startTimer();
        } else {
            $('#phoneError').text(res.message);
        }
        $('#sendOtpBtn').prop('disabled', false).text('ارسال کد تأیید');
    });
});

// زمان‌سنج OTP
function startTimer(){
    remainingTime = otpDuration;
    $('#resendOtpBtn').prop('disabled', true);
    timerInterval = setInterval(()=>{
        remainingTime--;
        $('#otpTimer').text(`زمان باقی‌مانده: ${remainingTime} ثانیه`);
        if(remainingTime <= 0){
            clearInterval(timerInterval);
            $('#otpTimer').text('مهلت تمام شد');
            $('#resendOtpBtn').prop('disabled', false);
        }
    }, 1000);
}

// ارسال مجدد OTP
$('#resendOtpBtn').on('click', function(){
    $('#step-otp').addClass('d-none');
    $('#step-phone').removeClass('d-none');
});

// مرحله ۲: بررسی OTP
$('#verifyOtpBtn').on('click', function(){
    const phone = $('#phoneInput').val().trim();
    const code = $('#otpCode').val().trim();
    $('#otpError').text('');

    if(code.length !== 6){
        $('#otpError').text('کد ۶ رقمی را به درستی وارد کنید.');
        return;
    }
    showLoading();
    $.post('{{ route("otp.verify") }}', {_token:'{{ csrf_token() }}', phone, code}, res => {
        hideLoading();
        if(res.success){
            clearInterval(timerInterval);
            verifiedPhone = phone;
            $('#otpCode').removeClass('is-invalid').addClass('is-valid');
            $('#step-otp').addClass('d-none');
            $('#step-info').removeClass('d-none');
        } else {
            $('#otpError').text(res.message);
            $('#otpCode').addClass('is-invalid');
        }
    });
});

// مرحله ۳: ارسال اطلاعات کاربر و بررسی تخفیف
$('#bookingForm').on('submit', function(e){
    e.preventDefault();
    showLoading()
    if(!verifiedPhone) return alert('ابتدا شماره موبایل خود را تأیید کنید.');

    const data = $(this).serialize() + `&phone=${verifiedPhone}`;
    $.post('{{ route("bookings.store") }}', data, res => {
        hideLoading();
        if(res.success){
            $('#step-info').addClass('d-none');
            $('#discountMsg').text(res.message);
            $('#finalPrice').text(res.final_price);
            $('#step-result').removeClass('d-none');
        } else {
            alert(res.message);
        }
    });
});

// مرحله ۴: هدایت به پرداخت تستی
$('#goToPayment').on('click', function(){
    window.location.href = `/payment/test/${Date.now()}`;
});

function showLoading() {
    $('#loadingOverlay').removeClass('d-none').addClass('show');
}

function hideLoading() {
    $('#loadingOverlay').addClass('d-none').removeClass('show');
}

</script>
@endpush

@endsection