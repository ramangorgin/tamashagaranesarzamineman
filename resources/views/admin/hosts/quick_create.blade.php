@extends('layouts.admin')
@section('title','ایجاد سریع میزبان')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.hosts.index') }}">میزبان‌ها</a></li>
  <li class="breadcrumb-item active">ایجاد سریع</li>
@endsection

@section('content')
<div class="container py-4">
  <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width:680px;">
    <div class="card-body p-4">
      <h5 class="mb-3"><i class="bi bi-person-plus me-2"></i>ایجاد سریع میزبان برای ثبت اقامت‌گاه</h5>
      <p class="text-muted small mb-4">این فرم کوتاه، فقط برای ثبت اولیه میزبان توسط مدیر است. پروفایل کامل بعداً توسط خود میزبان تکمیل خواهد شد.</p>
      @if($errors->any())
        <div class="alert alert-danger small">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif
      <form method="POST" action="{{ route('admin.hosts.quick_store') }}" class="row g-3">
        @csrf
        <div class="col-12">
          <label class="form-label">نام و نام خانوادگی</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">موبایل</label>
          <input type="text" name="phone" class="form-control" inputmode="tel" dir="ltr" required data-normalize-digits>
        </div>
        <div class="col-md-6">
          <label class="form-label">کد ملی (اختیاری)</label>
          <input type="text" name="national_id" class="form-control" inputmode="numeric" dir="ltr" data-normalize-digits>
        </div>
        <div class="d-flex justify-content-between mt-3">
          <a href="{{ route('admin.stays.index') }}" class="btn btn-light">انصراف</a>
          <button type="submit" class="btn btn-primary">ایجاد میزبان و ادامه به ثبت اقامت‌گاه</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Normalize Persian/Arabic digits to English as user types
(function(){
  const fa = '۰۱۲۳۴۵۶۷۸۹';
  const ar = '٠١٢٣٤٥٦٧٨٩';
  function toEnDigits(str){
    if(!str) return '';
    let out='';
    for(const ch of String(str)){
      const iFa = fa.indexOf(ch);
      if(iFa>-1){ out += String(iFa); continue; }
      const iAr = ar.indexOf(ch);
      if(iAr>-1){ out += String(iAr); continue; }
      out += ch;
    }
    return out;
  }
  document.querySelectorAll('[data-normalize-digits]').forEach(inp=>{
    ['input','blur','change'].forEach(ev=> inp.addEventListener(ev,()=>{
      const pos = inp.selectionStart;
      const val = inp.value;
      const norm = toEnDigits(val).replace(/[^0-9+]/g,'');
      if(norm !== val){ inp.value = norm; try{ inp.setSelectionRange(pos,pos); }catch(_){} }
    }));
  });
})();
</script>
@endpush
