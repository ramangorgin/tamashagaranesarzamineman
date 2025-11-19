@extends('layouts.host')
@section('title','تکمیل اطلاعات میزبان')

@section('content')
<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:640px;">
    <div class="card-body p-4">
      <h4 class="text-center mb-4 fw-bold text-primary">
        <i class="bi bi-person-lines-fill me-2"></i> تکمیل اطلاعات میزبان
      </h4>
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
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
  // Step navigation (if you have multi-step)
  let step = 1;
  function showStep(targetId, currentId){
    const $current = $(currentId);
    const $target  = $(targetId);
    $target.removeClass('d-none').hide();
    $current.fadeOut(200, () => $target.fadeIn(220));
  }
  $('.next-step').on('click', function(){
    if(step === 1){ showStep('#step2','#step1'); step = 2; }
    else if(step === 2){ showStep('#step3','#step2'); step = 3; }
    else if(step === 3){ showStep('#step4','#step3'); step = 4; }
    else if(step === 4){ showStep('#step5','#step4'); step = 5; }
  });
  $('.prev-step').on('click', function(){
    if(step === 2){ showStep('#step1','#step2'); step = 1; }
    else if(step === 3){ showStep('#step2','#step3'); step = 2; }
    else if(step === 4){ showStep('#step3','#step4'); step = 3; }
    else if(step === 5){ showStep('#step4','#step5'); step = 4; }
  });

  // Cascading selects
  const provinceSel = $('#province');
  const citySel     = $('#city');
  const countySel   = $('#county');
  const villageSel  = $('#village');

  const hProvince   = $('#province_name');
  const hCity       = $('#city_name');
  const hCounty     = $('#county_name');

  function opt(v,t){ return `<option value="${v}">${t}</option>`; }
  function load(url){
    return fetch(url, { headers: { 'Accept': 'application/json' } })
      .then(r => {
        if(!r.ok) throw new Error('HTTP '+r.status);
        return r.json();
      });
  }
  function reset(sel, placeholder, disable=true){
    sel.html(opt('', placeholder));
    if(disable) sel.prop('disabled', true);
  }

  function geoUrl(name, params = {}) {
    const base = {
      provinces: "{{ route('geo.provinces') }}",
      cities: "{{ route('geo.cities', ['province' => '___']) }}",
      counties: "{{ route('geo.counties', ['province' => '___P', 'city' => '___C']) }}",
      villages: "{{ route('geo.villages', ['province' => '___P', 'city' => '___C', 'county' => '___K']) }}"
    };
    let u;
    if (name === 'provinces') u = base.provinces;
    if (name === 'cities') u = base.cities.replace('___', params.province);
    if (name === 'counties') u = base.counties.replace('___P', params.province).replace('___C', params.city);
    if (name === 'villages') u = base.villages.replace('___P', params.province).replace('___C', params.city).replace('___K', params.county);
    return u;
  }

  // Load provinces
  reset(provinceSel, 'انتخاب استان', false);
  reset(citySel,     'ابتدا استان را انتخاب کنید');
  reset(countySel,   'ابتدا شهر را انتخاب کنید');
  reset(villageSel,  'ابتدا بخش را انتخاب کنید');

  load(geoUrl('provinces')).then(data=>{
    provinceSel.html(opt('', 'انتخاب استان'));
    data.forEach(p=>{
      // Your JSON has provinceName and provinceId
      provinceSel.append(opt(p.provinceId, p.provinceName));
    });
  }).catch(err=>{
    console.error('Load provinces failed:', err);
    provinceSel.html(opt('', 'خطا در بارگذاری استان‌ها'));
  });

  provinceSel.on('change', function(){
    const pid  = this.value;
    const name = $(this).find('option:selected').text();
    hProvince.val(name);

    reset(citySel,    'در حال بارگذاری...', false);
    reset(countySel,  'ابتدا شهر را انتخاب کنید');
    reset(villageSel, 'ابتدا بخش را انتخاب کنید');

    if(!pid){ reset(citySel,'انتخاب شهر'); return; }

    citySel.prop('disabled', true).html(opt('', 'در حال بارگذاری...'));
    load(geoUrl('cities',{province: pid})).then(rows=>{
      citySel.prop('disabled', false).html(opt('', 'انتخاب شهر'));
      rows.forEach(c=>{
        const id = c.cityId ?? c.CityId ?? c.id;
        const nm = c.cityName ?? c.CityName ?? c.name;
        if(id && nm) citySel.append(opt(id, nm));
      });
    }).catch(()=> citySel.html(opt('', 'خطا در بارگذاری شهرها')));
  });

  citySel.on('change', function(){
    const pid  = provinceSel.val();
    const cid  = this.value;
    const name = $(this).find('option:selected').text();
    hCity.val(name);

    reset(countySel,  'در حال بارگذاری...', false);
    reset(villageSel, 'ابتدا بخش را انتخاب کنید');

    if(!cid){ reset(countySel,'انتخاب بخش/شهرستان'); return; }

    countySel.prop('disabled', true).html(opt('', 'در حال بارگذاری...'));
    load(geoUrl('counties',{province: pid, city: cid})).then(rows=>{
      countySel.prop('disabled', false).html(opt('', 'انتخاب بخش/شهرستان'));
      rows.forEach(c=>{
        const id = c.countyId ?? c.CountyId ?? c.id;
        const nm = c.countyName ?? c.CountyName ?? c.name;
        if(id && nm) countySel.append(opt(id, nm));
      });
    }).catch(()=> countySel.html(opt('', 'خطا در بارگذاری بخش/شهرستان')));
  });

  countySel.on('change', function(){
    const pid  = provinceSel.val();
    const cid  = citySel.val();
    const coid = this.value;
    const name = $(this).find('option:selected').text();
    hCounty.val(name);

    reset(villageSel, 'در حال بارگذاری...', false);
    if(!coid){ reset(villageSel,'(اختیاری) انتخاب روستا'); return; }

    villageSel.prop('disabled', true).html(opt('', 'در حال بارگذاری...'));
    load(geoUrl('villages',{province: pid, city: cid, county: coid})).then(rows=>{
      villageSel.prop('disabled', false).html(opt('', '(اختیاری) انتخاب روستا'));
      if(!rows.length){ villageSel.append(opt('', 'روستایی ثبت نشده')); return; }
      rows.forEach(v=>{
        const nm = v.villageName ?? v.VillageName ?? v.name;
        if(nm) villageSel.append(opt(nm, nm)); // store name only
      });
    }).catch(()=> villageSel.html(opt('', 'خطا در بارگذاری روستاها')));
  });
});
</script>
@endpush
