@extends('layouts.admin')

@section('title', 'داشبورد مدیریت')

@push('styles')
<style>
.stat-card{
    background:#fff;
    border-radius:24px;
    padding:24px 18px 20px;
    box-shadow:0 8px 24px -6px rgba(0,0,0,.06);
    position:relative;
    overflow:hidden;
    transition:.35s;
    color:#0f172a;
}
.stat-card:before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,0));
    pointer-events:none;
}
.stat-card h6{font-size:.85rem;margin-bottom:4px;letter-spacing:.5px;font-weight:600;}
.stat-card h3{font-size:1.9rem;font-weight:700;margin:0;font-family:inherit;}
.stat-card i{line-height:1;color:#fff;filter:drop-shadow(0 4px 8px rgba(0,0,0,.25));}

.bg-blue{background:linear-gradient(135deg,#2563eb,#3b82f6);}
.bg-green{background:linear-gradient(135deg,#059669,#10b981);}
.bg-orange{background:linear-gradient(135deg,#d97706,#f59e0b);}
.bg-purple{background:linear-gradient(135deg,#7e22ce,#9333ea);}

.stat-card:hover{
    transform:translateY(-6px) scale(1.02);
    box-shadow:0 16px 32px -10px rgba(0,0,0,.18);
}

.stat-card:hover i{animation:pop .5s;}
@keyframes pop{0%{transform:scale(1)}50%{transform:scale(1.25)}100%{transform:scale(1)}}
.stat-card h6, .stat-card h3{color:#fff;}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold text-dark">👋 خوش آمدید، مدیر محترم</h4>

    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-blue text-center">
                <i class="bi bi-house-door fs-2 d-block mb-2"></i>
                <h6>تعداد اقامت‌گاه‌ها</h6>
                <h3>۲۴</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-green text-center">
                <i class="bi bi-clipboard-check fs-2 d-block mb-2"></i>
                <h6>قراردادهای فعال</h6>
                <h3>۸</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-orange text-center">
                <i class="bi bi-envelope fs-2 d-block mb-2"></i>
                <h6>درخواست‌های جدید</h6>
                <h3>۵</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card bg-purple text-center">
                <i class="bi bi-bell fs-2 d-block mb-2"></i>
                <h6>اعلان‌ها</h6>
                <h3>۳</h3>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="fw-bold text-dark mb-3">📈 آمار کلی</h5>
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <p>در این بخش می‌توانید خلاصه‌ای از وضعیت اقامتگاه‌ها، قراردادها و کاربران را مشاهده کنید.  
               بخش‌های جدید مدیریت به‌زودی اضافه خواهند شد.</p>
        </div>
    </div>
</div>
@endsection
