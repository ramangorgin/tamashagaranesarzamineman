@extends('layouts.app')

@section('title', 'پنل کاربری')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3">👋 خوش آمدید {{ auth()->user()->name ?? 'کاربر عزیز' }}</h4>
        <p>شما وارد حساب کاربری خود شدید.</p>

        <a href="#" class="btn btn-outline-primary">مشاهده رزروها</a>
        <a href="#" class="btn btn-outline-secondary">ویرایش پروفایل</a>

        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger float-end">خروج</button>
        </form>
    </div>
</div>
@endsection
