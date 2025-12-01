@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.host')
@section('title','ایجاد اقامت‌گاه جدید')

@section('breadcrumb')
    @if(($role ?? null)==='admin')
        <li class="breadcrumb-item"><a href="{{ route('admin.stays.index') }}">اقامت‌گاه‌ها</a></li>
        <li class="breadcrumb-item active">ایجاد</li>
    @endif
@endsection

@section('content')
<div class="container py-4">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:1000px;">
    <div class="card-body p-4">
      <h4 class="fw-bold text-primary text-center mb-4">
        <i class="bi bi-building-add me-2"></i> ثبت اقامت‌گاه جدید
      </h4>
      @php $role = $role ?? (auth('admin')->check() ? 'admin' : 'host'); @endphp
      @if($role==='admin')
        <div class="card border-0 mb-3">
          <div class="card-body p-3 d-flex flex-column gap-2">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="bi bi-person-badge me-2"></i> انتخاب میزبان</div>
              <div class="small text-muted">ابتدا میزبان را تعیین کنید</div>
            </div>
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label small">نام میزبان (جستجو فقط بر اساس نام)</label>
                <input type="text" id="hostSearch" class="form-control" placeholder="مثال: علی رضایی" autocomplete="off">
                <div id="hostSearchResults" class="list-group mt-1" style="max-height:220px;overflow:auto;display:none"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label small">موبایل (فقط جستجو با شماره)</label>
                <input type="text" id="hostPhone" class="form-control" inputmode="tel" dir="ltr" placeholder="09XXXXXXXXX">
              </div>
              <div class="col-md-2 d-grid">
                <label class="form-label small">&nbsp;</label>
                <button type="button" id="btnQuickCreateHost" class="btn btn-outline-primary">ایجاد سریع</button>
              </div>
            </div>
            <div id="hostSelectedBox" class="alert alert-warning d-flex justify-content-between align-items-center small" style="display:none">
              <div>
                <i class="bi bi-person-badge me-2"></i>
                میزبان: <strong id="hostSelName">—</strong>
                <span class="mx-2">|</span>
                موبایل: <span id="hostSelPhone" dir="ltr"></span>
                <span class="mx-2">|</span>
                وضعیت: <span id="hostSelStatus"></span>
              </div>
              <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnChangeHost">تغییر</button>
              </div>
            </div>
            <!-- moved hidden host_id input inside the form below -->
          </div>
        </div>
      @endif
      @if(($role ?? null)!=='admin')
        @if($errors->any())
          <div class="alert alert-danger small mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif
      @endif
      <form id="stayForm" method="POST" action="{{ isset($role)&&$role==='admin' ? route('admin.stays.store') : route('host.stays.store') }}" enctype="multipart/form-data">
        @if($role==='admin')
          <input type="hidden" name="host_id" id="host_id" value="{{ isset($host)?$host->id:'' }}">
        @endif
        @csrf
        {{-- Step indicators --}}
        <div class="d-flex flex-wrap justify-content-center mb-4 gap-2 small fw-semibold">
          <div class="step-dot active" data-step="1">مشخصات</div>
          <div class="step-dot" data-step="2">موقعیت</div>
          <div class="step-dot" data-step="3">ظرفیت</div>
          <div class="step-dot" data-step="4">قیمت</div>
          <div class="step-dot" data-step="5">تصاویر</div>
          <div class="step-dot" data-step="6">قوانین</div>
          @if(($role ?? null)!=='admin')
            <div class="step-dot" data-step="7">تأیید</div>
          @endif
        </div>
        {{-- Step 1 --}}
        <div id="step1">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">عنوان اقامت‌گاه</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">دسته‌بندی</label>
              <select name="category" class="form-select" required>
                @php $cats=['hotel','villa','apartment','ecolodge','suite','motel','house']; @endphp
                @foreach($cats as $c)
                  <option value="{{ $c }}">{{ stayTypeToPersian($c) }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 2: Location --}}
        <div id="step2" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">استان</label>
              <select name="province_id" id="province" class="form-select" required></select>
              <input type="hidden" name="province_name" id="province_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">شهر</label>
              <select name="city_id" id="city" class="form-select" required disabled></select>
              <input type="hidden" name="city_name" id="city_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">بخش / شهرستان</label>
              <select name="county_id" id="county" class="form-select" required disabled></select>
              <input type="hidden" name="county_name" id="county_name">
            </div>
            <div class="col-md-6">
              <label class="form-label">روستا (اختیاری)</label>
              <select name="village_name" id="village" class="form-select" disabled>
                <option value="">(انتخاب کنید)</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">آدرس دقیق</label>
              <textarea name="address" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-12">
              <label class="form-label d-flex align-items-center justify-content-between">
                موقعیت روی نقشه
                <small class="text-muted ms-2">روی نقطه کلیک کنید</small>
              </label>
              <div id="map" style="height:380px;border:1px solid #dee2e6;border-radius:14px;"></div>
              <input type="hidden" name="latitude" id="lat">
              <input type="hidden" name="longitude" id="lng">
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 3: Capacity --}}
        <div id="step3" class="d-none">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">ظرفیت پایه</label>
              <input type="number" min="1" name="base_capacity" class="form-control" value="1" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">ظرفیت کل</label>
              <input type="number" min="1" name="capacity" class="form-control" value="1" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">نفرات اضافه</label>
              <input type="number" min="0" name="extra_capacity" class="form-control" value="0">
            </div>
            <div class="col-md-3">
              <label class="form-label">متراژ (متر)</label>
              <input type="number" min="0" name="area" class="form-control">
            </div>
            <div class="col-12">
              <div class="row g-3 mt-1">
                <div class="col-6 col-md-3">
                  <label class="form-label">اتاق خواب</label>
                  <input type="number" min="0" name="bedrooms" class="form-control" value="1">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">تخت دبل</label>
                  <input type="number" min="0" name="double_beds" class="form-control" value="0">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">تخت سینگل</label>
                  <input type="number" min="0" name="single_beds" class="form-control" value="0">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">سوییت / کف‌خواب</label>
                  <input type="number" min="0" name="floor_beds" class="form-control" value="0">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">حمام</label>
                  <input type="number" min="0" name="bathrooms" class="form-control" value="0">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">سرویس ایرانی</label>
                  <input type="number" min="0" name="iranian_toilets" class="form-control" value="0">
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label">سرویس فرنگی</label>
                  <input type="number" min="0" name="western_toilets" class="form-control" value="0">
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 4: Pricing --}}
        <div id="step4" class="d-none">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">نوع قیمت‌گذاری</label>
              <select name="pricing_mode" id="pricing_mode" class="form-select" required>
                <option value="per_person">به ازای هر نفر</option>
                <option value="per_night">به ازای هر شب</option>
              </select>
            </div>
            <div class="col-md-4 pricing-per-person">
              <label class="form-label">قیمت هر نفر (ظرفیت پایه) <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="price_per_person" id="price_per_person" class="form-control price-field" data-price-format value="0">
            </div>
            <div class="col-md-4 pricing-per-night d-none">
              <label class="form-label">قیمت هر شب <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="price_per_night" id="price_per_night" class="form-control price-field" data-price-format value="0">
            </div>
            <div class="col-md-4 extra-person-group">
              <label class="form-label">قیمت نفر اضافه <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="extra_person_price" class="form-control price-field" data-price-format value="0">
            </div>
              <div class="col-md-4">
                <label class="form-label">درصد کمیسیون سایت</label>
                <div class="input-group">
                  <input type="number" name="site_commission" min="0" max="100" step="0.5" value="10" class="form-control" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">حداکثر تخفیف (عادی)</label>
                <div class="input-group">
                  <input type="number" name="max_discount_normal" min="0" max="100" step="0.5" value="20" class="form-control" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">حداکثر تخفیف (پیک)</label>
                <div class="input-group">
                  <input type="number" name="max_discount_peak" min="0" max="100" step="0.5" value="10" class="form-control" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <input type="hidden" name="site_commission" value="0">
              <input type="hidden" name="max_discount_normal" value="0">
              <input type="hidden" name="max_discount_peak" value="0">
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 5: Images --}}
        <div id="step5" class="d-none">
          <div class="mb-3">
            <label class="form-label">تصاویر اقامت‌گاه (حداکثر 10)</label>
            <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple>
            <small class="text-muted d-block mt-2" style="cursor: pointer;">
              برای افزودن تصویر، روی جعبه بالا کلیک کنید. هر بار یک تصویر انتخاب کنید و در صورت نیاز چندین بار کلیک نمایید.
              با دوبار کلیک روی تصویر بندانگشتی، آن را به‌عنوان «تصویر اصلی» انتخاب کنید.
            </small>
          </div>
          <input type="hidden" name="main_image_index" id="main_image_index">
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 6: Rules --}}
        <div id="step6" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">ساعت ورود</label>
              <input type="time" name="checkin_time" id="checkin_time" class="form-control" value="14:00">
            </div>
            <div class="col-md-6">
              <label class="form-label">ساعت خروج</label>
              <input type="time" name="checkout_time" id="checkout_time" class="form-control" value="12:00">
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
            <label class="form-label mb-0">سایر قوانین</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRuleBtn">
              <i class="bi bi-plus-lg"></i> افزودن قانون
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" id="rulesTable">
              <thead class="table-light">
                <tr>
                  <th style="width:70%">متن قانون</th>
                  <th style="width:16%">وضعیت</th>
                  <th style="width:14%">حذف</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <textarea name="rules_json" id="rules_json" class="d-none"></textarea>
          <div class="mt-3 small text-muted">نمونه‌ها: «برگزاری پارتی: ممنوع»، «پخش آهنگ: مجاز»</div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            @if(($role ?? null)==='admin')
              <button type="submit" class="btn btn-success">ثبت اقامت‌گاه <i class="bi bi-check2-circle"></i></button>
            @else
              <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
            @endif
          </div>
        </div>
        {{-- Step 7: Review --}}
        @if(($role ?? null)!=='admin')
          <div id="step7" class="d-none">
            <div class="alert alert-info d-flex align-items-center">
              <i class="bi bi-info-circle-fill me-2 fs-5"></i>
              بررسی نهایی و ارسال برای تأیید مدیر.
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
              <button type="submit" class="btn btn-success">ثبت اقامت‌گاه <i class="bi bi-check2-circle"></i></button>
            </div>
          </div>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
