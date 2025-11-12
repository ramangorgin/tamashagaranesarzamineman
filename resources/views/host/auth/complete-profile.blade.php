@extends('layouts.app')

@section('title', 'تکمیل اطلاعات میزبان')

@section('content')
<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:600px;">
    <div class="card-body p-4">
      <h4 class="text-center mb-4 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i> تکمیل اطلاعات میزبان</h4>

      <form id="profileForm" method="POST" action="{{ route('host.saveProfile') }}">
        @csrf

        {{-- Step 1: اطلاعات شخصی --}}
        <div id="step1">
          <div class="mb-3">
            <label class="form-label">نام کامل</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">کد ملی</label>
            <input type="text" name="national_id" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ایمیل (اختیاری)</label>
            <input type="email" name="email" class="form-control">
          </div>
          <button type="button" class="btn btn-primary w-100 next-step">مرحله بعد</button>
        </div>

        {{-- Step 2: آدرس --}}
        <div id="step2" class="d-none">
          <div class="mb-3">
            <label class="form-label">استان</label>
            <input type="text" name="province" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">شهر</label>
            <input type="text" name="city" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">آدرس دقیق</label>
            <textarea name="address" class="form-control" rows="2" required></textarea>
          </div>
          <button type="button" class="btn btn-secondary w-100 prev-step mb-2">بازگشت</button>
          <button type="button" class="btn btn-primary w-100 next-step">مرحله بعد</button>
        </div>

        {{-- Step 3: تایید نهایی --}}
        <div id="step3" class="d-none text-center">
          <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
          <h5 class="fw-bold mb-3">آیا اطلاعات شما صحیح است؟</h5>
          <button type="button" class="btn btn-secondary prev-step">بازگشت</button>
          <button type="submit" class="btn btn-success ms-2">تایید و ثبت</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(function(){
  let step = 1;
  const fade = (hide, show) => { hide.fadeOut(300, () => show.fadeIn(300)); };

  $('.next-step').click(() => {
    if(step === 1){ fade($('#step1'), $('#step2')); step++; }
    else if(step === 2){ fade($('#step2'), $('#step3')); step++; }
  });
  $('.prev-step').click(() => {
    if(step === 2){ fade($('#step2'), $('#step1')); step--; }
    else if(step === 3){ fade($('#step3'), $('#step2')); step--; }
  });
});
</script>
@endpush
@endsection
