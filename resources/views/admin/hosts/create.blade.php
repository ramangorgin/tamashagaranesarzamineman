@extends('layouts.admin')
@section('title','ایجاد میزبان')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.hosts.index') }}">میزبانان</a></li>
    <li class="breadcrumb-item active">ایجاد</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mt-3">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.hosts.store') }}" enctype="multipart/form-data">
      @csrf
      @if($errors->any())
        <div class="alert alert-danger small">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">نام</label><input name="name" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">موبایل</label><input name="phone" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">کد ملی</label><input name="national_id" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">ایمیل</label><input type="email" name="email" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">کد پستی</label><input name="postal_code" class="form-control"></div>

        <div class="col-md-3"><label class="form-label">استان</label><select id="province" name="province_id" class="form-select"></select><input type="hidden" name="province_name" id="province_name"></div>
        <div class="col-md-3"><label class="form-label">شهر</label><select id="city" name="city_id" class="form-select" disabled></select><input type="hidden" name="city_name" id="city_name"></div>
        <div class="col-md-3"><label class="form-label">شهرستان</label><select id="county" name="county_id" class="form-select" disabled></select><input type="hidden" name="county_name" id="county_name"></div>
        <div class="col-md-3"><label class="form-label">روستا</label><select id="village" name="village_name" class="form-select" disabled><option value="">—</option></select></div>
        <div class="col-md-12"><label class="form-label">آدرس</label><input name="address" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">شبا</label><input name="iban" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">بانک</label><input name="bank_name" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">صاحب حساب</label><input name="account_holder" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">وضعیت</label>
          <select name="status" class="form-select">
            <option value="pending">در انتظار</option>
            <option value="approved">تأیید شده</option>
            <option value="rejected">رد شده</option>
          </select>
        </div>
        <div class="col-md-8"><label class="form-label">علت رد (اختیاری)</label><input name="rejection_reason" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">تصویر کارت ملی</label><input type="file" name="id_card_image" accept="image/*" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">سلفی</label><input type="file" name="selfie_image" accept="image/*" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">مجوز / سند</label><input type="file" name="business_license" accept="image/*" class="form-control"></div>
      </div>
      <div class="mt-3">
        <button class="btn btn-success">ثبت</button>
        <a href="{{ route('admin.hosts.index') }}" class="btn btn-outline-secondary">انصراف</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const GEO_BASE = "{{ url('/geo') }}";

const selP = document.getElementById('province'),
      selC = document.getElementById('city'),
      selCo= document.getElementById('county'),
      selV = document.getElementById('village');
const hP = document.getElementById('province_name'),
      hC = document.getElementById('city_name'),
      hCo= document.getElementById('county_name');

function opt(v,t){ return `<option value="${v}">${t}</option>`; }
function reset(sel, text, dis=true){ sel.innerHTML=opt('',text); if(dis) sel.disabled=true; }
function fill(sel, text, arr){ sel.innerHTML=opt('',text)+arr.map(r=>`<option value="${r.id}" data-name="${r.name}">${r.name}</option>`).join(''); sel.disabled=false; }

reset(selP,'بارگذاری...',false); reset(selC,'ابتدا استان',true); reset(selCo,'ابتدا شهر',true); reset(selV,'ابتدا شهرستان',true);

fetch("{{ route('geo.provinces') }}").then(r=>r.json()).then(rows=>{
  fill(selP,'انتخاب استان', rows);
}).catch(()=>reset(selP,'خطا',false));

selP.onchange = () => {
  hP.value = selP.selectedOptions[0]?.dataset.name || '';
  reset(selC,'بارگذاری...',false); reset(selCo,'ابتدا شهر',true); reset(selV,'ابتدا شهرستان',true);
  if(!selP.value){ reset(selC,'انتخاب شهر'); return; }
  fetch(`${GEO_BASE}/provinces/${selP.value}/cities`).then(r=>r.json()).then(rows=>{
    if(!rows.length){ reset(selC,'هیچ شهری نیست',false); return; }
    fill(selC,'انتخاب شهر', rows);
  }).catch(()=>reset(selC,'خطا',false));
};

selC.onchange = () => {
  hC.value = selC.selectedOptions[0]?.dataset.name || '';
  reset(selCo,'بارگذاری...',false); reset(selV,'ابتدا شهرستان',true);
  if(!selC.value){ reset(selCo,'انتخاب شهرستان'); return; }
  fetch(`${GEO_BASE}/provinces/${selP.value}/cities/${selC.value}/counties`).then(r=>r.json()).then(rows=>{
    if(!rows.length){ reset(selCo,'هیچ شهرستانی نیست',false); return; }
    fill(selCo,'انتخاب شهرستان', rows);
  }).catch(()=>reset(selCo,'خطا',false));
};

selCo.onchange = () => {
  hCo.value = selCo.selectedOptions[0]?.dataset.name || '';
  reset(selV,'بارگذاری...',false);
  if(!selCo.value){ reset(selV,'—'); return; }
  fetch(`${GEO_BASE}/provinces/${selP.value}/cities/${selC.value}/counties/${selCo.value}/villages`).then(r=>r.json()).then(rows=>{
    if(!rows.length){ reset(selV,'روستایی نیست',false); return; }
    selV.innerHTML = opt('','(اختیاری) انتخاب روستا') + rows.map(r=>`<option value="${r.name}">${r.name}</option>`).join('');
    selV.disabled=false;
  }).catch(()=>reset(selV,'خطا',false));
};
</script>
@endpush