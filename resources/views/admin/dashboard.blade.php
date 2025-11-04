@extends('layouts.admin')

@section('title', 'پنل مدیریت')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3"> <i class="bi bi-check-square-fill me-1"></i> پنل مدیریت</h4>
        <p>به بخش مدیریت سایت خوش آمدید.</p>

        <a href="{{ route('stays.index') }}" class="btn btn-primary">مدیریت مکان‌ها</a>
        <a href="#" class="btn btn-success">مشاهده کاربران</a>

        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger float-end">خروج</button>
        </form>
    </div>
</div>
@endsection
