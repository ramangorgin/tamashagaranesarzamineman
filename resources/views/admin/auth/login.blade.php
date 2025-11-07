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

      <form method="POST" action="{{ route('admin.sendOtp') }}">
          @csrf
          <div class="mb-3">
              <label for="phone">شماره تلفن</label>
              <input type="text" name="phone" id="phone" class="form-control text-center" placeholder="مثلاً 09123456789" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">ارسال کد</button>
      </form>

      <form method="POST" action="{{ route('admin.verifyOtp') }}" class="mt-3">
          @csrf
          <div class="mb-3">
              <label for="code">کد دریافتی</label>
              <input type="text" name="code" id="code" class="form-control text-center" placeholder="******" required>
          </div>
          <button type="submit" class="btn btn-success w-100">تایید و ورود</button>
      </form>
  </div>
</div>
@endsection
