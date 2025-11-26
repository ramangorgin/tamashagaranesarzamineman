@extends('layouts.admin')
@section('title', 'ایجاد قرارداد جدید')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.discount_contracts.index') }}">تخفیفات سازمانی</a></li>
    <li class="breadcrumb-item active">ایجاد</li>
@endsection

@section('breadcrumb-actions')
    <a href="{{ route('admin.discount_contracts.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-right"></i> بازگشت
    </a>
@endsection

@section('content')
<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger small mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form id="contractForm" action="{{ route('admin.discount_contracts.store') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">عنوان قرارداد</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">درصد تخفیف</label>
                <div class="input-group">
                    <input type="number" name="discount_percent" class="form-control" min="0" max="100" step=".5" value="{{ old('discount_percent') }}" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">وضعیت</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active','1')=='1'?'selected':'' }}>فعال</option>
                    <option value="0" {{ old('is_active')=='0'?'selected':'' }}>غیرفعال</option>
                </select>
            </div>

            <div class="col-md-6 position-relative">
                <label class="form-label">تاریخ شروع (شمسی)</label>
                <div class="input-group">
                    <input type="text" id="start_date_display" data-jdp class="form-control" placeholder="انتخاب تاریخ" value="{{ old('start_date') }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
            </div>

            <div class="col-md-6 position-relative">
                <label class="form-label">تاریخ پایان (شمسی)</label>
                <div class="input-group">
                    <input type="text" id="end_date_display" data-jdp class="form-control" placeholder="انتخاب تاریخ" value="{{ old('end_date') }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">
            </div>

            <div class="col-12">
                <label class="form-label">توضیحات (اختیاری)</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success"><i class="bi bi-check2"></i> ذخیره قرارداد</button>
            <a href="{{ route('admin.discount_contracts.index') }}" class="btn btn-light">انصراف</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const sd=document.getElementById('start_date_display');
    const ed=document.getElementById('end_date_display');
    const sh=document.getElementById('start_date');
    const eh=document.getElementById('end_date');
    function greg(detail, fallback){
        try{
            if(detail?.date?.gregorian?.date) return detail.date.gregorian.date;
            if(detail?.date?.gregorian) return detail.date.gregorian;
            if(typeof detail?.date?.format==='function') return detail.date.format('YYYY-MM-DD','en');
        }catch(_){}
        return fallback;
    }
    sd?.addEventListener('jdp:change',e=>{ if(sh) sh.value = greg(e.detail, sd.value); });
    ed?.addEventListener('jdp:change',e=>{ if(eh) eh.value = greg(e.detail, ed.value); });
    sd?.addEventListener('change',()=>{ if(sh) sh.value = sd.value; });
    ed?.addEventListener('change',()=>{ if(eh) eh.value = ed.value; });
    document.getElementById('contractForm')?.addEventListener('submit',function(e){
        if(!sh?.value || !eh?.value){
            e.preventDefault();
            Swal.fire({ icon:'warning', title:'تاریخ ناقص', text:'لطفاً تاریخ شروع و پایان را انتخاب کنید.', confirmButtonText:'باشه' });
        }
    });
});
</script>
@endpush