@extends('layouts.admin')

@section('title','تخفیفات سازمانی')

@section('breadcrumb')
    <li class="breadcrumb-item active">تخفیفات سازمانی</li>
@endsection

@section('breadcrumb-actions')
    <a href="{{ route('admin.discount_contracts.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> قرارداد جدید
    </a>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))  <div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.discount_contracts.index') }}" class="row g-2 align-items-end" id="searchForm">
                <div class="col-md-4">
                    <label class="form-label small mb-1">جستجو</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="عنوان قرارداد، نام عضو، موبایل یا کدملی">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">وضعیت</label>
                    <select name="status" class="form-select">
                        <option value="">همه</option>
                        <option value="active"   @selected(request('status')==='active')>فعال</option>
                        <option value="inactive" @selected(request('status')==='inactive')>غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-2 position-relative">
                    <label class="form-label small mb-1">از تاریخ (شمسی)</label>
                    <div class="input-group">
                        <input type="text" id="from_display" data-jdp class="form-control" placeholder="انتخاب" value="{{ request('from') }}">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                    <input type="hidden" name="from" id="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2 position-relative">
                    <label class="form-label small mb-1">تا تاریخ (شمسی)</label>
                    <div class="input-group">
                        <input type="text" id="to_display" data-jdp class="form-control" placeholder="انتخاب" value="{{ request('to') }}">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                    <input type="hidden" name="to" id="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.discount_contracts.index') }}" class="btn btn-light w-100"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>عنوان</th>
                            <th>درصد</th>
                            <th>دوره</th>
                            <th>وضعیت</th>
                            <th>تعداد اعضا</th>
                            <th style="width:180px">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contracts->firstItem() + $loop->index }}</td>
                            <td class="text-start">{{ $contract->title }}</td>
                            <td><span class="badge bg-primary">{{ rtrim(rtrim(number_format($contract->discount_percent,2), '0'),'.') }}%</span></td>
                            <td>
                                @faNum(verta($contract->start_date)->format('Y/m/d'))
                                —
                                @faNum(verta($contract->end_date)->format('Y/m/d'))
                            </td>
                            <td>
                                @if(!$contract->is_active)
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @elseif(!$contract->start_date || !$contract->end_date)
                                    <span class="badge bg-secondary">ناقص</span>
                                @elseif(now(config('app.timezone'))->startOfDay()->lt($contract->start_date))
                                    <span class="badge bg-warning text-dark">آتی</span>
                                @elseif(now(config('app.timezone'))->startOfDay()->gt($contract->end_date))
                                    <span class="badge bg-dark">منقضی</span>
                                @elseif($contract->is_currently_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @endif
                            </td>
                            <td>{{ $contract->members_count ?? $contract->members->count() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.discount_contracts.show', $contract) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.discount_contracts.edit', $contract) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.discount_contracts.destroy', $contract) }}" method="POST"
                                          onsubmit="return confirm('حذف این قرارداد؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted py-4">موردی یافت نشد.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    نمایش {{ $contracts->firstItem() }} تا {{ $contracts->lastItem() }} از {{ $contracts->total() }} مورد
                </small>
                {{ $contracts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
  const fd=document.getElementById('from_display');
  const td=document.getElementById('to_display');
  const fh=document.getElementById('from');
  const th=document.getElementById('to');
    // Convert initial hidden Gregorian to visible Jalali (YYYY/MM/DD)
    function toJalaliStr(greg){
        try{
            if(!greg) return '';
            // Use jalali-moment if available; fallback to existing value
            if(window.moment){
                const m = window.moment(greg, 'YYYY-MM-DD');
                if(m.isValid()) return m.locale('fa').format('jYYYY/jMM/jDD');
            }
        }catch(_){}
        return '';
    }
  function greg(detail, fallback){
    try{
      if(detail?.date?.gregorian?.date) return detail.date.gregorian.date;
      if(detail?.date?.gregorian) return detail.date.gregorian;
      if(typeof detail?.date?.format==='function') return detail.date.format('YYYY-MM-DD','en');
    }catch(_){}
    return fallback;
  }
    // Initialize visible fields from hidden Gregorian if present
    if(fd && fh && fh.value && !fd.value){
        const j = toJalaliStr(fh.value);
        if(j) fd.value = j;
    }
    if(td && th && th.value && !td.value){
        const j = toJalaliStr(th.value);
        if(j) td.value = j;
    }
  fd?.addEventListener('jdp:change',e=>{ if(fh) fh.value = greg(e.detail, fd.value); });
  td?.addEventListener('jdp:change',e=>{ if(th) th.value = greg(e.detail, td.value); });
  fd?.addEventListener('change',()=>{ if(fh) fh.value = fd.value; });
  td?.addEventListener('change',()=>{ if(th) th.value = td.value; });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/jalali-moment@3.3.10/dist/jalali-moment.browser.js"></script>
@endpush
