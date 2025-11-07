@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="card shadow border-0 rounded-4 p-4">
    <h4 class="fw-bold text-primary mb-3">خوش آمدی، {{ Auth::user()->name }}</h4>
    <p class="text-muted">شهر شما: {{ $host->city ?? '-' }}</p>
    <p class="text-muted">نوع اقامتگاه: {{ $host->stay_type ?? '-' }}</p>

    <a href="#" class="btn btn-outline-primary mt-3">
      <i class="bi bi-plus-circle"></i> افزودن اقامتگاه جدید
    </a>
  </div>
</div>
@endsection
