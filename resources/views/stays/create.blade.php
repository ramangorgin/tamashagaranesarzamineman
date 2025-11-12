@php
    $isAdmin = Auth::guard('admin')->check();
    $isHost = Auth::guard('host')->check();
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.host';
    $storeRoute = $isAdmin ? route('admin.stays.store') : route('host.stays.store');
@endphp

@extends($layout)

@section('title', 'افزودن اقامت‌گاه جدید')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-building-add text-success me-2"></i>
            {{ $isAdmin ? 'افزودن اقامت‌گاه (ادمین)' : 'افزودن اقامت‌گاه (میزبان)' }}
        </h4>
        <a href="{{ $isAdmin ? route('admin.stays.index') : route('host.stays.index') }}" 
           class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-right-circle me-1"></i> بازگشت
        </a>
    </div>

    @include('admin.partials.messages')

    <div class="card border-0 shadow-sm rounded-4 p-4 fade-in">
        <form method="POST" action="{{ $storeRoute }}" enctype="multipart/form-data">
            @csrf

            {{-- فرم مشترک --}}
            @include('stays.partials.form-fields', [
                'stay' => $stay ?? null,
                'isAdmin' => $isAdmin,
                'categories' => $categories
            ])


            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="bi bi-check-circle me-1"></i> ثبت اقامت‌گاه
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.fade-in { animation: fadeIn .4s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
input, textarea, select { transition: all 0.2s ease-in-out; }
input:focus, textarea:focus, select:focus { box-shadow: 0 0 5px rgba(13,110,253,0.5); border-color: #0d6efd; }
.hover-facility:hover { background-color: #e9f5ff; transform: scale(1.02); }
</style>
@endpush
@endsection
