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
      @if($errors->any())
        <div class="alert alert-danger small">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif
      <form id="stayForm" method="POST" action="{{ isset($role)&&$role==='admin' ? route('admin.stays.store') : route('host.stays.store') }}" enctype="multipart/form-data">
        @csrf
        @php $role = $role ?? (auth('admin')->check() ? 'admin' : 'host'); @endphp
        {{-- Step indicators --}}
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
              <label class="form-label">قیمت هر نفر (ظرفیت پایه) <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="price_per_person" class="form-control price-field" data-price-format value="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">قیمت نفر اضافه <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="extra_person_price" class="form-control price-field" data-price-format value="0">
            </div>
            @if($role !== 'admin')
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
        {{-- Step 5: Images --}}
        <div id="step5" class="d-none">
          <div class="mb-3">
            <label class="form-label">تصاویر اقامت‌گاه (حداکثر 10)</label>
            <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple class="form-control">
            <small class="text-muted d-block mt-1">روی ستاره کلیک کنید تا تصویر اصلی شود.</small>
          </div>
          <div id="imagesPreview" class="row g-3"></div>
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
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>
        {{-- Step 7: Review --}}
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const totalSteps=7;
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
    if(step===2 && !document.getElementById('lat').value){
      Swal.fire({icon:'warning',title:'مختصات انتخاب نشده'}); return false;
    }
    if(step===5){ // images limit
      const files=document.getElementById('imagesInput').files;
      if(files.length>10){ Swal.fire({icon:'error',title:'حداکثر 10 تصویر'}); return false; }
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

  // Geo cascading
  const provinceSel=document.getElementById('province'),
        citySel=document.getElementById('city'),
        countySel=document.getElementById('county'),
        villageSel=document.getElementById('village'),
        hProvince=document.getElementById('province_name'),
        hCity=document.getElementById('city_name'),
        hCounty=document.getElementById('county_name');
  function opt(v,t){return `<option value="${v}">${t}</option>`;}
  function reset(sel,ph,disable=true){ sel.innerHTML=opt('',ph); if(disable) sel.setAttribute('disabled','disabled'); else sel.removeAttribute('disabled'); }
  async function load(url){ const r=await fetch(url,{headers:{'Accept':'application/json'}}); if(!r.ok) throw new Error(); return r.json(); }
  const routes={
    provinces:"{{ route('geo.provinces') }}",
    cities:"{{ route('geo.cities',['province'=>'__']) }}",
    counties:"{{ route('geo.counties',['province'=>'__P','city'=>'__C']) }}",
    villages:"{{ route('geo.villages',['province'=>'__P','city'=>'__C','county'=>'__K']) }}"
  };
  function url(t,p={}){ if(t==='cities')return routes.cities.replace('__',p.province); if(t==='counties')return routes.counties.replace('__P',p.province).replace('__C',p.city); if(t==='villages')return routes.villages.replace('__P',p.province).replace('__C',p.city).replace('__K',p.county); return routes.provinces; }
  reset(provinceSel,'در حال بارگذاری...',false); reset(citySel,'ابتدا استان را انتخاب کنید'); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
  load(url('provinces')).then(list=>{
    provinceSel.innerHTML=opt('','انتخاب استان');
    list.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.provinceId,p.provinceName)));
  }).catch(()=> provinceSel.innerHTML=opt('', 'خطا در بارگذاری استان‌ها'));
  provinceSel.addEventListener('change',()=>{
    const pid=provinceSel.value; hProvince.value=provinceSel.options[provinceSel.selectedIndex]?.text||'';
    reset(citySel,'در حال بارگذاری...',false); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!pid){ reset(citySel,'انتخاب شهر'); return; }
    load(url('cities',{province:pid})).then(rows=>{
      citySel.innerHTML=opt('','انتخاب شهر'); citySel.removeAttribute('disabled');
      rows.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)));
    }).catch(()=> citySel.innerHTML=opt('', 'خطا در بارگذاری شهرها'));
  });
  citySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value; hCity.value=citySel.options[citySel.selectedIndex]?.text||'';
    reset(countySel,'در حال بارگذاری...',false); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!cid){ reset(countySel,'انتخاب بخش'); return; }
    load(url('counties',{province:pid,city:cid})).then(rows=>{
      countySel.innerHTML=opt('','انتخاب بخش/شهرستان'); countySel.removeAttribute('disabled');
      rows.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.countyId,c.countyName)));
    }).catch(()=> countySel.innerHTML=opt('', 'خطا در بارگذاری بخش'));
  });
  countySel.addEventListener('change',()=>{
    const pid=provinceSel.value,cid=citySel.value,coid=countySel.value; hCounty.value=countySel.options[countySel.selectedIndex]?.text||'';
    reset(villageSel,'در حال بارگذاری...',false);
    if(!coid){ reset(villageSel,'(اختیاری) انتخاب روستا'); return; }
    load(url('villages',{province:pid,city:cid,county:coid})).then(rows=>{
      villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا'); villageSel.removeAttribute('disabled');
      if(!rows.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
      else rows.forEach(v=> villageSel.insertAdjacentHTML('beforeend', opt(v.villageName,v.villageName)));
    }).catch(()=> villageSel.innerHTML=opt('', 'خطا در بارگذاری روستاها'));
  });

  // Images preview
  const imagesInput=document.getElementById('imagesInput');
  const previewContainer=document.getElementById('imagesPreview');
  const mainIndexField=document.getElementById('main_image_index');
  imagesInput.addEventListener('change',()=>{
    previewContainer.innerHTML='';
    const files=[...imagesInput.files].slice(0,10);
    files.forEach((file,i)=>{
      const reader=new FileReader();
      reader.onload=e=>{
        const col=document.createElement('div');
        col.className='col-6 col-md-3';
        col.innerHTML=`<div class="image-box">
          <img src="${e.target.result}" alt="">
          <div class="main-badge" data-index="${i}"><i class="bi bi-star${i===0?' -fill':''}"></i><span>${i===0?'تصویر اصلی':'انتخاب بعنوان اصلی'}</span></div>
          <button type="button" class="remove-img" data-index="${i}" title="حذف"><i class="bi bi-x-lg"></i></button>
        </div>`;
        previewContainer.appendChild(col);
      };
      reader.readAsDataURL(file);
    });
    mainIndexField.value='0';
  });
  previewContainer.addEventListener('click',e=>{
    const badge=e.target.closest('.main-badge');
    if(badge){
      const idx=badge.dataset.index;
      mainIndexField.value=idx;
      previewContainer.querySelectorAll('.main-badge').forEach(b=>{
        const star=b.querySelector('i');
        if(b.dataset.index===idx){ star.className='bi bi-star-fill'; b.querySelector('span').textContent='تصویر اصلی'; b.style.background='#ffc107'; }
        else { star.className='bi bi-star'; b.querySelector('span').textContent='انتخاب بعنوان اصلی'; b.style.background='rgba(255,255,255,.85)'; }
      });
    }
    const remove=e.target.closest('.remove-img');
    if(remove){
      const rIndex=parseInt(remove.dataset.index);
      // Build new FileList (not trivial) -> simplest: alert user to reselect
      Swal.fire({icon:'info',title:'برای حذف تصویر، لطفاً دوباره انتخاب نمایید',text:'مرورگر اجازه حذف تکی فایل انتخاب شده را نمی‌دهد. فایل‌ها را مجدداً انتخاب کنید.'});
    }
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
        is_allowed: tr.querySelector('.toggle-allowed').dataset.allowed==='1',
        checkin_time: tr.querySelector('.checkin-time').value || null,
        checkout_time: tr.querySelector('.checkout-time').value || null
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

  show(1);
})();
</script>
@endpush