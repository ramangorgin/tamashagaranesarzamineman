@extends('layouts.admin')

@section('title', 'داشبورد مدیریت')

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
