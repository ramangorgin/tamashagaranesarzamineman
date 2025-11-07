@extends('layouts.app')

@section('title', 'خطای داخلی سرور')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="text-danger">⚠️ خطای سیستمی</h2>
    <p>متأسفانه در پردازش درخواست شما خطایی رخ داده است.</p>
    <p>لطفاً مجدداً تلاش کنید یا با پشتیبانی سایت تماس بگیرید.</p>
    <a href="{{ url()->previous() }}" class="btn btn-outline-primary mt-3">بازگشت به صفحه قبل</a>
</div>
@endsection
