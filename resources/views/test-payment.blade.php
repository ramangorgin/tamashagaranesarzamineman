@extends('layouts.app')

@section('title', 'پرداخت تستی')

@section('content')
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-gradient-success text-white py-4 text-center">
          <h4 class="mb-0">
            <i class="bi bi-credit-card me-2"></i>
            پرداخت تستی
          </h4>
        </div>
        <div class="card-body p-4">
          <div class="text-center mb-4">
            <div class="mb-3">
              <i class="bi bi-receipt-cutoff text-primary" style="font-size: 4rem;"></i>
            </div>
            <h5 class="mb-2">مبلغ قابل پرداخت</h5>
            <p class="fs-3 fw-bold text-success mb-0">{{ number_format($booking->final_price) }} ریال</p>
          </div>

          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            این یک پرداخت تستی است. در محیط واقعی، اینجا درگاه پرداخت نمایش داده می‌شود.
          </div>

          <form id="paymentForm" method="POST" action="{{ route('payment.test.complete', $booking) }}">
            @csrf
            <button type="submit" class="btn btn-success btn-lg w-100">
              <i class="bi bi-check-circle me-2"></i>
              تأیید پرداخت و دریافت بلیط
            </button>
          </form>

          <div class="text-center mt-3">
            <a href="{{ route('stays.show', $booking->stay) }}" class="text-muted text-decoration-none">
              <i class="bi bi-arrow-right me-1"></i>
              بازگشت به اقامت‌گاه
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .bg-gradient-success {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  }
</style>
@endpush
@endsection
