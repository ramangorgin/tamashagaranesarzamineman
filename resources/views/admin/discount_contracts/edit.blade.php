@extends('layouts.admin')
@section('title', 'ویرایش قرارداد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.discount_contracts.index') }}">تخفیفات سازمانی</a></li>
    <li class="breadcrumb-item active">ویرایش</li>
@endsection

@section('breadcrumb-actions')
    <a href="{{ route('admin.discount_contracts.show',$contract) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-eye"></i> مشاهده
    </a>
@endsection

@section('content')
<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger small mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <form id="contractEditForm" action="{{ route('admin.discount_contracts.update', $contract) }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4">
                @csrf @method('PUT')

                <label class="form-label">عنوان</label>
                <input type="text" name="title" class="form-control mb-3" value="{{ old('title', $contract->title) }}" required>

                <label class="form-label">درصد تخفیف</label>
                <div class="input-group mb-3">
                    <input type="number" name="discount_percent" class="form-control" min="0" max="100" step=".5" value="{{ old('discount_percent',$contract->discount_percent) }}" required>
                    <span class="input-group-text">%</span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label">تاریخ شروع</label>
                        <div class="input-group">
                            <input type="text" id="start_date_display" data-jdp class="form-control" value="{{ verta($contract->start_date)->format('Y/m/d') }}" placeholder="انتخاب">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                        <input type="hidden" name="start_date" id="start_date" value="{{ $contract->start_date->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mb-3 position-relative">
                        <label class="form-label">تاریخ پایان</label>
                        <div class="input-group">
                            <input type="text" id="end_date_display" data-jdp class="form-control" value="{{ verta($contract->end_date)->format('Y/m/d') }}" placeholder="انتخاب">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        </div>
                        <input type="hidden" name="end_date" id="end_date" value="{{ $contract->end_date->format('Y-m-d') }}">
                    </div>
                </div>

                <label class="form-label">وضعیت</label>
                <select name="is_active" class="form-select mb-3">
                    <option value="1" {{ old('is_active',$contract->is_active)?'selected':'' }}>فعال</option>
                    <option value="0" {{ !old('is_active',$contract->is_active)?'selected':'' }}>غیرفعال</option>
                </select>

                <label class="form-label">توضیحات</label>
                <textarea name="description" rows="3" class="form-control mb-4">{{ old('description',$contract->description) }}</textarea>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-save"></i> ذخیره تغییرات</button>
                    <a href="{{ route('admin.discount_contracts.index') }}" class="btn btn-light">بازگشت</a>
                </div>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">اعضای قرارداد</h6>

                    <form class="row g-2 mb-3" method="POST" action="{{ route('admin.discount_contract_members.store',$contract) }}">
                        @csrf
                        <div class="col-md-4"><input name="full_name" class="form-control" placeholder="نام کامل" required></div>
                        <div class="col-md-3"><input name="national_id" class="form-control" placeholder="کدملی"></div>
                        <div class="col-md-3"><input name="phone" class="form-control" placeholder="موبایل"></div>
                        <div class="col-md-2"><button class="btn btn-success w-100"><i class="bi bi-plus-lg"></i></button></div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light"><tr><th>#</th><th>نام</th><th>کدملی</th><th>موبایل</th><th class="text-end">عملیات</th></tr></thead>
                            <tbody>
                            @forelse($contract->members as $m)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $m->full_name }}</td>
                                    <td>{{ $m->national_id ?: '-' }}</td>
                                    <td>{{ $m->phone ?: '-' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.discount_contract_members.destroy',[$contract,$m]) }}" onsubmit="return confirm('حذف این عضو؟')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center">عضوی ثبت نشده است.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jalali-moment@3.3.10/dist/jalali-moment.browser.js"></script>
<script>
$(function() {
  // Validate only the main edit form (not the member form)
  $('#contractEditForm').on('submit', function(e){
    if(!$('#start_date').val() || !$('#end_date').val()){
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'تاریخ ناقص',
        text: 'لطفاً تاریخ شروع و پایان را انتخاب کنید.',
        confirmButtonText: 'باشه'
      });
    }
  });

    // Wire JalaliDatePicker display fields to hidden Gregorian values
    const sd = document.getElementById('start_date_display');
    const ed = document.getElementById('end_date_display');
    const sh = document.getElementById('start_date');
    const eh = document.getElementById('end_date');
    function greg(detail, fallback){
        try{
            if(detail?.date?.gregorian?.date) return detail.date.gregorian.date;
            if(detail?.date?.gregorian) return detail.date.gregorian;
            if(typeof detail?.date?.format==='function') return detail.date.format('YYYY-MM-DD','en');
        }catch(_){}
        return fallback;
    }
    function toJalaliStr(greg){
        try{
            if(!greg || !window.moment) return '';
            const m = window.moment(greg,'YYYY-MM-DD');
            if(m.isValid()) return m.locale('fa').format('jYYYY/jMM/jDD');
        }catch(_){}
        return '';
    }
    // Initialize visible Jalali from hidden Gregorian if needed
    if(sd && sh && sh.value && !sd.value){ const j = toJalaliStr(sh.value); if(j) sd.value=j; }
    if(ed && eh && eh.value && !ed.value){ const j = toJalaliStr(eh.value); if(j) ed.value=j; }
    // Update hidden on picker changes
    sd?.addEventListener('jdp:change', e => { if(sh) sh.value = greg(e.detail, sh.value); });
    ed?.addEventListener('jdp:change', e => { if(eh) eh.value = greg(e.detail, eh.value); });
    // Fallback on manual change
    sd?.addEventListener('change', () => { if(sh && sd.value && window.moment){
        const m = window.moment(sd.value,'jYYYY/jMM/jDD'); if(m.isValid()) sh.value = m.format('YYYY-MM-DD');
    }});
    ed?.addEventListener('change', () => { if(eh && ed.value && window.moment){
        const m = window.moment(ed.value,'jYYYY/jMM/jDD'); if(m.isValid()) eh.value = m.format('YYYY-MM-DD');
    }});
});
</script>
@endpush
