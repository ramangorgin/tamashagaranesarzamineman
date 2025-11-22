@extends('layouts.host')

@section('title', 'داشبورد میزبان')

@section('content')
<div class="container-fluid animate__animated animate__fadeInUp">
  <h4 class="mb-4 fw-bold text-dark">👋 سلام {{ auth('host')->user()->name ?? 'میزبان عزیز' }}</h4>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="stat-card bg-blue">
        <h6>اقامت‌گاه‌های فعال</h6>
        <h3>۴</h3>
        <i class="bi bi-house-door fs-3 opacity-75"></i>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-orange">
        <h6>درخواست‌های در انتظار</h6>
        <h3>۲</h3>
        <i class="bi bi-clock fs-3 opacity-75"></i>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-green">
        <h6>درآمد این ماه</h6>
        <h3>۸٬۴۵۰٬۰۰۰</h3>
        <i class="bi bi-cash-coin fs-3 opacity-75"></i>
      </div>
    </div>

    <div class="col-md-3">
      <div class="stat-card bg-purple">
        <h6>پیام‌های جدید</h6>
        <h3>۵</h3>
        <i class="bi bi-chat-text fs-3 opacity-75"></i>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h5 class="fw-bold text-dark mb-3">📈 گزارش وضعیت اقامت‌گاه‌ها</h5>
    <div class="card border-0 shadow-sm rounded-4 p-4">
      <p class="text-muted mb-0">
        در این بخش می‌توانید آمار رزروها، درآمد و وضعیت تایید اقامت‌گاه‌ها را مشاهده کنید.  
        بخش‌های مدیریتی بیشتر به‌زودی اضافه خواهند شد.
      </p>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/host-layout.css') }}">
<style>
/* keep existing stat-card styles or import from admin */
</style>
@endpush
