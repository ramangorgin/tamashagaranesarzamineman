@extends('layouts.admin-login')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4 border-0" style="max-width:400px;width:100%">
      <h4 class="text-center mb-4 text-primary fw-bold">ورود مدیر</h4>

      @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif

      {{-- فرم ارسال کد --}}
      <form method="POST" action="{{ route('admin.sendOtp') }}" id="otpForm">
          @csrf
          <div class="mb-3">
              <label for="phone">شماره تلفن</label>
              <input type="text" name="phone" id="phone" class="form-control text-center" placeholder="مثلاً 09123456789" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">ارسال کد</button>
      </form>

      {{-- فرم تایید کد --}}
      <form method="POST" action="{{ route('admin.verifyOtp') }}" class="mt-3" id="verifyForm">
          @csrf
          <input type="hidden" name="phone" id="verify_phone" value="">
          <div class="mb-3">
              <label for="code">کد دریافتی</label>
              <input type="text" name="code" id="code" class="form-control text-center" placeholder="******" required>
          </div>
          <button type="submit" class="btn btn-success w-100">تایید و ورود</button>
      </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('#phone');
    const verifyPhoneInput = document.querySelector('#verify_phone');

    // مقدار شماره را در localStorage نگه‌دار
    phoneInput.addEventListener('input', () => {
        localStorage.setItem('admin_phone', phoneInput.value);
    });

    // وقتی فرم دوم لود میشه، مقدار ذخیره‌شده را بذار داخل hidden input
    const savedPhone = localStorage.getItem('admin_phone');
    if (savedPhone) {
        verifyPhoneInput.value = savedPhone;
        phoneInput.value = savedPhone; // برای اطمینان
    }

    // اگه فرم verify ارسال شد، مقدار phone را ست کن
    document.querySelector('#verifyForm').addEventListener('submit', () => {
        verifyPhoneInput.value = phoneInput.value;
    });
});
</script>
@endsection
