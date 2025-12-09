@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.app')
@section('title', $stay->title)

@section('breadcrumb')
    @if(($role ?? null)==='admin')
        <li class="breadcrumb-item"><a href="{{ route('admin.stays.index') }}">اقامت‌گاه‌ها</a></li>
        <li class="breadcrumb-item active">جزئیات</li>
    @endif
@endsection

@section('breadcrumb-actions')
    @if(($role ?? null)==='admin')
        <a href="{{ route('admin.stays.edit',$stay) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil-square"></i> ویرایش
        </a>
    @endif
@endsection

@section('content')
<div class="container my-5">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-4">
      <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">خانه</a></li>
      <li class="breadcrumb-item">
        <a href="#" class="text-decoration-none text-muted">{{ $stay->province_name ?? '—' }}</a>
      </li>
      <li class="breadcrumb-item active text-primary">{{ $stay->title }}</li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-8">

      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="fw-bold text-dark">{{ $stay->title }}</h2>
        <span class="badge bg-primary fs-6 py-2 px-3 rounded-pill">
          {{ stayTypeToPersian($stay->category) }}
        </span>
      </div>

      <p class="text-muted mb-4">
        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
        {{ $stay->city_name ?? '—' }}, {{ $stay->province_name ?? '—' }}
      </p>

      {{-- Images gallery --}}
      @php $images = ($stay->images ?? collect()); @endphp
      @if($images->isNotEmpty())
        <div class="row g-2 mb-4">
          @foreach($images->take(5) as $index => $img)
            <div class="col-{{ $index === 0 ? '12' : '6' }}">
              <img src="{{ $img->url }}" class="img-fluid rounded-4 shadow-sm hover-zoom w-100" alt="">
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-light border small">تصویری ثبت نشده است.</div>
      @endif

      {{-- About --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i> درباره اقامت‌گاه</h5>
          <p class="text-muted lh-lg">
            {{ $stay->address ?: 'توضیحات ثبت نشده است.' }}
          </p>
        </div>
      </div>

      {{-- Rules --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist text-warning me-2"></i> قوانین اقامت‌گاه</h5>
          <ul class="list-unstyled mb-0">
            @forelse(($stay->rules ?? collect()) as $rule)
              <li class="mb-2">
                <i class="bi bi-dot text-primary fs-4"></i>
                {{ $rule->rule_text }}
                <span class="badge {{ $rule->is_allowed ? 'bg-success' : 'bg-danger' }} ms-1">
                  {{ $rule->is_allowed ? 'مجاز' : 'ممنوع' }}
                </span>
              </li>
            @empty
              <li class="text-muted">قوانینی ثبت نشده است.</li>
            @endforelse
          </ul>
        </div>
      </div>

      {{-- Map --}}
      @if($stay->latitude && $stay->longitude)
        <div class="card border-0 shadow-sm rounded-4 mb-5">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-geo text-danger me-2"></i> موقعیت مکانی</h5>
            <div id="map" class="rounded-4 overflow-hidden" style="height:300px;width:100%"></div>
          </div>
        </div>
      @endif

    </div>

    {{-- Right column (booking/prices) --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 80px;">
        <div class="card-body">
          <h4 class="fw-bold text-dark mb-3">
            @php $mode = $stay->pricing_mode ?? 'per_person'; @endphp
            @if($mode === 'per_night')
              {!! displayStayPrice($stay, 'per_night') !!}
              <small class="text-muted fs-6 d-block mt-1">ریال / هر شب</small>
            @else
              {!! displayStayPrice($stay, 'per_person') !!}
              <small class="text-muted fs-6 d-block mt-1">ریال / هر نفر (پایه)</small>
            @endif
          </h4>
          @if(($stay->pricing_mode ?? 'per_person') === 'per_person' && !empty($stay->extra_person_price))
            <div class="small mb-3">
              نفر اضافه: {!! displayStayPrice($stay, 'extra_person') !!} <span class="text-muted"></span>
            </div>
          @endif
          <ul class="list-unstyled text-muted small mb-4">
            <li><i class="bi bi-person-fill text-primary me-1"></i> ظرفیت: {{ $stay->base_capacity }} / {{ $stay->capacity }}</li>
            <li><i class="bi bi-house-door text-primary me-1"></i> متراژ: {{ $stay->area ?? 'نامشخص' }} متر</li>
            <li><i class="bi bi-geo-alt text-primary me-1"></i> {{ $stay->province_name }} / {{ $stay->city_name }}</li>
          </ul>

          @guest('admin')
          @guest('host')
            @includeWhen(View::exists('partials.booking-modal'), 'partials.booking-modal')
            <button type="button" class="btn btn-success w-100 mt-3" data-bs-toggle="modal" data-bs-target="#bookingModal">
              <i class="bi bi-calendar-check"></i> رزرو اقامت‌گاه
            </button>
          @else
            <div class="alert alert-info text-center">فقط مسافران می‌توانند اقامت‌گاه رزرو کنند.</div>
          @endguest
          @endguest

        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
{{-- Leaflet --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($stay->latitude && $stay->longitude)
<script>
document.addEventListener('DOMContentLoaded', function () {
  const lat = {{ (float) $stay->latitude }};
  const lng = {{ (float) $stay->longitude }};
  const map = L.map('map', { zoomControl: true }).setView([lat, lng], 13);

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map);

  L.marker([lat, lng]).addTo(map);

  // Fix wrong tile positions when the container was not fully laid out
  setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  /* Keep tiles from inheriting global img rules and clip overflow */
  #map { position: relative; overflow: hidden; }
  .leaflet-container img { max-width: none !important; }
  .leaflet-container { z-index: 0; }
</style>
@endpush
@endsection