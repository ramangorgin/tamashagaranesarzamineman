@extends('layouts.app')
@section('title', $stay->title)

@section('content')
<div class="container my-5">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-4">
      <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">خانه</a></li>
      <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">{{ $stay->province }}</a></li>
      <li class="breadcrumb-item active text-primary">{{ $stay->title }}</li>
    </ol>
  </nav>

  {{-- عنوان و اطلاعات اصلی --}}
  <div class="row g-4">
    <div class="col-lg-8">

      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="fw-bold text-dark">{{ $stay->title }}</h2>
        <span class="badge bg-primary fs-6 py-2 px-3 rounded-pill">
          {{ ucfirst($stay->category) }}
        </span>
      </div>
      <p class="text-muted mb-4">
        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
        {{ $stay->city }}, {{ $stay->province }}
      </p>

      {{-- گالری تصاویر --}}
      <div class="row g-2 mb-4">
        @foreach($stay->images->take(5) as $index => $img)
        <div class="col-{{ $index == 0 ? '12' : '6' }}">
          <img src="{{ asset('storage/'.$img->path) }}" class="img-fluid rounded-4 shadow-sm hover-zoom" alt="">
        </div>
        @endforeach
      </div>

      {{-- درباره اقامتگاه --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i> درباره اقامت‌گاه</h5>
          <p class="text-muted lh-lg">{{ $stay->description ?? 'توضیحات ثبت نشده است.' }}</p>
        </div>
      </div>

      {{-- امکانات --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-grid-1x2 text-success me-2"></i> امکانات اقامت‌گاه</h5>
          <div class="row g-3">
            @forelse($stay->facilities as $facility)
            <div class="col-6 col-md-4">
              <div class="d-flex align-items-center">
                <i class="{{ $facility->icon ?? 'bi bi-check-circle' }} text-success me-2 fs-5"></i>
                <span>{{ $facility->name }}</span>
              </div>
            </div>
            @empty
            <p class="text-muted">هیچ امکاناتی ثبت نشده است.</p>
            @endforelse
          </div>
        </div>
      </div>

      {{-- قوانین --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist text-warning me-2"></i> قوانین اقامت‌گاه</h5>
          <ul class="list-unstyled mb-0">
            @forelse($stay->rules as $rule)
            <li class="mb-2"><i class="bi bi-dot text-primary fs-4"></i> {{ $rule->rule_text }}</li>
            @empty
            <p class="text-muted">قوانینی ثبت نشده است.</p>
            @endforelse
          </ul>
        </div>
      </div>

      {{-- نقشه --}}
      @if($stay->latitude && $stay->longitude)
      <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-geo text-danger me-2"></i> موقعیت مکانی</h5>
          <div id="map" style="height: 300px;" class="rounded-4"></div>
        </div>
      </div>
      @endif

    </div>

    {{-- ستون سمت راست (رزرو) --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 80px;">
        <div class="card-body">
          <h4 class="fw-bold text-dark mb-3">
            <span class="text-primary">{{ number_format($stay->final_price) }}</span>
            <small class="text-muted fs-6">تومان / هر شب</small>
          </h4>
          <ul class="list-unstyled text-muted small mb-4">
            <li><i class="bi bi-person-fill text-primary me-1"></i> ظرفیت: {{ $stay->capacity }} نفر</li>
            <li><i class="bi bi-house-door text-primary me-1"></i> متراژ: {{ $stay->area ?? 'نامشخص' }} متر</li>
          </ul>

          {{-- فقط برای کاربران عادی --}}
          @guest('admin')
          @guest('host')
            @include('partials.booking-modal')
          @else
            <div class="alert alert-info text-center">فقط مسافران می‌توانند اقامت‌گاه رزرو کنند.</div>
          @endguest
          @endguest

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

{{-- نقشه --}}
@if($stay->latitude && $stay->longitude)
<script>
document.addEventListener('DOMContentLoaded', function () {
  const map = L.map('map').setView([{{ $stay->latitude }}, {{ $stay->longitude }}], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  L.marker([{{ $stay->latitude }}, {{ $stay->longitude }}]).addTo(map);
});
</script>
@endif

@endpush


@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-modal.css') }}">
<style>
.hover-zoom { transition: transform 0.3s ease; }
.hover-zoom:hover { transform: scale(1.03); }

.card { transition: all .3s ease-in-out; }
.card:hover { transform: translateY(-3px); }

.sticky-top { z-index: 1020; }

@media (max-width: 768px) {
  .breadcrumb { font-size: 0.9rem; }
  .card-body h5 { font-size: 1rem; }
}
</style>
@endpush

@endsection