@extends('layouts.admin')
@section('title','ویرایش میزبان')

@section('content')
<div class="breadcrumb-container">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.hosts.index') }}">میزبانان</a></li>
    <li class="breadcrumb-item active">ویرایش</li>
  </ol>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-3">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.hosts.update',$host) }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      @if($errors->any())
        <div class="alert alert-danger small">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">نام</label><input name="name" class="form-control" value="{{ old('name',$host->name) }}" required></div>
        <div class="col-md-4"><label class="form-label">موبایل</label><input name="phone" class="form-control" value="{{ old('phone',$host->phone) }}" required></div>
        <div class="col-md-4"><label class="form-label">کد ملی</label><input name="national_id" class="form-control" value="{{ old('national_id',$host->national_id) }}"></div>
        <div class="col-md-4"><label class="form-label">ایمیل</label><input type="email" name="email" class="form-control" value="{{ old('email',$host->email) }}"></div>
        <div class="col-md-4"><label class="form-label">کد پستی</label><input name="postal_code" class="form-control" value="{{ old('postal_code',$host->postal_code) }}"></div>

        <div class="col-md-3"><label class="form-label">استان</label><select id="province" name="province_id" class="form-select"></select><input type="hidden" name="province_name" id="province_name" value="{{ old('province_name',$host->province_name) }}"></div>
        <div class="col-md-3"><label class="form-label">شهر</label><select id="city" name="city_id" class="form-select" disabled></select><input type="hidden" name="city_name" id="city_name" value="{{ old('city_name',$host->city_name) }}"></div>
        <div class="col-md-3"><label class="form-label">شهرستان</label><select id="county" name="county_id" class="form-select" disabled></select><input type="hidden" name="county_name" id="county_name" value="{{ old('county_name',$host->county_name) }}"></div>
        <div class="col-md-3"><label class="form-label">روستا</label><select id="village" name="village_name" class="form-select" disabled><option value="">—</option></select></div>
        <div class="col-md-12"><label class="form-label">آدرس</label><input name="address" class="form-control" value="{{ old('address',$host->address) }}"></div>

        <div class="col-md-4"><label class="form-label">شبا</label><input name="iban" class="form-control" value="{{ old('iban',$host->iban) }}"></div>
        <div class="col-md-4"><label class="form-label">بانک</label><input name="bank_name" class="form-control" value="{{ old('bank_name',$host->bank_name) }}"></div>
        <div class="col-md-4"><label class="form-label">صاحب حساب</label><input name="account_holder" class="form-control" value="{{ old('account_holder',$host->account_holder) }}"></div>

        <div class="col-md-4"><label class="form-label">وضعیت</label>
          <select name="status" class="form-select">
            <option value="pending"  @selected(old('status',$host->status)=='pending')>در انتظار</option>
            <option value="approved" @selected(old('status',$host->status)=='approved')>تأیید شده</option>
            <option value="rejected" @selected(old('status',$host->status)=='rejected')>رد شده</option>
          </select>
        </div>
        <div class="col-md-8"><label class="form-label">علت رد (اختیاری)</label>
          <input name="rejection_reason" class="form-control" value="{{ old('rejection_reason',$host->rejection_reason) }}">
        </div>

        <div class="col-md-4">
          <label class="form-label">تصویر کارت ملی</label>
          <input type="file" name="id_card_image" accept="image/*" class="form-control">
          @if($host->id_card_image) <img src="{{ asset($host->id_card_image) }}" class="img-fluid mt-1 rounded"> @endif
        </div>
        <div class="col-md-4">
          <label class="form-label">سلفی</label>
          <input type="file" name="selfie_image" accept="image/*" class="form-control">
          @if($host->selfie_image) <img src="{{ asset($host->selfie_image) }}" class="img-fluid mt-1 rounded"> @endif
        </div>
        <div class="col-md-4">
          <label class="form-label">مجوز / سند</label>
          <input type="file" name="business_license" accept="image/*" class="form-control">
          @if($host->business_license) <img src="{{ asset($host->business_license) }}" class="img-fluid mt-1 rounded"> @endif
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-success">ذخیره</button>
        <a href="{{ route('admin.hosts.show',$host) }}" class="btn btn-outline-secondary">انصراف</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const selP=document.getElementById('province'),
        selC=document.getElementById('city'),
        selCo=document.getElementById('county'),
        selV=document.getElementById('village');
  const hP=document.getElementById('province_name'),
        hC=document.getElementById('city_name'),
        hCo=document.getElementById('county_name');

  const initP='{{ old('province_id',$host->province_id) }}',
        initC='{{ old('city_id',$host->city_id) }}',
        initCo='{{ old('county_id',$host->county_id) }}',
        initV='{{ old('village_name',$host->village_name) }}';

  function opt(v,t){ return `<option value="${v}">${t}</option>`; }
  function reset(sel, ph, disable=true){ sel.innerHTML=opt('',ph); if(disable) sel.setAttribute('disabled','disabled'); else sel.removeAttribute('disabled'); }

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
      selP.innerHTML = opt('','انتخاب استان');
      DATA_PROVINCES.forEach(p=> selP.insertAdjacentHTML('beforeend', opt(p.provinceId,p.provinceName)) );
      selP.removeAttribute('disabled');
      if(initP){ selP.value=initP; hP.value='{{ old('province_name',$host->province_name) }}'; selP.dispatchEvent(new Event('change')); }
    }catch{ selP.innerHTML=opt('', 'خطا در بارگذاری استان‌ها'); }
  }

  selP.addEventListener('change',()=>{
    hP.value = selP.options[selP.selectedIndex]?.text || '';
    reset(selC,'در حال بارگذاری...',false); reset(selCo,'ابتدا شهر را انتخاب کنید'); reset(selV,'ابتدا بخش را انتخاب کنید');
    if(!selP.value){ reset(selC,'انتخاب شهر'); return; }
    (async ()=>{
      try{
        if(!DATA_CITIES){ DATA_CITIES = await fetch(URLS.cities,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const cities = DATA_CITIES.filter(row=>row.provinceId===selP.value);
        selC.innerHTML=opt('','انتخاب شهر');
        cities.forEach(c=> selC.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)) );
        selC.removeAttribute('disabled');
        if(initC){ selC.value=initC; hC.value='{{ old('city_name',$host->city_name) }}'; selC.dispatchEvent(new Event('change')); }
      }catch{ selC.innerHTML=opt('', 'خطا در بارگذاری شهرها'); }
    })();
  });

  selC.addEventListener('change',()=>{
    hC.value = selC.options[selC.selectedIndex]?.text || '';
    reset(selCo,'در حال بارگذاری...',false); reset(selV,'ابتدا بخش را انتخاب کنید');
    if(!selC.value){ reset(selCo,'انتخاب بخش/شهرستان'); return; }
    (async ()=>{
      try{
        if(!DATA_COUNTIES){ DATA_COUNTIES = await fetch(URLS.counties,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const counties = DATA_COUNTIES.filter(row=>row.provinceId===selP.value && row.cityId===selC.value);
        selCo.innerHTML=opt('','انتخاب بخش/شهرستان');
        counties.forEach(c=> selCo.insertAdjacentHTML('beforeend', opt(c.countyId,c.countyName)) );
        selCo.removeAttribute('disabled');
        if(initCo){ selCo.value=initCo; hCo.value='{{ old('county_name',$host->county_name) }}'; selCo.dispatchEvent(new Event('change')); }
      }catch{ selCo.innerHTML=opt('', 'خطا در بارگذاری بخش'); }
    })();
  });

  selCo.addEventListener('change',()=>{
    hCo.value = selCo.options[selCo.selectedIndex]?.text || '';
    reset(selV,'در حال بارگذاری...',false);
    if(!selCo.value){ reset(selV,'(اختیاری) انتخاب روستا'); return; }
    (async ()=>{
      try{
        if(!DATA_VILLAGES){ DATA_VILLAGES = await fetch(URLS.villages,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const villages = DATA_VILLAGES.filter(row=>row.provinceId===selP.value && row.cityId===selC.value && row.countyId===selCo.value);
        selV.innerHTML=opt('', '(اختیاری) انتخاب روستا');
        if(!villages.length) selV.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
        else villages.forEach(v=> { if(v.villageName && v.villageName.trim()) selV.insertAdjacentHTML('beforeend', opt(v.villageName,v.villageName)); });
        selV.removeAttribute('disabled');
        if(initV){ selV.value=initV; }
      }catch{ selV.innerHTML=opt('', 'خطا در بارگذاری روستاها'); }
    })();
  });

  reset(selP,'در حال بارگذاری...',false); reset(selC,'ابتدا استان را انتخاب کنید'); reset(selCo,'ابتدا شهر را انتخاب کنید'); reset(selV,'ابتدا بخش را انتخاب کنید');
  loadProvinces();
})();
</script>
@endpush