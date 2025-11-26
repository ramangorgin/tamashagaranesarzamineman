@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.host')
@section('title','ویرایش اقامت‌گاه')

@section('breadcrumb')
    @if(($role ?? null)==='admin')
        <li class="breadcrumb-item"><a href="{{ route('admin.stays.index') }}">اقامت‌گاه‌ها</a></li>
        <li class="breadcrumb-item active">ویرایش</li>
    @endif
@endsection

@section('breadcrumb-actions')
    @if(($role ?? null)==='admin')
        <a href="{{ route('stays.show',$stay) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye"></i> مشاهده
        </a>
    @endif
@endsection

@section('content')
<div class="container py-4">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:1000px;">
    <div class="card-body p-4">
      <h4 class="fw-bold text-primary text-center mb-4">
        <i class="bi bi-pencil-square me-2"></i> ویرایش اقامت‌گاه
      </h4>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger small mb-3">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form id="stayForm" method="POST" action="{{ route('host.stays.update',$stay) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @php $role = $role ?? (auth('admin')->check() ? 'admin' : 'host'); @endphp

        <div class="d-flex flex-wrap justify-content-center mb-4 gap-2 small fw-semibold">
          <div class="step-dot active" data-step="1">مشخصات</div>
          <div class="step-dot" data-step="2">موقعیت</div>
          <div class="step-dot" data-step="3">ظرفیت</div>
          <div class="step-dot" data-step="4">قیمت</div>
          <div class="step-dot" data-step="5">تصاویر</div>
          <div class="step-dot" data-step="6">قوانین</div>
          <div class="step-dot" data-step="7">تأیید</div>
        </div>

        {{-- Step 1 --}}
        <div id="step1">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">عنوان اقامت‌گاه</label>
              <input type="text" name="title" class="form-control" value="{{ old('title',$stay->title) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">دسته‌بندی</label>
              @php $cats=['hotel','villa','apartment','ecolodge','suite','motel','house']; @endphp
              <select name="category" class="form-select" required>
                @foreach($cats as $c)
                  <option value="{{ $c }}" @selected(old('category',$stay->category)===$c)>{{ stayTypeToPersian($c) }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 2 --}}
        <div id="step2" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">استان</label>
              <select name="province_id" id="province" class="form-select" required></select>
              <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name',$stay->province_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">شهر</label>
              <select name="city_id" id="city" class="form-select" required disabled></select>
              <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name',$stay->city_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">بخش / شهرستان</label>
              <select name="county_id" id="county" class="form-select" required disabled></select>
              <input type="hidden" name="county_name" id="county_name" value="{{ old('county_name',$stay->county_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">روستا (اختیاری)</label>
              <select name="village_name" id="village" class="form-select" disabled>
                <option value="">(انتخاب کنید)</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">آدرس دقیق</label>
              <textarea name="address" class="form-control" rows="2" required>{{ old('address',$stay->address) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label d-flex justify-content-between">موقعیت روی نقشه
                <small class="text-muted">برای تغییر کلیک کنید</small>
              </label>
              <div id="map" style="height:380px;border:1px solid #dee2e6;border-radius:14px;"></div>
              <input type="hidden" name="latitude" id="lat" value="{{ old('latitude',$stay->latitude) }}">
              <input type="hidden" name="longitude" id="lng" value="{{ old('longitude',$stay->longitude) }}">
              <div class="small text-muted mt-2">مختصات فعلی: <span id="coordText">{{ $stay->latitude ? $stay->latitude.', '.$stay->longitude : 'انتخاب نشده' }}</span></div>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 3 --}}
        <div id="step3" class="d-none">
          <div class="row g-3">
            <div class="col-md-3"><label class="form-label">ظرفیت پایه</label>
              <input type="number" min="1" name="base_capacity" class="form-control" value="{{ old('base_capacity',$stay->base_capacity) }}" required>
            </div>
            <div class="col-md-3"><label class="form-label">ظرفیت کل</label>
              <input type="number" min="1" name="capacity" class="form-control" value="{{ old('capacity',$stay->capacity) }}" required>
            </div>
            <div class="col-md-3"><label class="form-label">نفرات اضافه</label>
              <input type="number" min="0" name="extra_capacity" class="form-control" value="{{ old('extra_capacity',$stay->extra_capacity) }}">
            </div>
            <div class="col-md-3"><label class="form-label">متراژ (متر)</label>
              <input type="number" min="0" name="area" class="form-control" value="{{ old('area',$stay->area) }}">
            </div>
            <div class="col-12">
              <div class="row g-3 mt-1">
                <div class="col-6 col-md-3"><label class="form-label">اتاق خواب</label><input type="number" min="0" name="bedrooms" class="form-control" value="{{ old('bedrooms',$stay->bedrooms) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">تخت دبل</label><input type="number" min="0" name="double_beds" class="form-control" value="{{ old('double_beds',$stay->double_beds) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">تخت سینگل</label><input type="number" min="0" name="single_beds" class="form-control" value="{{ old('single_beds',$stay->single_beds) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">کف‌خواب</label><input type="number" min="0" name="floor_beds" class="form-control" value="{{ old('floor_beds',$stay->floor_beds) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">حمام</label><input type="number" min="0" name="bathrooms" class="form-control" value="{{ old('bathrooms',$stay->bathrooms) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">سرویس ایرانی</label><input type="number" min="0" name="iranian_toilets" class="form-control" value="{{ old('iranian_toilets',$stay->iranian_toilets) }}"></div>
                <div class="col-6 col-md-3"><label class="form-label">سرویس فرنگی</label><input type="number" min="0" name="western_toilets" class="form-control" value="{{ old('western_toilets',$stay->western_toilets) }}"></div>
              </div>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 4 (Pricing) --}}
        <div id="step4" class="d-none">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">قیمت هر نفر (ریال)</label>
              <input type="text" name="price_per_person" class="form-control price-field" data-price-format value="{{ old('price_per_person',number_format($stay->price_per_person)) }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">قیمت نفر اضافه (ریال)</label>
              <input type="text" name="extra_person_price" class="form-control price-field" data-price-format value="{{ old('extra_person_price',number_format($stay->extra_person_price)) }}">
            </div>

            @if($role !== 'admin')
              <div class="col-md-4">
                <label class="form-label">کمیسیون سایت</label>
                <div class="input-group">
                  <input type="number" name="site_commission" min="0" max="100" step="0.5" class="form-control" value="{{ old('site_commission',$stay->site_commission) }}" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">حداکثر تخفیف (عادی)</label>
                <div class="input-group">
                  <input type="number" name="max_discount_normal" min="0" max="100" step="0.5" class="form-control" value="{{ old('max_discount_normal',$stay->max_discount_normal) }}" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">حداکثر تخفیف (پیک)</label>
                <div class="input-group">
                  <input type="number" name="max_discount_peak" min="0" max="100" step="0.5" class="form-control" value="{{ old('max_discount_peak',$stay->max_discount_peak) }}" required>
                  <span class="input-group-text">%</span>
                </div>
              </div>
            @else
              <input type="hidden" name="site_commission" value="0">
              <input type="hidden" name="max_discount_normal" value="0">
              <input type="hidden" name="max_discount_peak" value="0">
            @endif

          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 5 --}}
        <div id="step5" class="d-none">
          <div class="mb-3">
            <label class="form-label">افزودن تصاویر جدید (حداکثر 10)</label>
            <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple class="form-control">
          </div>
          <h6 class="fw-bold small mb-2">تصاویر فعلی</h6>
          <div class="row g-3 mb-3">
            @forelse($stay->images as $img)
              <div class="col-6 col-md-3">
                <div class="position-relative">
                  <img src="{{ asset($img->path) }}" class="img-fluid rounded-3" style="height:140px;object-fit:cover;">
                  @if($img->is_main)
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-1">اصلی</span>
                  @endif
                  <button type="button" class="btn btn-sm btn-outline-danger position-absolute bottom-0 end-0 m-1 existing-delete" data-id="{{ $img->id }}"><i class="bi bi-trash"></i></button>
                  <button type="button" class="btn btn-sm btn-outline-info position-absolute bottom-0 start-0 m-1 make-main-existing" data-id="{{ $img->id }}">
                    <i class="bi {{ $img->is_main ? 'bi-star-fill' : 'bi-star' }}"></i>
                  </button>
                </div>
              </div>
            @empty
              <p class="small text-muted">تصویری ندارد.</p>
            @endforelse
          </div>
          <h6 class="fw-bold small mb-2">پیش‌نمایش تصاویر جدید</h6>
          <div id="imagesPreview" class="row g-3"></div>
          <input type="hidden" name="main_image_index" id="main_image_index">
          <input type="hidden" name="main_image_existing_id" id="main_image_existing_id" value="{{ optional($stay->images->firstWhere('is_main',true))->id }}">
          <input type="hidden" name="remove_image_ids" id="remove_image_ids">
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 6 --}}
        <div id="step6" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">ساعت ورود</label>
              <input type="time" name="checkin_time" id="checkin_time" class="form-control" value="{{ old('checkin_time',$stay->checkin_time ?? '14:00') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">ساعت خروج</label>
              <input type="time" name="checkout_time" id="checkout_time" class="form-control" value="{{ old('checkout_time',$stay->checkout_time ?? '12:00') }}">
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
            <label class="form-label mb-0">سایر قوانین</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRuleBtn"><i class="bi bi-plus-lg"></i> افزودن قانون</button>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" id="rulesTable">
              <thead class="table-light">
                <tr><th style="width:70%">متن قانون</th><th style="width:16%">وضعیت</th><th style="width:14%">حذف</th></tr>
              </thead>
              <tbody>
              @foreach($stay->rules ?? [] as $r)
                <tr>
                  <td><input type="text" class="form-control form-control-sm rule-text" value="{{ $r->rule_text }}"></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm {{ $r->is_allowed?'btn-success':'btn-danger' }} toggle-allowed" data-allowed="{{ $r->is_allowed?1:0 }}">
                      <i class="bi {{ $r->is_allowed?'bi-check-circle':'bi-x-circle' }}"></i>
                    </button>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-rule"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <textarea name="rules_json" id="rules_json" class="d-none"></textarea>
          <div class="mt-3 small text-muted">نمونه: «برگزاری پارتی: ممنوع»</div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 7 --}}
        <div id="step7" class="d-none">
          <div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2 fs-5"></i>بررسی نهایی و ذخیره.</div>
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="submit" class="btn btn-success">ذخیره تغییرات <i class="bi bi-check2-circle"></i></button>
          </div>
        </div>

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
.price-field{text-align:left;direction:ltr}
.image-box{position:relative}
.image-box img{width:100%;height:140px;object-fit:cover;border-radius:10px}
.main-badge{position:absolute;top:6px;left:6px;background:#ffc107;color:#212529;padding:4px 8px;border-radius:20px;font-size:.7rem;cursor:pointer}
.rule-row-removed{opacity:.4;text-decoration:line-through}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const totalSteps=7; let current=1;
  let map, marker=null;
  function initMap(){
    if(map) return;
    const latField=document.getElementById('lat');
    const lngField=document.getElementById('lng');
    const startLat = latField.value ? parseFloat(latField.value) : 32;
    const startLng = lngField.value ? parseFloat(lngField.value) : 53;
    const zoom = latField.value ? 12 : 5;
    map = L.map('map').setView([startLat,startLng],zoom);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
    if(latField.value && lngField.value){ marker=L.marker([startLat,startLng]).addTo(map); }
    map.on('click',e=>{
      const {lat,lng}=e.latlng;
      if(marker) marker.setLatLng(e.latlng); else marker=L.marker(e.latlng).addTo(map);
      latField.value=lat.toFixed(6); lngField.value=lng.toFixed(6);
      document.getElementById('coordText').textContent=latField.value+', '+lngField.value;
      Swal.fire({toast:true,icon:'success',title:'مختصات ثبت شد',position:'top',showConfirmButton:false,timer:1300});
    });
  }

  function show(step){
    for(let i=1;i<=totalSteps;i++){
      const el=document.getElementById('step'+i);
      if(!el) continue;
      if(i===step){ el.classList.remove('d-none'); el.style.display='block'; }
      else el.style.display='none';
      document.querySelector('.step-dot[data-step="'+i+'"]')?.classList.toggle('active',i===step);
    }
    current=step;
    if(step===2){ initMap(); setTimeout(()=> map.invalidateSize(),150); }
    window.scrollTo({top:0,behavior:'smooth'});
  }

  document.querySelectorAll('.next-btn').forEach(b=>b.addEventListener('click',()=>{
    if(!validateStep(current)) return;
    if(current<totalSteps) show(current+1);
    if(current===6) syncRulesJson();
  }));
  document.querySelectorAll('.prev-btn').forEach(b=>b.addEventListener('click',()=>{ if(current>1) show(current-1); }));
  document.querySelectorAll('.step-dot').forEach(d=>d.addEventListener('click',()=>{const s=parseInt(d.dataset.step); if(s<current) show(s);}));

  function validateStep(step){
    if(step===2 && !document.getElementById('lat').value){
      Swal.fire({icon:'warning',title:'مختصات انتخاب نشده'}); return false;
    }
    if(step===5){
      const files=document.getElementById('imagesInput').files;
      if(files.length>10){ Swal.fire({icon:'error',title:'حداکثر 10 تصویر'}); return false; }
    }
    const c=document.getElementById('step'+step);
    let invalid=false;
    c.querySelectorAll('[required]').forEach(inp=>{
      if(!inp.value){ inp.classList.add('is-invalid'); invalid=true; } else inp.classList.remove('is-invalid');
    });
    if(invalid){ Swal.fire({icon:'error',title:'فیلدهای ضروری خالی است'}); return false; }
    return true;
  }

  // Price formatting
  function fmt(v){ v=v.replace(/[^\d]/g,''); return v? v.replace(/\B(?=(\d{3})+(?!\d))/g,','):'0'; }
  document.querySelectorAll('[data-price-format]').forEach(inp=>{
    inp.addEventListener('input',()=>{ const pos=inp.selectionStart; inp.value=fmt(inp.value); inp.setSelectionRange(pos,pos); });
    inp.addEventListener('blur',()=> inp.value=fmt(inp.value));
  });

  // Geo cascading (static JSON like create.blade.php)
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
  const oldProvince="{{ old('province_id',$stay->province_id) }}",
        oldCity="{{ old('city_id',$stay->city_id) }}",
        oldCounty="{{ old('county_id',$stay->county_id) }}",
        oldVillage="{{ old('village_name',$stay->village_name) }}";

  async function loadProvinces(){
    try{
      if(!DATA_PROVINCES){ DATA_PROVINCES = await fetch(URLS.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
      provinceSel.innerHTML=opt('','انتخاب استان');
      DATA_PROVINCES.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.provinceId,p.provinceName)) );
      provinceSel.removeAttribute('disabled');
      if(oldProvince){ provinceSel.value=oldProvince; hProvince.value="{{ old('province_name',$stay->province_name) }}"; provinceSel.dispatchEvent(new Event('change')); }
    }catch(e){
      try{
        const rows = await fetch(FALLBACK.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json());
        provinceSel.innerHTML=opt('','انتخاب استان'); rows.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.id||p.provinceId,p.name||p.provinceName)) );
        provinceSel.removeAttribute('disabled');
        if(oldProvince){ provinceSel.value=oldProvince; hProvince.value="{{ old('province_name',$stay->province_name) }}"; provinceSel.dispatchEvent(new Event('change')); }
      }catch{ provinceSel.innerHTML=opt('', 'خطا در بارگذاری استان‌ها'); }
    }
  }

  provinceSel.addEventListener('change',()=>{
    const pid=provinceSel.value; hProvince.value = provinceSel.options[provinceSel.selectedIndex]?.text || '';
    reset(citySel,'در حال بارگذاری...',false); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!pid){ reset(citySel,'انتخاب شهر'); return; }
    (async ()=>{
      try{
        if(!DATA_CITIES){ DATA_CITIES = await fetch(URLS.cities,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const cities = DATA_CITIES.filter(row=>row.provinceId===pid);
        citySel.innerHTML=opt('','انتخاب شهر');
        cities.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)) );
        citySel.removeAttribute('disabled');
        if(oldCity){ citySel.value=oldCity; hCity.value="{{ old('city_name',$stay->city_name) }}"; citySel.dispatchEvent(new Event('change')); }
      }catch{
        try{
          const rows = await fetch(FALLBACK.cities(pid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          citySel.innerHTML=opt('','انتخاب شهر'); rows.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.id||c.cityId,c.name||c.cityName)) );
          citySel.removeAttribute('disabled');
          if(oldCity){ citySel.value=oldCity; hCity.value="{{ old('city_name',$stay->city_name) }}"; citySel.dispatchEvent(new Event('change')); }
        }catch{ citySel.innerHTML=opt('', 'خطا در بارگذاری شهرها'); }
      }
    })();
  });

  citySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value; hCity.value = citySel.options[citySel.selectedIndex]?.text || '';
    reset(countySel,'در حال بارگذاری...',false); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!cid){ reset(countySel,'انتخاب بخش'); return; }
    (async ()=>{
      try{
        if(!DATA_COUNTIES){ DATA_COUNTIES = await fetch(URLS.counties,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const counties = DATA_COUNTIES.filter(row=>row.provinceId===pid && row.cityId===cid);
        countySel.innerHTML=opt('','انتخاب بخش/شهرستان');
        counties.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.countyId,c.countyName)) );
        countySel.removeAttribute('disabled');
        if(oldCounty){ countySel.value=oldCounty; hCounty.value="{{ old('county_name',$stay->county_name) }}"; countySel.dispatchEvent(new Event('change')); }
      }catch{
        try{
          const rows = await fetch(FALLBACK.counties(pid,cid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          countySel.innerHTML=opt('','انتخاب بخش/شهرستان'); rows.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.id||c.countyId,c.name||c.countyName)) );
          countySel.removeAttribute('disabled');
          if(oldCounty){ countySel.value=oldCounty; hCounty.value="{{ old('county_name',$stay->county_name) }}"; countySel.dispatchEvent(new Event('change')); }
        }catch{ countySel.innerHTML=opt('', 'خطا در بارگذاری بخش'); }
      }
    })();
  });

  countySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value, coid=countySel.value; hCounty.value = countySel.options[countySel.selectedIndex]?.text || '';
    reset(villageSel,'در حال بارگذاری...',false);
    if(!coid){ reset(villageSel,'(اختیاری) انتخاب روستا'); return; }
    (async ()=>{
      try{
        if(!DATA_VILLAGES){ DATA_VILLAGES = await fetch(URLS.villages,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const villages = DATA_VILLAGES.filter(row=>row.provinceId===pid && row.cityId===cid && row.countyId===coid);
        villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
        if(!villages.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
        else villages.forEach(v=> { if(v.villageName && v.villageName.trim()) villageSel.insertAdjacentHTML('beforeend', opt(v.villageName,v.villageName)); });
        villageSel.removeAttribute('disabled');
        if(oldVillage){ villageSel.value=oldVillage; }
      }catch{
        try{
          const rows = await fetch(FALLBACK.villages(pid,cid,coid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
          if(!rows.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
          else rows.forEach(v=> { const name=v.name||v.villageName; if(name && name.trim()) villageSel.insertAdjacentHTML('beforeend', opt(name,name)); });
          villageSel.removeAttribute('disabled');
          if(oldVillage){ villageSel.value=oldVillage; }
        }catch{ villageSel.innerHTML=opt('', 'خطا در بارگذاری روستاها'); }
      }
    })();
  });

  reset(provinceSel,'در حال بارگذاری...',false); reset(citySel,'ابتدا استان را انتخاب کنید'); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
  loadProvinces();

  // Images
  const imagesInput=document.getElementById('imagesInput');
  const preview=document.getElementById('imagesPreview');
  const mainIndex=document.getElementById('main_image_index');
  const mainExisting=document.getElementById('main_image_existing_id');
  const removeIds=document.getElementById('remove_image_ids');
  let removed=[];
  imagesInput.addEventListener('change',()=>{
    preview.innerHTML=''; const files=[...imagesInput.files].slice(0,10);
    files.forEach((f,i)=>{
      const reader=new FileReader();
      reader.onload=e=>{
        const col=document.createElement('div');
        col.className='col-6 col-md-3';
        col.innerHTML=`<div class="image-box">
          <img src="${e.target.result}" alt="">
          <div class="position-absolute top-0 start-0 m-1 p-1 px-2 rounded-3 bg-light small make-main-new" data-index="${i}" style="cursor:pointer"><i class="bi bi-star${i===0?' -fill':''}"></i></div>
        </div>`;
        preview.appendChild(col);
      };
      reader.readAsDataURL(f);
    });
    mainIndex.value='0';
  });
  preview.addEventListener('click',e=>{
    const btn=e.target.closest('.make-main-new');
    if(btn){
      const idx=btn.dataset.index;
      mainIndex.value=idx; mainExisting.value='';
      preview.querySelectorAll('.make-main-new i').forEach(i=>i.className='bi bi-star');
      btn.querySelector('i').className='bi bi-star-fill';
    }
  });
  document.querySelectorAll('.existing-delete').forEach(b=>{
    b.addEventListener('click',()=>{
      const id=b.dataset.id;
      if(!removed.includes(id)) removed.push(id);
      b.closest('.col-6,.col-md-3').style.opacity=.4;
      removeIds.value=removed.join(',');
    });
  });
  document.querySelectorAll('.make-main-existing').forEach(b=>{
    b.addEventListener('click',()=>{
      mainExisting.value=b.dataset.id; mainIndex.value='';
      document.querySelectorAll('.make-main-existing i').forEach(i=>i.className='bi bi-star');
      b.querySelector('i').className='bi bi-star-fill';
    });
  });

  // Rules
  const rulesTbody=document.querySelector('#rulesTable tbody');
  const addRuleBtn=document.getElementById('addRuleBtn');
  const rulesJson=document.getElementById('rules_json');
  let ruleCounter=rulesTbody.querySelectorAll('tr').length;
  addRuleBtn.addEventListener('click',()=>{
    if(ruleCounter>=20){ Swal.fire({icon:'warning',title:'حداکثر 20 قانون'}); return; }
    const tr=document.createElement('tr');
    tr.innerHTML=`<td><input type="text" class="form-control form-control-sm rule-text" placeholder="مثال: برگزاری پارتی"></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-success toggle-allowed" data-allowed="1"><i class="bi bi-check-circle"></i></button></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-rule"><i class="bi bi-trash3"></i></button></td>`;
    rulesTbody.appendChild(tr); ruleCounter++;
  });
  rulesTbody.addEventListener('click',e=>{
    const t=e.target.closest('.toggle-allowed');
    if(t){
      const allowed=t.dataset.allowed==='1';
      t.dataset.allowed=allowed?'0':'1';
      t.classList.toggle('btn-success',!allowed);
      t.classList.toggle('btn-danger',allowed);
      t.innerHTML=allowed?'<i class="bi bi-x-circle"></i>':'<i class="bi bi-check-circle"></i>';
    }
    const rm=e.target.closest('.remove-rule');
    if(rm){
      const tr=rm.closest('tr');
      tr.classList.add('rule-row-removed');
      setTimeout(()=>{ tr.remove(); ruleCounter--; },200);
    }
  });
  function syncRulesJson(){
    const arr=[];
    rulesTbody.querySelectorAll('tr').forEach(tr=>{
      const txt=tr.querySelector('.rule-text')?.value?.trim();
      if(!txt) return;
      arr.push({rule_text:txt,is_allowed: tr.querySelector('.toggle-allowed').dataset.allowed==='1'});
    });
    rulesJson.value=JSON.stringify(arr);
  }

  document.getElementById('stayForm').addEventListener('submit',function(e){
    if(!validateStep(current)){ e.preventDefault(); return; }
    syncRulesJson();
    this.querySelectorAll('[data-price-format]').forEach(inp=> inp.value=inp.value.replace(/,/g,''));
  });

  show(1);
})();
</script>
@endpush