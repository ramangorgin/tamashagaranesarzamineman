@extends('layouts.app')
@section('title','پشتیبانی')
@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4 anim fade-up">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-life-preserver me-1 text-primary"></i>مرکز پشتیبانی</h5>
                    <p class="small text-secondary mb-3">اگر در فرایند ثبت‌نام، جستجو یا رزرو اقامتگاه با مشکلی مواجه شدید از یکی از روش‌های زیر استفاده کنید یا فرم را ارسال نمایید.</p>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><i class="bi bi-envelope-open text-primary me-1"></i>ایمیل: <span class="fw-semibold">support@tamashagaranesarzamineman.ir</span></li>
                        <li class="mb-2"><i class="bi bi-telegram text-info me-1"></i>تلگرام: <a class="text-decoration-none" href="https://t.me/your_channel" target="_blank" rel="noopener">@your_channel</a></li>
                        <li class="mb-2"><i class="bi bi-whatsapp text-success me-1"></i>واتساپ: <a class="text-decoration-none" href="https://wa.me/989000000000" target="_blank" rel="noopener">+98 900 000 0000</a></li>
                        <li class="mb-2"><i class="bi bi-instagram text-danger me-1"></i>اینستاگرام: <a class="text-decoration-none" href="https://instagram.com/your_profile" target="_blank" rel="noopener">@your_profile</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 anim fade-up delay-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-chat-dots me-1 text-primary"></i>ارسال درخواست پشتیبانی</h5>
                    @if(session('status'))
                        <div class="alert alert-success small" dir="rtl">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('support.submit') }}" class="row g-3" novalidate>
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label small">نام</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">ایمیل</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">موضوع</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label small">شرح مشکل / پیام</label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary px-4"><i class="bi bi-send me-1"></i>ارسال</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection