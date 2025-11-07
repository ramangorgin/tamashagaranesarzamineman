@extends('layouts.app')

@section('title', 'پرداخت تستی')

@section('content')
<div class="container text-center mt-5">
    <h4>💳 پرداخت تستی</h4>
    <p>مبلغ قابل پرداخت: <strong>{{ number_format($booking->final_price) }}</strong> تومان</p>
    <button class="btn btn-success mt-3" onclick="alert('پرداخت با موفقیت شبیه‌سازی شد 🎉')">
        تأیید پرداخت
    </button>
</div>
@endsection
