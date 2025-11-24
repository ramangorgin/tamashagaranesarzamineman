@extends('layouts.host')
@section('title','تکمیل اطلاعات میزبان')

@section('content')
<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:640px;">
    <div class="card-body p-4">
      <h4 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-person-lines-fill me-2"></i> تکمیل اطلاعات میزبان
      </h4>

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      @if($host->status==='pending' && $host->name && $host->national_id)
        <div class="alert alert-info text-center">
          اطلاعات شما ارسال شد و در صف بررسی است. لطفاً منتظر تأیید مدیریت بمانید.
        </div>
      @elseif($host->status==='approved' && $host->name && $host->national_id)
        <div class="alert alert-success text-center">
          اطلاعات شما تأیید شد.
        </div>
        <a href="{{ route('host.dashboard') }}" class="btn btn-success w-100">
          ورود به داشبورد
        </a>
      @else
        @if($host->status==='rejected')
          <div class="alert alert-danger text-center mb-3">
            درخواست شما رد شده است. لطفاً موارد زیر را اصلاح و دوباره ارسال کنید.
          </div>
          @if($host->rejection_reason)
            <div class="border rounded-3 p-3 mb-4 bg-light small text-danger fw-semibold">
              دلیل رد: {{ $host->rejection_reason }}
            </div>
          @endif
        @endif
        {{-- multi-step form --}}
        <form id="profileForm" method="POST" action="{{ route('host.completeProfile.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Step 1: اطلاعات شخصی --}}
            <div id="step1">
              <div class="mb-3">
                <label class="form-label">نام کامل</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">کد ملی</label>
                <input type="text" name="national_id" class="form-control" required>
              </div>
                <div class="mb-3">
                <label class="form-label">ایمیل (اختیاری)</label>
                <input type="email" name="email" class="form-control">
              </div>
              <button type="button" class="btn btn-primary w-100 next-step">مرحله بعد</button>
            </div>

            {{-- Step 2: فایل‌های احراز هویت --}}
            <div id="step2" class="d-none">
              <div class="mb-3">
                <label class="form-label">تصویر کارت ملی</label>
                <input type="file" name="id_card_image" accept="image/*" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">سلفی همراه کارت ملی</label>
                <input type="file" name="selfie_image" accept="image/*" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">مجوز کسب / سند (اختیاری)</label>
                <input type="file" name="business_license" accept="image/*,.pdf" class="form-control">
              </div>
              <button type="button" class="btn btn-secondary w-100 prev-step mt-2">بازگشت</button>
              <button type="button" class="btn btn-primary w-100 next-step mt-2">مرحله بعد</button>
            </div>

            {{-- Step 3: آدرس --}}
            <div id="step3" class="d-none">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">استان</label>
                  <select name="province_id" id="province" class="form-select" required></select>
                  <input type="hidden" name="province_name" id="province_name">
                </div>
                <div class="col-12">
                  <label class="form-label">شهر</label>
                  <select name="city_id" id="city" class="form-select" required disabled></select>
                  <input type="hidden" name="city_name" id="city_name">
                </div>
                <div class="col-12">
                  <label class="form-label">بخش / شهرستان</label>
                  <select name="county_id" id="county" class="form-select" required disabled></select>
                  <input type="hidden" name="county_name" id="county_name">
                </div>
                <div class="col-12">
                  <label class="form-label">روستا (اختیاری)</label>
                  <select name="village_name" id="village" class="form-select" disabled>
                    <option value="">(انتخاب کنید)</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">آدرس دقیق</label>
                  <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>
              </div>
              <button type="button" class="btn btn-secondary w-100 prev-step mt-3">بازگشت</button>
              <button type="button" class="btn btn-primary w-100 next-step mt-2">مرحله بعد</button>
            </div>

            {{-- Step 4: اطلاعات بانکی --}}
            <div id="step4" class="d-none">
              <div class="mb-3">
                <label class="form-label">شماره شبا (IBAN) بدون IR</label>
                <input type="text" name="iban" class="form-control" maxlength="24" required>
              </div>
              <div class="mb-3">
                <label class="form-label">نام بانک</label>
                <input type="text" name="bank_name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">نام صاحب حساب</label>
                <input type="text" name="account_holder" class="form-control" required>
              </div>
              <button type="button" class="btn btn-secondary w-100 prev-step mt-2">بازگشت</button>
              <button type="button" class="btn btn-primary w-100 next-step mt-2">مرحله بعد</button>
            </div>

            {{-- Step 5: تایید --}}
            <div id="step5" class="d-none text-center">
              <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
              <h5 class="fw-bold mb-3">بررسی و تأیید نهایی اطلاعات</h5>
              <button type="button" class="btn btn-secondary prev-step">بازگشت</button>
              <button type="submit" class="btn btn-success ms-2">تأیید و ثبت</button>
            </div>

        </form>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// --- GEO logic (static JSON from /public/data) ---
const pSel=document.getElementById('province'),
      cSel=document.getElementById('city'),
      coSel=document.getElementById('county'),
      vSel=document.getElementById('village'),
      hP=document.getElementById('province_name'),
      hC=document.getElementById('city_name'),
      hCo=document.getElementById('county_name');
