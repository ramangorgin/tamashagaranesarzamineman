@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.app')
@section('title', stayTypeToPersian($stay->category) . ' در ' . ($stay->city_name ?? 'نامشخص'))

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
      <li class="breadcrumb-item active text-primary">{{ $stay->city_name ?? '—' }}</li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-8">

      {{-- Title --}}
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="fw-bold text-dark">{{ stayTypeToPersian($stay->category) }} در {{ $stay->city_name ?? 'نامشخص' }}</h2>
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
              <img src="{{ $img->url }}" class="img-fluid rounded-4 shadow-sm hover-zoom w-100" alt="" style="object-fit: cover; height: {{ $index === 0 ? '400px' : '200px' }};">
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-light border small">تصویری ثبت نشده است.</div>
      @endif

      {{-- About (Expandable) --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i> درباره اقامت‌گاه</h5>
          @php
            $description = $stay->description ?? $stay->address ?? '';
            $descriptionPreview = \Illuminate\Support\Str::limit($description, 200);
            $isLongDescription = strlen($description) > 200;
          @endphp
          <div class="description-content">
            <p class="text-muted lh-lg mb-0" id="descriptionPreview">
              {{ $descriptionPreview ?: 'توضیحات ثبت نشده است.' }}
            </p>
            @if($isLongDescription)
              <p class="text-muted lh-lg mb-0 d-none" id="descriptionFull">
                {{ $description }}
              </p>
              <button type="button" class="btn btn-link p-0 mt-2 text-primary" id="toggleDescription">
                <i class="bi bi-chevron-down me-1"></i> نمایش بیشتر
              </button>
            @endif
          </div>
        </div>
      </div>

      {{-- Sleeping Space (Hotels Only) --}}
      @if($stay->category === 'hotel' && $stay->hotel && $stay->hotel->roomTypes->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4 rounded-4">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-bed text-primary me-2"></i> فضاهای خواب</h5>
            <div class="row g-3">
              @foreach($stay->hotel->roomTypes as $roomType)
                <div class="col-12">
                  <div class="border rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h6 class="fw-semibold mb-0">{{ $roomType->title }}</h6>
                      <div class="text-end">
                        <div class="fw-bold text-primary">{{ number_format($roomType->price_per_night) }} <small class="text-muted">ریال</small></div>
                        <small class="text-muted">هر شب</small>
                      </div>
                    </div>
                    <div class="row g-2 small text-muted mb-2">
                      <div class="col-auto">
                        <i class="bi bi-people me-1"></i> ظرفیت: {{ $roomType->capacity }} نفر
                      </div>
                      @if($roomType->area)
                        <div class="col-auto">
                          <i class="bi bi-rulers me-1"></i> متراژ: {{ number_format($roomType->area) }} متر
                        </div>
                      @endif
                      @if($roomType->total_rooms)
                        <div class="col-auto">
                          <i class="bi bi-door-open me-1"></i> تعداد اتاق: {{ $roomType->total_rooms }}
                        </div>
                      @endif
                    </div>
                    @if($roomType->beds->isNotEmpty())
                      <div class="mt-2 pt-2 border-top">
                        <small class="text-muted d-block mb-1">ترکیب تخت‌ها:</small>
                        <div class="d-flex flex-wrap gap-2">
                          @foreach($roomType->beds as $bed)
                            <span class="badge bg-light text-dark border">
                              {{ $bed->title_fa }} × {{ $bed->pivot->quantity }}
                            </span>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      {{-- Amenities (Expandable) --}}
      @php
        $amenities = [];
        // Check if amenities_json column exists and has data
        if (isset($stay->amenities_json) && !empty($stay->amenities_json)) {
          $amenities = json_decode($stay->amenities_json, true) ?: [];
        }
        // For hotels, also include hotel facilities
        if ($stay->category === 'hotel' && $stay->hotel) {
          $hotelFacilities = [
            ['name' => 'لابی', 'has' => $stay->hotel->has_lobby ?? false],
            ['name' => 'آسانسور', 'has' => $stay->hotel->has_elevator ?? false],
            ['name' => 'رستوران', 'has' => $stay->hotel->has_restaurant ?? false],
            ['name' => 'پارکینگ', 'has' => $stay->hotel->has_parking ?? false],
            ['name' => 'صبحانه', 'has' => $stay->hotel->has_breakfast ?? false],
            ['name' => 'پذیرش 24 ساعته', 'has' => $stay->hotel->has_24h_reception ?? false],
          ];
          foreach ($hotelFacilities as $facility) {
            if ($facility['has']) {
              $amenities[] = ['name' => $facility['name'], 'has' => true, 'description' => ''];
            }
          }
        }
        $amenitiesPreview = array_slice($amenities, 0, 6);
        $hasMoreAmenities = count($amenities) > 6;
      @endphp
      @if(!empty($amenities))
        <div class="card border-0 shadow-sm mb-4 rounded-4">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-star text-warning me-2"></i> امکانات</h5>
            <div class="amenities-content">
              <div class="row g-3" id="amenitiesPreview">
                @foreach($amenitiesPreview as $amenity)
                  <div class="col-md-4 col-sm-6">
                    <div class="d-flex align-items-center">
                      @if($amenity['has'] ?? true)
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                      @else
                        <i class="bi bi-x-circle-fill text-muted me-2 fs-5" style="opacity: 0.5; text-decoration: line-through;"></i>
                      @endif
                      <div>
                        <div class="fw-semibold {{ ($amenity['has'] ?? true) ? '' : 'text-muted' }}" style="{{ ($amenity['has'] ?? true) ? '' : 'opacity: 0.6; text-decoration: line-through;' }}">
                          {{ $amenity['name'] ?? '' }}
                        </div>
                        @if(!empty($amenity['description']))
                          <small class="text-muted d-block">{{ $amenity['description'] }}</small>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              @if($hasMoreAmenities)
                <div class="row g-3 d-none" id="amenitiesFull">
                  @foreach(array_slice($amenities, 6) as $amenity)
                    <div class="col-md-4 col-sm-6">
                      <div class="d-flex align-items-center">
                        @if($amenity['has'] ?? true)
                          <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        @else
                          <i class="bi bi-x-circle-fill text-muted me-2 fs-5" style="opacity: 0.5; text-decoration: line-through;"></i>
                        @endif
                        <div>
                          <div class="fw-semibold {{ ($amenity['has'] ?? true) ? '' : 'text-muted' }}" style="{{ ($amenity['has'] ?? true) ? '' : 'opacity: 0.6; text-decoration: line-through;' }}">
                            {{ $amenity['name'] ?? '' }}
                          </div>
                          @if(!empty($amenity['description']))
                            <small class="text-muted d-block">{{ $amenity['description'] }}</small>
                          @endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
                <button type="button" class="btn btn-link p-0 mt-3 text-primary" id="toggleAmenities">
                  <i class="bi bi-chevron-down me-1"></i> نمایش بیشتر
                </button>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Price Calendar --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-calendar3 text-info me-2"></i> تقویم قیمت</h5>
          <div id="priceCalendar" class="mb-3"></div>
          <small class="text-muted d-flex gap-2 flex-wrap mt-2">
            <span class="badge bg-primary text-white">قیمت عادی</span>
            <span class="badge bg-warning text-dark">قیمت پیک</span>
            <span class="badge bg-success text-white">قیمت تخفیف</span>
          </small>
        </div>
      </div>

      {{-- Cancellation Policy --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-success me-2"></i> سیاست لغو رزرو</h5>
          <p class="text-muted lh-lg mb-0">
            لغو رزرو تا 48 ساعت قبل از تاریخ ورود بدون هزینه امکان‌پذیر است. در صورت لغو کمتر از 48 ساعت قبل از ورود، 50% از مبلغ رزرو کسر می‌شود. در صورت عدم حضور در تاریخ مقرر، کل مبلغ رزرو کسر خواهد شد.
          </p>
        </div>
      </div>

      {{-- Rules (Expandable) --}}
      @php
        $rules = $stay->rules ?? collect();
        $rulesPreview = $rules->take(3);
        $hasMoreRules = $rules->count() > 3;
      @endphp
      @if($rules->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4 rounded-4">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist text-warning me-2"></i> قوانین اقامت‌گاه</h5>
            <div class="rules-content">
              <ul class="list-unstyled mb-0" id="rulesPreview">
                @foreach($rulesPreview as $rule)
                  <li class="mb-2">
                    @if($rule->is_allowed)
                      <i class="bi bi-check-circle-fill text-success me-2"></i>
                    @else
                      <i class="bi bi-x-circle-fill text-danger me-2"></i>
                    @endif
                    <span class="{{ $rule->is_allowed ? '' : 'text-muted' }}">
                      {{ $rule->rule_text }}
                    </span>
                  </li>
                @endforeach
              </ul>
              @if($hasMoreRules)
                <ul class="list-unstyled mb-0 d-none" id="rulesFull">
                  @foreach($rules->skip(3) as $rule)
                    <li class="mb-2">
                      @if($rule->is_allowed)
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                      @else
                        <i class="bi bi-x-circle-fill text-danger me-2"></i>
                      @endif
                      <span class="{{ $rule->is_allowed ? '' : 'text-muted' }}">
                        {{ $rule->rule_text }}
                      </span>
                    </li>
                  @endforeach
                </ul>
                <button type="button" class="btn btn-link p-0 mt-2 text-primary" id="toggleRules">
                  <i class="bi bi-chevron-down me-1"></i> نمایش بیشتر
                </button>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Map (Approximate Area) --}}
      @if($stay->latitude && $stay->longitude)
        <div class="card border-0 shadow-sm rounded-4 mb-5">
          <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-geo text-danger me-2"></i> موقعیت مکانی</h5>
            <div id="map" class="rounded-4 overflow-hidden" style="height:300px;width:100%"></div>
            <p class="text-muted small mt-2 mb-0">
              <i class="bi bi-info-circle me-1"></i> موقعیت نمایش داده شده تقریبی است و برای حفظ حریم خصوصی، موقعیت دقیق پس از رزرو نمایش داده می‌شود.
            </p>
          </div>
        </div>
      @endif

    </div>

    {{-- Right column (Reservation Sidebar) --}}
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
            <div class="small mb-3 text-muted">
              <i class="bi bi-person-plus me-1"></i> نفر اضافه: {!! displayStayPrice($stay, 'extra_person') !!} ریال
            </div>
          @endif
          
          @if($stay->category === 'hotel' && $stay->hotel)
            <div class="mb-3">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-star-fill text-warning me-2"></i>
                <span class="fw-semibold">رتبه: {{ $stay->hotel->star_rating ?? '—' }} ستاره</span>
              </div>
              @if($stay->hotel->license_number)
                <div class="d-flex align-items-center small text-muted">
                  <i class="bi bi-file-earmark-text me-2"></i>
                  <span>شماره پروانه: {{ $stay->hotel->license_number }}</span>
                </div>
              @endif
            </div>
          @endif

          <ul class="list-unstyled text-muted small mb-4 border-top pt-3">
            <li class="mb-2"><i class="bi bi-person-fill text-primary me-2"></i> ظرفیت: {{ $stay->base_capacity }} / {{ $stay->capacity }} نفر</li>
            @if($stay->area)
              <li class="mb-2"><i class="bi bi-house-door text-primary me-2"></i> متراژ: {{ number_format($stay->area) }} متر</li>
            @endif
            <li class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i> {{ $stay->province_name }} / {{ $stay->city_name }}</li>
            @if($stay->checkin_time)
              <li class="mb-2"><i class="bi bi-clock text-primary me-2"></i> ورود: {{ \Carbon\Carbon::parse($stay->checkin_time)->format('H:i') }}</li>
            @endif
            @if($stay->checkout_time)
              <li><i class="bi bi-clock-history text-primary me-2"></i> خروج: {{ \Carbon\Carbon::parse($stay->checkout_time)->format('H:i') }}</li>
            @endif
          </ul>

          @guest('admin')
          @guest('host')
            @includeWhen(View::exists('partials.booking-modal'), 'partials.booking-modal')
            <button type="button" class="btn btn-success w-100 mt-3 btn-lg fw-semibold" data-bs-toggle="modal" data-bs-target="#bookingModal" style="transition: all 0.3s ease;">
              <i class="bi bi-calendar-check me-2"></i> رزرو اقامت‌گاه
            </button>
          @else
            <div class="alert alert-info text-center small">فقط مسافران می‌توانند اقامت‌گاه رزرو کنند.</div>
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
{{-- FullCalendar for price calendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/fa.js"></script>

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

  // Add approximate circular area instead of exact marker
  const circle = L.circle([lat, lng], {
    color: '#3388ff',
    fillColor: '#3388ff',
    fillOpacity: 0.2,
    radius: 500 // 500 meters radius
  }).addTo(map);

  // Fit map to show the circle
  map.fitBounds(circle.getBounds());

  // Fix wrong tile positions when the container was not fully laid out
  setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Expandable sections
  const toggleDescription = document.getElementById('toggleDescription');
  if (toggleDescription) {
    toggleDescription.addEventListener('click', function() {
      const preview = document.getElementById('descriptionPreview');
      const full = document.getElementById('descriptionFull');
      const icon = this.querySelector('i');
      
      if (full.classList.contains('d-none')) {
        preview.classList.add('d-none');
        full.classList.remove('d-none');
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        this.innerHTML = '<i class="bi bi-chevron-up me-1"></i> نمایش کمتر';
      } else {
        preview.classList.remove('d-none');
        full.classList.add('d-none');
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        this.innerHTML = '<i class="bi bi-chevron-down me-1"></i> نمایش بیشتر';
      }
    });
  }

  const toggleAmenities = document.getElementById('toggleAmenities');
  if (toggleAmenities) {
    toggleAmenities.addEventListener('click', function() {
      const preview = document.getElementById('amenitiesPreview');
      const full = document.getElementById('amenitiesFull');
      const icon = this.querySelector('i');
      
      if (full.classList.contains('d-none')) {
        full.classList.remove('d-none');
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        this.innerHTML = '<i class="bi bi-chevron-up me-1"></i> نمایش کمتر';
      } else {
        full.classList.add('d-none');
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        this.innerHTML = '<i class="bi bi-chevron-down me-1"></i> نمایش بیشتر';
      }
    });
  }

  const toggleRules = document.getElementById('toggleRules');
  if (toggleRules) {
    toggleRules.addEventListener('click', function() {
      const preview = document.getElementById('rulesPreview');
      const full = document.getElementById('rulesFull');
      const icon = this.querySelector('i');
      
      if (full.classList.contains('d-none')) {
        full.classList.remove('d-none');
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        this.innerHTML = '<i class="bi bi-chevron-up me-1"></i> نمایش کمتر';
      } else {
        full.classList.add('d-none');
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        this.innerHTML = '<i class="bi bi-chevron-down me-1"></i> نمایش بیشتر';
      }
    });
  }

  // Price Calendar
  const calendarEl = document.getElementById('priceCalendar');
  if (calendarEl) {
    @php
      // Pre-calculate prices for next 3 months
      $basePrice = $stay->pricing_mode === 'per_night' ? ($stay->price_per_night ?? 0) : ($stay->price_per_person ?? 0);
      $priceEvents = [];
      if ($basePrice > 0) {
        $startDate = \Carbon\Carbon::today();
        $endDate = \Carbon\Carbon::today()->addMonths(3);
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
          $dateStr = $currentDate->toDateString();
          $priceInfo = $stay->calculateAdjustedPrice($basePrice, $dateStr);
          $priceEvents[] = [
            'date' => $dateStr,
            'price' => $priceInfo['adjusted'],
            'type' => $priceInfo['type'] ?? 'normal'
          ];
          $currentDate->addDay();
        }
      }
    @endphp
    
    const priceEventsData = @json($priceEvents);
    const basePrice = {{ $basePrice }};
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'fa',
      direction: 'rtl',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listWeek'
      },
      events: function(fetchInfo, successCallback, failureCallback) {
        const events = [];
        
        if (basePrice <= 0) {
          successCallback([]);
          return;
        }
        
        // Filter price events for the requested date range
        const startStr = fetchInfo.startStr;
        const endStr = fetchInfo.endStr;
        
        priceEventsData.forEach(function(priceEvent) {
          if (priceEvent.date >= startStr && priceEvent.date <= endStr) {
            const priceType = priceEvent.type;
            events.push({
              title: priceType === 'peak' ? 'پیک: ' + priceEvent.price.toLocaleString('fa-IR') + ' ریال' : 
                     priceType === 'discount' ? 'تخفیف: ' + priceEvent.price.toLocaleString('fa-IR') + ' ریال' :
                     priceEvent.price.toLocaleString('fa-IR') + ' ریال',
              start: priceEvent.date,
              backgroundColor: priceType === 'peak' ? '#ffc107' : priceType === 'discount' ? '#28a745' : '#0d6efd',
              borderColor: priceType === 'peak' ? '#ffc107' : priceType === 'discount' ? '#28a745' : '#0d6efd',
              textColor: priceType === 'peak' ? '#000' : '#fff',
              display: 'background'
            });
          }
        });
        
        successCallback(events);
      },
      dayCellContent: function(args) {
        return args.dayNumberText;
      }
    });
    calendar.render();
  }

  // Reservation button animation
  const reserveBtn = document.querySelector('[data-bs-target="#bookingModal"]');
  if (reserveBtn) {
    reserveBtn.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 4px 12px rgba(25, 135, 84, 0.3)';
    });
    reserveBtn.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '';
    });
  }
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  /* Keep tiles from inheriting global img rules and clip overflow */
  #map { position: relative; overflow: hidden; }
  .leaflet-container img { max-width: none !important; }
  .leaflet-container { z-index: 0; }
  
  /* Hover zoom effect for images */
  .hover-zoom {
    transition: transform 0.3s ease;
  }
  .hover-zoom:hover {
    transform: scale(1.02);
  }
  
  /* Calendar styling */
  .fc {
    direction: rtl;
  }
  .fc-toolbar-title {
    font-size: 1.25rem;
  }
  .fc-button {
    background-color: #0d6efd;
    border-color: #0d6efd;
  }
  .fc-button:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
  }
  .fc-button-active {
    background-color: #0a58ca;
    border-color: #0a58ca;
  }
</style>
@endpush
@endsection
