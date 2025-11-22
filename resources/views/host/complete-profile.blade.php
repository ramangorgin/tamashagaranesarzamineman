@extends('layouts.host')
@section('title','تکمیل اطلاعات میزبان')

@section('content')
<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:640px;">
    <div class="card-body p-4">
      <h4 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-person-lines-fill me-2"></i> تکمیل اطلاعات میزبان
      </h4>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @elseif(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
      @endif

      @if(in_array($host->status, ['pending','approved']))
        <div class="alert {{ $host->status==='pending' ? 'alert-warning' : 'alert-success' }} text-center">
          {{ $host->status==='pending'
              ? 'اطلاعات شما ارسال شد و در صف بررسی است. لطفاً منتظر تأیید مدیریت بمانید.'
              : 'اطلاعات شما تأیید شد.' }}
        </div>
      @else
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
// --- موجود: GEO logic ---
const GEO_BASE="{{ url('/geo') }}";
const pSel=document.getElementById('province'),
      cSel=document.getElementById('city'),
      coSel=document.getElementById('county'),
      vSel=document.getElementById('village'),
      hP=document.getElementById('province_name'),
      hC=document.getElementById('city_name'),
      hCo=document.getElementById('county_name');
function opt(v,t){ return `<option value="${v}">${t}</option>`; }
function reset(sel,text,dis=true){ sel.innerHTML=opt('',text); if(dis) sel.disabled=true; }
function fill(sel,text,rows){ sel.innerHTML=opt('',text)+rows.map(r=>`<option value="${r.id}" data-name="${r.name}">${r.name}</option>`).join(''); sel.disabled=false; }

reset(pSel,'بارگذاری...',false); reset(cSel,'ابتدا استان',true); reset(coSel,'ابتدا شهر',true); reset(vSel,'ابتدا شهرستان',true);
fetch("{{ route('geo.provinces') }}").then(r=>r.json()).then(rows=>fill(pSel,'انتخاب استان',rows));

pSel.onchange=()=>{
  hP.value=pSel.selectedOptions[0]?.dataset.name||'';
  reset(cSel,'بارگذاری...',false); reset(coSel,'ابتدا شهر',true); reset(vSel,'ابتدا شهرستان',true);
  if(!pSel.value){ reset(cSel,'انتخاب شهر'); return; }
  fetch(`${GEO_BASE}/provinces/${pSel.value}/cities`).then(r=>r.json()).then(rows=>fill(cSel,'انتخاب شهر',rows)).catch(()=>reset(cSel,'خطا',false));
};
cSel.onchange=()=>{
  hC.value=cSel.selectedOptions[0]?.dataset.name||'';
  reset(coSel,'بارگذاری...',false); reset(vSel,'ابتدا شهرستان',true);
  if(!cSel.value){ reset(coSel,'انتخاب شهرستان'); return; }
  fetch(`${GEO_BASE}/provinces/${pSel.value}/cities/${cSel.value}/counties`).then(r=>r.json()).then(rows=>fill(coSel,'انتخاب شهرستان',rows)).catch(()=>reset(coSel,'خطا',false));
};
coSel.onchange=()=>{
  hCo.value=coSel.selectedOptions[0]?.dataset.name||'';
  reset(vSel,'بارگذاری...',false);
  if(!coSel.value){ reset(vSel,'—'); return; }
  fetch(`${GEO_BASE}/provinces/${pSel.value}/cities/${cSel.value}/counties/${coSel.value}/villages`).then(r=>r.json()).then(rows=>{
    vSel.innerHTML=opt('','(اختیاری) انتخاب روستا')+rows.map(r=>`<option value="${r.name}">${r.name}</option>`).join('');
    vSel.disabled=false;
  }).catch(()=>reset(vSel,'خطا',false));
};

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
