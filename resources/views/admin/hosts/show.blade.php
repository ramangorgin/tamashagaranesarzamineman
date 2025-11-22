@extends('layouts.admin')
@section('title','جزئیات میزبان')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.hosts.index') }}">میزبانان</a></li>
    <li class="breadcrumb-item active">جزئیات</li>
@endsection

@section('content')
<div class="breadcrumb-container">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.hosts.index') }}">میزبانان</a></li>
    <li class="breadcrumb-item active">جزئیات</li>
  </ol>
</div>

@if(session('success')) <div class="alert alert-success mt-3">{{ session('success') }}</div> @endif

<div class="card border-0 shadow-sm rounded-4 mt-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <h5 class="fw-bold mb-0">{{ $host->name ?? '—' }}</h5>
      <span class="badge {{ $host->status==='approved'?'bg-success':($host->status==='rejected'?'bg-danger':'bg-secondary') }}">
        {{ $host->status==='approved'?'تأیید شده':($host->status==='rejected'?'رد شده':'در انتظار') }}
      </span>
    </div>

    <div class="row g-3 small text-muted">
      <div class="col-md-3"><strong>موبایل:</strong> <span dir="ltr">{{ $host->phone }}</span></div>
      <div class="col-md-3"><strong>کد ملی:</strong> {{ $host->national_id ?? '—' }}</div>
      <div class="col-md-3"><strong>ایمیل:</strong> {{ $host->email ?? '—' }}</div>
      <div class="col-md-3"><strong>کد پستی:</strong> {{ $host->postal_code ?? '—' }}</div>

      <div class="col-md-12"><strong>آدرس:</strong>
        {{ $host->province_name }} / {{ $host->city_name }} / {{ $host->county_name }} / {{ $host->village_name }}
        - {{ $host->address }}
      </div>

      <div class="col-md-4"><strong>بانک:</strong> {{ $host->bank_name ?? '—' }}</div>
      <div class="col-md-4"><strong>صاحب حساب:</strong> {{ $host->account_holder ?? '—' }}</div>
      <div class="col-md-4"><strong>شبا:</strong> <span dir="ltr">{{ $host->iban ?? '—' }}</span></div>

      @if($host->rejection_reason)
        <div class="col-md-12"><strong>علت رد:</strong> {{ $host->rejection_reason }}</div>
      @endif
    </div>

    <hr>

    <h6 class="fw-bold mb-2">تصاویر احراز هویت</h6>
    <div class="row g-3">
      <div class="col-sm-4">
        <div class="border rounded-3 p-2 text-center">
          <div class="small fw-semibold mb-2">کارت ملی</div>
          @if($host->id_card_image)
            <img src="{{ asset($host->id_card_image) }}" class="img-fluid rounded">
          @else <span class="text-muted small">ثبت نشده</span> @endif
        </div>
      </div>
      <div class="col-sm-4">
        <div class="border rounded-3 p-2 text-center">
          <div class="small fw-semibold mb-2">سلفی</div>
          @if($host->selfie_image)
            <img src="{{ asset($host->selfie_image) }}" class="img-fluid rounded">
          @else <span class="text-muted small">ثبت نشده</span> @endif
        </div>
      </div>
      <div class="col-sm-4">
        <div class="border rounded-3 p-2 text-center">
          <div class="small fw-semibold mb-2">مجوز / سند</div>
          @if($host->business_license)
            <img src="{{ asset($host->business_license) }}" class="img-fluid rounded">
          @else <span class="text-muted small">ثبت نشده</span> @endif
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="{{ route('admin.hosts.edit',$host) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i> ویرایش</a>
      <a href="{{ route('admin.hosts.index') }}" class="btn btn-outline-secondary btn-sm">بازگشت</a>
    </div>
  </div>
</div>
@endsection