function opt(v,t){ return `<option value="${v}">${t}</option>`; }
function reset(sel,text,dis=true){ sel.innerHTML=opt('',text); if(dis) sel.disabled=true; else sel.disabled=false; }

let DATA_PROVINCES=null, DATA_CITIES=null, DATA_COUNTIES=null, DATA_VILLAGES=null;
const URLS={
  provinces: "{{ asset('data/provinces.json') }}",
  cities: "{{ asset('data/provinces_cities.json') }}",
  counties: "{{ asset('data/provinces_cities_counties.json') }}",
  villages: "{{ asset('data/provinces_cities_counties_villages.json') }}",
};

async function loadProvinces(){
  try{
    if(!DATA_PROVINCES){ DATA_PROVINCES = await fetch(URLS.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
    populateProvinces();
  }catch{ reset(pSel,'خطا در بارگذاری استان‌ها'); }
}

function populateProvinces(){
  pSel.innerHTML=opt('','انتخاب استان');
  DATA_PROVINCES.forEach(p=>{
    pSel.insertAdjacentHTML('beforeend', opt(p.provinceId, p.provinceName));
  });
  pSel.disabled=false;
}

pSel.addEventListener('change',()=>{
  hP.value=pSel.options[pSel.selectedIndex]?.text || '';
  reset(cSel,'بارگذاری...',false); reset(coSel,'ابتدا شهر',true); reset(vSel,'ابتدا شهرستان',true);
  if(!pSel.value){ reset(cSel,'انتخاب شهر'); return; }
  (async ()=>{
    try{
      if(!DATA_CITIES){ DATA_CITIES = await fetch(URLS.cities,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
      const cities = DATA_CITIES.filter(row=>row.provinceId===pSel.value);
      cSel.innerHTML=opt('','انتخاب شهر');
      cities.forEach(c=> cSel.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)) );
      cSel.disabled=false;
    }catch{ cSel.innerHTML=opt('','خطا در بارگذاری شهر'); }
  })();
});

cSel.addEventListener('change',()=>{
  hC.value=cSel.options[cSel.selectedIndex]?.text || '';
  reset(coSel,'بارگذاری...',false); reset(vSel,'ابتدا شهرستان',true);
  if(!cSel.value){ reset(coSel,'انتخاب شهرستان'); return; }
  (async ()=>{
    try{
      if(!DATA_COUNTIES){ DATA_COUNTIES = await fetch(URLS.counties,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
      const counties = DATA_COUNTIES.filter(row=>row.provinceId===pSel.value && row.cityId===cSel.value);
      coSel.innerHTML=opt('','انتخاب شهرستان');
      counties.forEach(co=> coSel.insertAdjacentHTML('beforeend', opt(co.countyId, co.countyName)) );
      coSel.disabled=false;
    }catch{ coSel.innerHTML=opt('','خطا در بارگذاری شهرستان'); }
  })();
});

coSel.addEventListener('change',()=>{
  hCo.value=coSel.options[coSel.selectedIndex]?.text || '';
  reset(vSel,'بارگذاری...',false);
  if(!coSel.value){ reset(vSel,'(اختیاری) انتخاب روستا'); return; }
  (async ()=>{
    try{
      if(!DATA_VILLAGES){ DATA_VILLAGES = await fetch(URLS.villages,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
      const villages = DATA_VILLAGES.filter(row=>row.provinceId===pSel.value && row.cityId===cSel.value && row.countyId===coSel.value);
      vSel.innerHTML=opt('','(اختیاری) انتخاب روستا');
      villages.forEach(v=>{
        if(v.villageName && v.villageName.trim()){
          vSel.insertAdjacentHTML('beforeend', opt(v.villageName, v.villageName));
        }
      });
      vSel.disabled=false;
    }catch{ reset(vSel,'خطا',false); }
  })();
});

reset(pSel,'در حال بارگذاری...',false); reset(cSel,'ابتدا استان',true); reset(coSel,'ابتدا شهر',true); reset(vSel,'ابتدا شهرستان',true);
loadProvinces();

// --- جدید: کنترل مراحل فرم ---
const steps=['step1','step2','step3','step4','step5'];
let current=0;

function showStep(i){
  steps.forEach((id,idx)=>{
    const el=document.getElementById(id);
    if(!el) return;
    el.classList.toggle('d-none', idx!==i);
  });
  current=i;
}

function validateStep(i){
  // ساده: فقط چک کردن فیلدهای required قابل مشاهده
  const wrapper=document.getElementById(steps[i]);
  if(!wrapper) return true;
  let ok=true;
  wrapper.querySelectorAll('[required]').forEach(inp=>{
    if(inp.closest('#'+steps[i]) && !inp.value.trim()){
      inp.classList.add('is-invalid'); ok=false;
    }else{
      inp.classList.remove('is-invalid');
    }
  });
  return ok;
}

document.querySelectorAll('.next-step').forEach(btn=>{
  btn.addEventListener('click',()=>{
    if(!validateStep(current)) return;
    if(current < steps.length-1) showStep(current+1);
  });
});

document.querySelectorAll('.prev-step').forEach(btn=>{
  btn.addEventListener('click',()=>{
    if(current>0) showStep(current-1);
  });
});

// شروع
showStep(0);
</script>
@endpush