.step-dot{background:#e9ecef;color:#6c757d;padding:6px 10px;border-radius:18px;min-width:70px;text-align:center;transition:.25s;cursor:pointer;font-size:.75rem;}
.step-dot.active{background:#0d6efd;color:#fff;box-shadow:0 0 0 3px rgba(13,110,253,.15);}
#map{position:relative;overflow:hidden;height:380px;border:1px solid #dee2e6;border-radius:14px;}
/* Prevent global img rules from breaking Leaflet tiles */
.leaflet-container img{max-width:none!important;}
.leaflet-container{z-index:0;}
.image-box{position:relative}
.image-box img{width:100%;height:140px;object-fit:cover;border-radius:10px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.15)}
.main-badge{position:absolute;top:6px;left:6px;background:#ffc107;color:#212529;padding:4px 8px;border-radius:20px;font-size:.7rem;cursor:pointer;display:flex;align-items:center;gap:4px;box-shadow:0 0 0 2px rgba(255,193,7,.4)}
.remove-img{position:absolute;bottom:6px;right:6px;background:#dc3545;color:#fff;border:none;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer}
.rule-row-removed{opacity:.4;text-decoration:line-through}
.price-field{text-align:left;direction:ltr}
/* FilePond styles */
.filepond--root{border-radius:12px;overflow:hidden}
.filepond--panel-root{border-radius:12px}
.filepond--drop-label{
  color:#334155; font-size:.95rem;
}
.filepond--item.is-main .filepond--item-panel{
  box-shadow:0 0 0 2px #ffc107 inset;
}
.filepond--item.is-main::after{
  content:'تصویر اصلی';
  position:absolute; top:6px; left:6px;
  background:#ffc107; color:#212529;
  padding:4px 8px; border-radius:12px; font-size:.7rem;
  box-shadow:0 0 0 2px rgba(255,193,7,.4);
}
</style>
<link href="https://unpkg.com/filepond@4.30.4/dist/filepond.min.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview@4.6.11/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview@4.6.11/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond@4.30.4/dist/filepond.min.js"></script>
<script>
(function(){
  const role='{{ $role }}';
  const totalSteps = role==='admin' ? 6 : 7;
  let current=1;
  let map, marker=null;
  function initMap(){
    if(map) return;
    map = L.map('map').setView([32,53],5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
      maxZoom:19,
      attribution:'© OpenStreetMap'
    }).addTo(map);
    map.on('click',e=>{
      const {lat,lng}=e.latlng;
      if(marker) marker.setLatLng(e.latlng); else marker=L.marker(e.latlng).addTo(map);
      document.getElementById('lat').value=lat.toFixed(6);
      document.getElementById('lng').value=lng.toFixed(6);
      Swal.fire({toast:true,position:'top',icon:'success',title:'مختصات ثبت شد',showConfirmButton:false,timer:1300});
    });
  }

  function show(step){
    for(let i=1;i<=totalSteps;i++){
      const el=document.getElementById('step'+i);
      if(!el) continue;
      if(i===step){ el.classList.remove('d-none'); el.style.display='block'; }
      else { el.style.display='none'; }
      const dot=document.querySelector('.step-dot[data-step="'+i+'"]');
      dot && dot.classList.toggle('active', i===step);
    }
    current=step;
    if(step===2){ initMap(); setTimeout(()=> map.invalidateSize(), 150); }
    window.scrollTo({top:0,behavior:'smooth'});
  }
  document.querySelectorAll('.next-btn').forEach(b=>b.addEventListener('click',()=>{
    if(!validateStep(current)) return;
    if(current<totalSteps) show(current+1);
    if(current===6) syncRulesJson();
  }));
  document.querySelectorAll('.prev-btn').forEach(b=>b.addEventListener('click',()=>{ if(current>1) show(current-1); }));
  document.querySelectorAll('.step-dot').forEach(d=> d.addEventListener('click',()=>{const s=parseInt(d.dataset.step); if(s<current) show(s);} ));

  function validateStep(step){
    // Admin must select host first
    if(role==='admin' && step===1){
      const hid=document.getElementById('host_id').value;
      if(!hid){ Swal.fire({icon:'warning',title:'ابتدا میزبان را انتخاب کنید'}); return false; }
    }
    if(step===2 && !document.getElementById('lat').value){
      Swal.fire({icon:'warning',title:'مختصات انتخاب نشده'}); return false;
    }
    if(step===5){ // images limit
      const inputEl=document.getElementById('imagesInput');
      const pondCount = (typeof FilePond!=='undefined' && inputEl? FilePond.find(inputEl)?.getFiles().length : 0) || 0;
      const filesLen = inputEl?.files?.length || 0;
      const total = Math.max(pondCount, filesLen);
      if(total>10){ Swal.fire({icon:'error',title:'حداکثر 10 تصویر'}); return false; }
    }
    const container=document.getElementById('step'+step);
    let invalid=false;
    container.querySelectorAll('[required]').forEach(inp=>{
      if(!inp.value){ inp.classList.add('is-invalid'); invalid=true; } else inp.classList.remove('is-invalid');
    });
    if(invalid){ Swal.fire({icon:'error',title:'فیلدهای ضروری خالی است'}); return false; }
    return true;
  }

  // Price formatting (ریال) with commas
  function formatPrice(val){
    val = val.replace(/[^\d]/g,'');
    if(!val) return '0';
    return val.replace(/\B(?=(\d{3})+(?!\d))/g,',');
  }
  document.querySelectorAll('[data-price-format]').forEach(inp=>{
    inp.addEventListener('input',()=> {
      const caret=inp.selectionStart;
      inp.value=formatPrice(inp.value);
      inp.setSelectionRange(caret,caret);
    });
    inp.addEventListener('blur',()=>{ inp.value=formatPrice(inp.value); });
  });

  // Geo cascading (static JSON from /public/data)
  const provinceSel=document.getElementById('province'),
        citySel=document.getElementById('city'),
        countySel=document.getElementById('county'),
        villageSel=document.getElementById('village'),
        hProvince=document.getElementById('province_name'),
        hCity=document.getElementById('city_name'),
        hCounty=document.getElementById('county_name');
  function opt(v,t){return `<option value="${v}">${t}</option>`;}
  function reset(sel,ph,disable=true){ sel.innerHTML=opt('',ph); if(disable) sel.setAttribute('disabled','disabled'); else sel.removeAttribute('disabled'); }
  let DATA_PROVINCES=null, DATA_CITIES=null, DATA_COUNTIES=null, DATA_VILLAGES=null;
  const URLS={
    provinces: "{{ asset('data/provinces.json') }}",
    cities: "{{ asset('data/provinces_cities.json') }}",
    counties: "{{ asset('data/provinces_cities_counties.json') }}",
    villages: "{{ asset('data/provinces_cities_counties_villages.json') }}",
  };
  const FALLBACK={
    provinces: "{{ route('geo.provinces') }}",
    cities: (pid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities",
    counties: (pid,cid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities/"+cid+"/counties",
    villages: (pid,cid,coid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities/"+cid+"/counties/"+coid+"/villages",
  };
  async function loadProvinces(){
    try {
      if(!DATA_PROVINCES){
        DATA_PROVINCES = await fetch(URLS.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json());
      }
      populateProvinces();
    } catch(e){
      try{
        const rows = await fetch(FALLBACK.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json());
        provinceSel.innerHTML=opt('','انتخاب استان');
        rows.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.id||p.provinceId,p.name||p.provinceName)) );
        provinceSel.removeAttribute('disabled');
      }catch{ provinceSel.innerHTML=opt('', 'خطا در بارگذاری استان‌ها'); }
    }
  }
  function populateProvinces(){
    provinceSel.innerHTML=opt('','انتخاب استان');
    DATA_PROVINCES.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.provinceId,p.provinceName)));
    provinceSel.removeAttribute('disabled');
  }
  provinceSel.addEventListener('change',()=>{
    const pid=provinceSel.value; hProvince.value=provinceSel.options[provinceSel.selectedIndex]?.text||'';
    reset(citySel,'در حال بارگذاری...',false); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!pid){ reset(citySel,'انتخاب شهر'); return; }
    (async () => {
      try{
        if(!DATA_CITIES){ DATA_CITIES = await fetch(URLS.cities,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const cities = DATA_CITIES.filter(row=>row.provinceId===pid);
        citySel.innerHTML=opt('','انتخاب شهر');
        cities.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)) );
        citySel.removeAttribute('disabled');
      }catch{
        try{
          const rows = await fetch(FALLBACK.cities(pid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          citySel.innerHTML=opt('','انتخاب شهر'); rows.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.id||c.cityId,c.name||c.cityName)) );
          citySel.removeAttribute('disabled');
        }catch{ citySel.innerHTML=opt('', 'خطا در بارگذاری شهرها'); }
      }
    })();
  });
  citySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value; hCity.value=citySel.options[citySel.selectedIndex]?.text||'';
    reset(countySel,'در حال بارگذاری...',false); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!cid){ reset(countySel,'انتخاب بخش'); return; }
    (async () => {
      try{
        if(!DATA_COUNTIES){ DATA_COUNTIES = await fetch(URLS.counties,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const counties = DATA_COUNTIES.filter(row=>row.provinceId===pid && row.cityId===cid);
        countySel.innerHTML=opt('','انتخاب بخش/شهرستان');
        counties.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.countyId,c.countyName)) );
        countySel.removeAttribute('disabled');
      }catch{
        try{
          const rows = await fetch(FALLBACK.counties(pid,cid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          countySel.innerHTML=opt('','انتخاب بخش/شهرستان'); rows.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.id||c.countyId,c.name||c.countyName)) );
          countySel.removeAttribute('disabled');
        }catch{ countySel.innerHTML=opt('', 'خطا در بارگذاری بخش'); }
      }
    })();
  });
  countySel.addEventListener('change',()=>{
    const pid=provinceSel.value,cid=citySel.value,coid=countySel.value; hCounty.value=countySel.options[countySel.selectedIndex]?.text||'';
    reset(villageSel,'در حال بارگذاری...',false);
    if(!coid){ reset(villageSel,'(اختیاری) انتخاب روستا'); return; }
    (async () => {
      try{
        if(!DATA_VILLAGES){ DATA_VILLAGES = await fetch(URLS.villages,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const villages = DATA_VILLAGES.filter(row=>row.provinceId===pid && row.cityId===cid && row.countyId===coid);
        villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
        if(!villages.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
        else villages.forEach(v=> { if(v.villageName && v.villageName.trim()) villageSel.insertAdjacentHTML('beforeend', opt(v.villageName,v.villageName)); });
        villageSel.removeAttribute('disabled');
      }catch{
        try{
          const rows = await fetch(FALLBACK.villages(pid,cid,coid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
          if(!rows.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
          else rows.forEach(v=> { const name=v.name||v.villageName; if(name && name.trim()) villageSel.insertAdjacentHTML('beforeend', opt(name,name)); });
          villageSel.removeAttribute('disabled');
        }catch{ villageSel.innerHTML=opt('', 'خطا در بارگذاری روستاها'); }
      }
    })();
  });
  reset(provinceSel,'در حال بارگذاری...',false); reset(citySel,'ابتدا استان را انتخاب کنید'); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
  loadProvinces();

  // Admin host selection logic
  (function(){
    const role='{{ $role }}';
    if(role!=='admin') return;
    const hostId=document.getElementById('host_id');
    const box=document.getElementById('hostSelectedBox');
    const selName=document.getElementById('hostSelName');
    const selPhone=document.getElementById('hostSelPhone');
    const selStatus=document.getElementById('hostSelStatus');
    function statusToFa(s){
      switch(String(s||'').toLowerCase()){
        case 'approved': return 'تأیید شده';
        case 'pending': return 'در انتظار بررسی';
        case 'rejected': return 'رد شده';
        default: return s||'—';
      }
    }
    function bindHost(h){
      hostId.value=h.id;
      selName.textContent=h.name||'—';
      selPhone.textContent=h.phone||'';
      selStatus.textContent=statusToFa(h.status);
      box.style.display='flex';
    }
    function clearHost(){
      hostId.value='';
      box.style.display='none';
      // reset inputs and UI state
      hostSearch.value='';
      results.innerHTML='';
      results.style.display='none';
      hostPhone.value='';
      // give a gentle cue and focus name field
      Swal.fire({toast:true,icon:'info',title:'میزبان پاک شد. دوباره انتخاب کنید',position:'top',timer:1400,showConfirmButton:false});
      setTimeout(()=> hostSearch.focus(), 50);
    }

    const hostSearch=document.getElementById('hostSearch');
    const results=document.getElementById('hostSearchResults');
    let t=null;
    hostSearch.addEventListener('input',()=>{
      // Enforce name-only: strip digits and phone-like characters
      const cleaned = hostSearch.value.replace(/[0-9۰-۹٠-٩+\-_,.]/g,'');
      if(cleaned !== hostSearch.value){ hostSearch.value = cleaned; }
      const q=hostSearch.value.trim();
      clearTimeout(t);
      if(q.length<2){ results.style.display='none'; results.innerHTML=''; return; }
      t=setTimeout(async ()=>{
        try{
          const res=await fetch("{{ route('admin.hosts.search') }}?q="+encodeURIComponent(q),{headers:{'Accept':'application/json'}});
          const rows=await res.json();
          results.innerHTML='';
          rows.forEach(h=>{
            const a=document.createElement('a');
            a.href='#'; a.className='list-group-item list-group-item-action';
            a.innerHTML=`<div class="d-flex justify-content-between"><div>${h.name||'—'}</div><div class="small" dir="ltr">${h.phone||''}</div></div><div class="small text-muted">${h.national_id||''} · ${h.status||''}</div>`;
            a.addEventListener('click',(e)=>{ e.preventDefault(); bindHost(h); results.style.display='none'; });
            results.appendChild(a);
          });
          results.style.display = rows.length ? 'block':'none';
        }catch{ results.style.display='none'; }
      },300);
    });

    const hostPhone=document.getElementById('hostPhone');
    function toEnDigits(s){const fa='۰۱۲۳۴۵۶۷۸۹', ar='٠١٢٣٤٥٦٧٨٩'; let out=''; for(const ch of String(s)){const iFa=fa.indexOf(ch); if(iFa>-1){out+=String(iFa); continue;} const iAr=ar.indexOf(ch); if(iAr>-1){out+=String(iAr); continue;} out+=ch;} return out; }
    ['input','blur','change'].forEach(ev=> hostPhone.addEventListener(ev,()=>{ hostPhone.value=toEnDigits(hostPhone.value).replace(/[^0-9]/g,''); }));
    // On blur: only try to bind existing host by phone (no modal)
    hostPhone.addEventListener('blur', async ()=>{
      const p=hostPhone.value.trim(); if(!p) return;
      try{
        const res=await fetch("{{ route('admin.hosts.lookup') }}?phone="+encodeURIComponent(p),{headers:{'Accept':'application/json'}});
        if(res.ok){ const h=await res.json(); bindHost(h); }
      }catch{}
    });

    // Determine endpoint (fallback if quick_store route is absent)
    const quickHostUrl = "{{ Route::has('admin.hosts.quick_store') ? route('admin.hosts.quick_store') : (Route::has('admin.hosts.store') ? route('admin.hosts.store') : '') }}";
    const quickBtn = document.getElementById('btnQuickCreateHost');
    if(!quickHostUrl){
      quickBtn.disabled = true;
      quickBtn.title = 'مسیر ایجاد میزبان در سرور یافت نشد';
    }

    // Quick create instantly using phone (+ optional name from search field)
    quickBtn.addEventListener('click', async ()=>{
      const phone = hostPhone.value.trim();
      if(!phone){ Swal.fire({icon:'warning',title:'ابتدا شماره موبایل را وارد کنید'}); return; }
      try{
        // If exists, bind
        const chk=await fetch("{{ route('admin.hosts.lookup') }}?phone="+encodeURIComponent(phone),{headers:{'Accept':'application/json'}});
        if(chk.ok){ const h=await chk.json(); bindHost(h); return; }
      }catch{}
      const name = (hostSearch.value||'').trim() || phone;
      const form = new FormData();
      form.append('_token','{{ csrf_token() }}');
      form.append('name', name);
      form.append('phone', phone);
      try{
        const resp = await fetch(quickHostUrl,{method:'POST', body: form, headers:{'Accept':'application/json'}});
        if(resp.ok){ const h=await resp.json(); bindHost(h); Swal.fire({toast:true,icon:'success',title:'میزبان ایجاد شد',position:'top',timer:1200,showConfirmButton:false}); }
        else{
          const d = await resp.json().catch(()=>({message:'خطا'}));
          Swal.fire({icon:'error',title:'خطا در ایجاد', text: d.message||'لطفاً ورودی‌ها را بررسی کنید'});
        }
      }catch{ Swal.fire({icon:'error',title:'خطا در ارتباط با سرور'}); }
    });
    document.getElementById('btnChangeHost').addEventListener('click',()=> clearHost());
  })();

  // Images upload with FilePond
  const imagesInput=document.getElementById('imagesInput');
  const mainIndexField=document.getElementById('main_image_index');
  FilePond.registerPlugin(FilePondPluginImagePreview);
  const pond = FilePond.create(imagesInput, {
    credits: false,
    allowMultiple: true,
    maxFiles: 10,
    acceptedFileTypes: ['image/*'],
    storeAsFile: true,
    instantUpload: false,
    labelIdle: 'برای افزودن تصویر کلیک کنید یا فایل را اینجا رها کنید',
    labelInvalidField: 'فایل انتخاب‌شده معتبر نیست',
    labelFileProcessing: 'در حال بارگذاری',
    labelFileProcessingComplete: 'بارگذاری شد',
    labelTapToCancel: 'لغو',
    labelTapToUndo: 'بازگردانی',
  });
  function updateMainBadge(){
    const files = pond.getFiles();
    files.forEach((f,idx)=>{
      const item = pond.element.querySelector(`[data-filepond-item-id="${f.id}"]`);
      if(item){
        item.classList.toggle('is-main', String(idx) === String(mainIndexField.value||'0'));
      }
    });
  }
  pond.on('addfile', ()=>{
    if(mainIndexField.value===''){ mainIndexField.value='0'; }
    updateMainBadge();
  });
  pond.on('removefile', ()=>{
    const files = pond.getFiles();
    let idx = parseInt(mainIndexField.value||'0',10);
    if(isNaN(idx) || idx>=files.length) idx = files.length ? 0 : '';
    mainIndexField.value = idx === '' ? '' : String(idx);
    updateMainBadge();
  });
  pond.on('reorderfiles', updateMainBadge);
  pond.on('activatefile', (file)=>{
    const files=pond.getFiles();
    const id = file?.id || file?.file?.id;
    const idx = files.findIndex(f=>f.id===id);
    if(idx>=0){ mainIndexField.value=String(idx); updateMainBadge(); }
  });

  // Rules dynamic
  const rulesTableBody=document.querySelector('#rulesTable tbody');
  const addRuleBtn=document.getElementById('addRuleBtn');
  const rulesJsonField=document.getElementById('rules_json');
  let ruleCounter=0;
  addRuleBtn.addEventListener('click',()=>{
    if(ruleCounter>=20){ Swal.fire({icon:'warning',title:'حداکثر 20 قانون'}); return; }
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td><input type="text" class="form-control form-control-sm rule-text" placeholder="متن قانون" required></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-success toggle-allowed" data-allowed="1"><i class="bi bi-check-circle"></i></button>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-rule"><i class="bi bi-trash3"></i></button>
      </td>`;
    rulesTableBody.appendChild(tr);
    ruleCounter++;
  });
  rulesTableBody.addEventListener('click',e=>{
    const btn=e.target.closest('.toggle-allowed');
    if(btn){
      const allowed=btn.dataset.allowed==='1';
      btn.dataset.allowed=allowed?'0':'1';
      btn.classList.toggle('btn-success', !allowed);
      btn.classList.toggle('btn-danger', allowed);
      btn.innerHTML=allowed?'<i class="bi bi-x-circle"></i>':'<i class="bi bi-check-circle"></i>';
    }
    const remove=e.target.closest('.remove-rule');
    if(remove){
      const tr=remove.closest('tr');
      tr.classList.add('rule-row-removed');
      setTimeout(()=>{ tr.remove(); ruleCounter--; },250);
    }
  });
  function syncRulesJson(){
    const rules=[];
    rulesTableBody.querySelectorAll('tr').forEach(tr=>{
      const text=tr.querySelector('.rule-text')?.value?.trim();
      if(!text) return;
      rules.push({
        rule_text:text,
        is_allowed: tr.querySelector('.toggle-allowed').dataset.allowed==='1'
      });
    });
    rulesJsonField.value=JSON.stringify(rules);
  }

  // Submit handler: convert price strings (remove commas)
  document.getElementById('stayForm').addEventListener('submit',function(e){
    if(!validateStep(current)){ e.preventDefault(); return; }
    syncRulesJson();
    this.querySelectorAll('[data-price-format]').forEach(inp=>{
      inp.value=inp.value.replace(/,/g,'');
    });
  });

  // Pricing mode toggle
  (function(){
    const modeSel=document.getElementById('pricing_mode');
    const perPerson=document.querySelectorAll('.pricing-per-person');
    const perNight=document.querySelectorAll('.pricing-per-night');
    const pricePerPerson=document.getElementById('price_per_person');
    const pricePerNight=document.getElementById('price_per_night');
    const extraGroup=document.querySelectorAll('.extra-person-group');
    const extraInput=document.querySelector('input[name="extra_person_price"]');
    function syncUI(){
      const byNight = modeSel.value==='per_night';
      perPerson.forEach(el=> el.classList.toggle('d-none', byNight));
      perNight.forEach(el=> el.classList.toggle('d-none', !byNight));
      // required swap
      if(pricePerPerson) pricePerPerson.toggleAttribute('required', !byNight);
      if(pricePerNight) pricePerNight.toggleAttribute('required', byNight);
      // extra person visibility (hidden for per-night)
      extraGroup.forEach(el=> el.classList.toggle('d-none', byNight));
      if(extraInput){
        extraInput.disabled = byNight;
        if(byNight) extraInput.value='';
      }
    }
    modeSel?.addEventListener('change', syncUI);
    syncUI();
  })();

  // Capacity constraints: extra_capacity <= capacity - base_capacity
  (function(){
    const capacityInp = document.querySelector('input[name="capacity"]');
    const baseInp = document.querySelector('input[name="base_capacity"]');
    const extraInp = document.querySelector('input[name="extra_capacity"]');
    if(!capacityInp || !baseInp || !extraInp) return;
    function toInt(v){ const n=parseInt(String(v||'').replace(/[^\d]/g,''),10); return isNaN(n)?0:n; }
    function updateMax(){
      const cap = toInt(capacityInp.value);
      const base = toInt(baseInp.value);
      const maxExtra = Math.max(0, cap - base);
      extraInp.setAttribute('max', String(maxExtra));
      const cur = toInt(extraInp.value);
      if(cur > maxExtra){ extraInp.value = String(maxExtra); }
    }
    ['input','change','blur'].forEach(ev=>{
      capacityInp.addEventListener(ev, updateMax);
      baseInp.addEventListener(ev, updateMax);
      extraInp.addEventListener(ev, updateMax);
    });
    updateMax();
  })();
  show(1);
})();
</script>
@endpush