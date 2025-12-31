@extends('layouts.app')

@section('title', 'بلیط رزرو')

@section('content')
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <!-- Success Alert -->
      <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
        <div>
          <h5 class="alert-heading mb-1">رزرو با موفقیت انجام شد!</h5>
          <p class="mb-0">بلیط رزرو شما در زیر نمایش داده شده است. لطفا آن را ذخیره کنید.</p>
        </div>
      </div>

      <!-- Booking Ticket -->
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white py-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-1">
                <i class="bi bi-ticket-perforated me-2"></i>
                بلیط رزرو
              </h4>
              <p class="mb-0 small opacity-75">شماره رزرو: #{{ $booking->id }}</p>
            </div>
            <div class="text-end">
              <div class="badge bg-light text-dark fs-6 px-3 py-2">
                {{ $booking->status === 'pending' ? 'در انتظار پرداخت' : ($booking->status === 'paid' ? 'پرداخت شده' : ucfirst($booking->status)) }}
              </div>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <!-- Host Information -->
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="info-box p-3 bg-light rounded">
                <h6 class="text-muted mb-2">
                  <i class="bi bi-person-badge me-2"></i>
                  اطلاعات میزبان
                </h6>
                <div class="mb-2">
                  <strong>نام میزبان:</strong>
                  <span class="ms-2">{{ $booking->stay->host->name ?? '—' }}</span>
                </div>
                <div class="mb-2">
                  <strong>شماره تماس:</strong>
                  <span class="ms-2" dir="ltr">{{ $booking->stay->host->phone ?? '—' }}</span>
                </div>
                @if($booking->stay->host->email)
                  <div>
                    <strong>ایمیل:</strong>
                    <span class="ms-2">{{ $booking->stay->host->email }}</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <div class="info-box p-3 bg-light rounded">
                <h6 class="text-muted mb-2">
                  <i class="bi bi-calendar-check me-2"></i>
                  اطلاعات رزرو
                </h6>
                <div class="mb-2">
                  <strong>اقامت‌گاه:</strong>
                  <span class="ms-2">{{ $booking->stay->title }}</span>
                </div>
                <div class="mb-2">
                  <strong>تاریخ ورود:</strong>
                  <span class="ms-2">{{ verta($booking->start_date)->format('Y/m/d') }}</span>
                  @if($booking->start_time)
                    <span class="ms-2">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</span>
                  @elseif($booking->stay->checkin_time)
                    <span class="ms-2">{{ \Carbon\Carbon::parse($booking->stay->checkin_time)->format('H:i') }}</span>
                  @endif
                </div>
                <div class="mb-2">
                  <strong>تاریخ خروج:</strong>
                  <span class="ms-2">{{ verta($booking->end_date)->format('Y/m/d') }}</span>
                  @if($booking->end_time)
                    <span class="ms-2">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                  @elseif($booking->stay->checkout_time)
                    <span class="ms-2">{{ \Carbon\Carbon::parse($booking->stay->checkout_time)->format('H:i') }}</span>
                  @endif
                </div>
                <div>
                  <strong>تعداد شب:</strong>
                  <span class="ms-2">{{ $booking->nights }} شب</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Guest Information -->
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="info-box p-3 bg-light rounded">
                <h6 class="text-muted mb-2">
                  <i class="bi bi-people me-2"></i>
                  اطلاعات مهمان
                </h6>
                <div class="mb-2">
                  <strong>نام و نام خانوادگی:</strong>
                  <span class="ms-2">{{ $booking->user->full_name ?? '—' }}</span>
                </div>
                <div class="mb-2">
                  <strong>شماره تماس:</strong>
                  <span class="ms-2" dir="ltr">{{ $booking->user->phone ?? '—' }}</span>
                </div>
                @if($booking->user->national_id)
                  <div>
                    <strong>کد ملی:</strong>
                    <span class="ms-2">{{ $booking->user->national_id }}</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <div class="info-box p-3 bg-light rounded">
                <h6 class="text-muted mb-2">
                  <i class="bi bi-person-fill me-2"></i>
                  تعداد نفرات
                </h6>
                <div class="mb-2">
                  <strong>نفرات پایه:</strong>
                  <span class="ms-2">{{ $booking->base_guests }} نفر</span>
                </div>
                @if($booking->extra_guests > 0)
                  <div class="mb-2">
                    <strong>نفرات اضافه:</strong>
                    <span class="ms-2">{{ $booking->extra_guests }} نفر</span>
                  </div>
                @endif
                <div>
                  <strong>کل نفرات:</strong>
                  <span class="ms-2 fw-bold text-primary">{{ $booking->base_guests + $booking->extra_guests }} نفر</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Pricing Details -->
          <div class="pricing-details p-4 bg-light rounded mb-4">
            <h6 class="text-muted mb-3">
              <i class="bi bi-receipt me-2"></i>
              جزئیات قیمت
            </h6>
            <div class="row">
              <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                  <span>مبلغ پایه:</span>
                  <strong>{{ number_format($booking->base_price) }} ریال</strong>
                </div>
                @if($booking->extra_cost > 0)
                  <div class="d-flex justify-content-between mb-2">
                    <span>مبلغ اضافه:</span>
                    <strong>{{ number_format($booking->extra_cost) }} ریال</strong>
                  </div>
                @endif
              </div>
              <div class="col-md-6">
                @if($booking->stay_discount > 0)
                  <div class="d-flex justify-content-between mb-2 text-muted">
                    <span>تخفیف اقامت‌گاه:</span>
                    <span>{{ number_format($booking->stay_discount) }}%</span>
                  </div>
                @endif
                @if($booking->org_discount > 0)
                  <div class="d-flex justify-content-between mb-2 text-success">
                    <span>تخفیف سازمانی:</span>
                    <strong>{{ number_format($booking->org_discount) }}%</strong>
                  </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                  <span class="fw-bold">مبلغ نهایی:</span>
                  <strong class="text-success fs-5">{{ number_format($booking->final_price) }} ریال</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Location Map -->
          @if($booking->stay->latitude && $booking->stay->longitude)
            <div class="location-section mb-4">
              <h6 class="text-muted mb-3">
                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                موقعیت دقیق اقامت‌گاه
              </h6>
              <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                  <div id="stayMap" style="height: 400px; width: 100%; border-radius: 0.5rem; overflow: hidden;"></div>
                </div>
                <div class="card-footer bg-light">
                  <div class="row g-2">
                    <div class="col-md-6">
                      <small class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $booking->stay->address }}
                      </small>
                    </div>
                    <div class="col-md-6 text-end">
                      <small class="text-muted">
                        {{ $booking->stay->city_name ?? '—' }}, {{ $booking->stay->province_name ?? '—' }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <!-- Actions -->
          <div class="d-flex gap-2 justify-content-center">
            <button onclick="window.print()" class="btn btn-primary">
              <i class="bi bi-printer me-2"></i>
              چاپ بلیط
            </button>
            <a href="{{ route('stays.show', $booking->stay) }}" class="btn btn-outline-primary">
              <i class="bi bi-arrow-left me-2"></i>
              بازگشت به اقامت‌گاه
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  @media print {
    .btn, .alert {
      display: none !important;
    }
    .card {
      box-shadow: none !important;
      border: 1px solid #ddd !important;
    }
  }

  .bg-gradient-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  }

  .info-box {
    border: 1px solid #e2e8f0;
  }

  .pricing-details {
    border: 1px solid #e2e8f0;
  }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($booking->stay->latitude && $booking->stay->longitude)
<script>
document.addEventListener('DOMContentLoaded', function() {
  const lat = {{ (float) $booking->stay->latitude }};
  const lng = {{ (float) $booking->stay->longitude }};
  
  const map = L.map('stayMap', { zoomControl: true }).setView([lat, lng], 16);

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map);

  // Add precise marker
  const marker = L.marker([lat, lng], {
    icon: L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })
  }).addTo(map);

  marker.bindPopup('<strong>{{ $booking->stay->title }}</strong><br>{{ $booking->stay->address }}').openPopup();

  // Fix map size on load
  setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif
@endpush
@endsection

