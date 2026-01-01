@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.host')
@section('title', ($role ?? null)==='admin' ? 'مدیریت اقامت‌گاه‌ها' : 'اقامت‌گاه‌های من')

@section('breadcrumb')
    @if(($role ?? null)==='admin')
        <li class="breadcrumb-item active">اقامت‌گاه‌ها</li>
    @endif
@endsection

@section('breadcrumb-actions')
  @if(in_array(($role ?? null), ['admin','host']))
    <a href="{{ ($role ?? null)==='admin' ? route('admin.stays.create') : route('host.stays.create') }}" class="btn btn-sm btn-primary">
      <i class="bi bi-plus-lg"></i> ایجاد اقامت‌گاه
    </a>
  @endif
@endsection

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container {{ $role==='admin' ? 'fluid' : 'py-4' }}">

  @if($role==='admin')
    @php
      $nowPeak = \App\Models\PeakPeriod::isNowPeak();
      $nowDiscount = \App\Models\DiscountPeriod::isNowDiscount();
      $peakPeriods = \App\Models\PeakPeriod::orderBy('start_date')->take(10)->get();
      $discountPeriods = \App\Models\DiscountPeriod::orderBy('start_date')->take(10)->get();
    @endphp

    <div class="row g-3 mb-4">
      {{-- Peak Period Form --}}
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow text-warning me-2"></i> بازه پیک</h6>
              <span class="badge {{ $nowPeak?'bg-warning text-dark':'bg-secondary' }}">
                {{ $nowPeak?'فعال':'غیرفعال' }}
              </span>
            </div>
            <form class="d-flex flex-column gap-2" method="POST" action="{{ route('admin.peak_periods.store') }}" onsubmit="return validatePeakForm()">
              @csrf
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small mb-1">شروع (شمسی)</label>
                  <input type="text" id="peak_start_display" data-jdp class="form-control form-control-sm" placeholder="انتخاب" required>
                  <input type="hidden" name="start_date" id="peak_start">
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">پایان (شمسی)</label>
                  <input type="text" id="peak_end_display" data-jdp class="form-control form-control-sm" placeholder="انتخاب" required>
                  <input type="hidden" name="end_date" id="peak_end">
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">درصد افزایش</label>
                  <div class="input-group input-group-sm">
                    <input type="number" name="percentage" class="form-control" min="0" max="100" step="0.5" placeholder="0" required>
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">استان‌ها (خالی = همه)</label>
                  <select name="provinces[]" id="peak_provinces" class="form-select form-select-sm" multiple>
                    @php
                      $provinces = json_decode(file_get_contents(public_path('data/provinces.json')), true);
                    @endphp
                    @foreach($provinces as $province)
                      <option value="{{ $province['provinceId'] }}">{{ $province['provinceName'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <button class="btn btn-sm btn-warning text-dark" style="width:100%;"><i class="bi bi-plus-lg"></i> ثبت</button>
              </div>
            </form>
            @if($peakPeriods->count())
              <div class="table-responsive mt-3">
                <table class="table table-sm table-borderless align-middle mb-0">
                  <thead>
                    <tr class="small">
                      <th>#</th>
                      <th>شروع</th>
                      <th>پایان</th>
                      <th>درصد</th>
                      <th>استان‌ها</th>
                      <th>وضعیت</th>
                      <th class="text-end">حذف</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($peakPeriods as $p)
                    @php
                      $today = \Carbon\Carbon::today();
                      $active = $today->gte($p->start_date) && $today->lte($p->end_date);
                    @endphp
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>@faNum(verta($p->start_date)->format('Y/m/d'))</td>
                      <td>@faNum(verta($p->end_date)->format('Y/m/d'))</td>
                      <td><span class="badge bg-warning text-dark">{{ rtrim(rtrim(number_format($p->percentage ?? 0, 2), '0'), '.') }}%</span></td>
                      <td class="small">
                        @if(empty($p->provinces) || !is_array($p->provinces) || count($p->provinces) === 0)
                          <span class="text-muted">همه</span>
                        @else
                          @php
                            $provinces = json_decode(file_get_contents(public_path('data/provinces.json')), true);
                            $provinceMap = collect($provinces)->keyBy('provinceId');
                            $selectedNames = collect($p->provinces)->map(function($id) use ($provinceMap) {
                              return $provinceMap[$id]['provinceName'] ?? $id;
                            })->take(2);
                          @endphp
                          {{ $selectedNames->implode('، ') }}
                          @if(count($p->provinces) > 2)
                            <span class="text-muted">+{{ count($p->provinces) - 2 }}</span>
                          @endif
                        @endif
                      </td>
                      <td>
                        <span class="badge {{ $active?'bg-warning text-dark':'bg-light text-muted' }}">
                          {{ $active?'جاری':'-' }}
                        </span>
                      </td>
                      <td class="text-end">
                        <form method="POST" action="{{ route('admin.peak_periods.destroy',$p) }}" onsubmit="return confirm('حذف این بازه؟')">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Discount Period Form --}}
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="mb-0 fw-bold"><i class="bi bi-graph-down-arrow text-success me-2"></i> بازه تخفیف</h6>
              <span class="badge {{ $nowDiscount?'bg-success':'bg-secondary' }}">
                {{ $nowDiscount?'فعال':'غیرفعال' }}
              </span>
            </div>
            <form class="d-flex flex-column gap-2" method="POST" action="{{ route('admin.discount_periods.store') }}" onsubmit="return validateDiscountForm()">
              @csrf
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small mb-1">شروع (شمسی)</label>
                  <input type="text" id="discount_start_display" data-jdp class="form-control form-control-sm" placeholder="انتخاب" required>
                  <input type="hidden" name="start_date" id="discount_start">
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">پایان (شمسی)</label>
                  <input type="text" id="discount_end_display" data-jdp class="form-control form-control-sm" placeholder="انتخاب" required>
                  <input type="hidden" name="end_date" id="discount_end">
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">درصد تخفیف</label>
                  <div class="input-group input-group-sm">
                    <input type="number" name="percentage" class="form-control" min="0" max="100" step="0.5" placeholder="0" required>
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small mb-1">استان‌ها (خالی = همه)</label>
                  <select name="provinces[]" id="discount_provinces" class="form-select form-select-sm" multiple>
                    @php
                      $provinces = json_decode(file_get_contents(public_path('data/provinces.json')), true);
                    @endphp
                    @foreach($provinces as $province)
                      <option value="{{ $province['provinceId'] }}">{{ $province['provinceName'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <button class="btn btn-sm btn-success" style="width:100%;"><i class="bi bi-plus-lg"></i> ثبت</button>
              </div>
            </form>
            @if($discountPeriods->count())
              <div class="table-responsive mt-3">
                <table class="table table-sm table-borderless align-middle mb-0">
                  <thead>
                    <tr class="small">
                      <th>#</th>
                      <th>شروع</th>
                      <th>پایان</th>
                      <th>درصد</th>
                      <th>استان‌ها</th>
                      <th>وضعیت</th>
                      <th class="text-end">حذف</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($discountPeriods as $d)
                    @php
                      $today = \Carbon\Carbon::today();
                      $active = $today->gte($d->start_date) && $today->lte($d->end_date);
                    @endphp
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>@faNum(verta($d->start_date)->format('Y/m/d'))</td>
                      <td>@faNum(verta($d->end_date)->format('Y/m/d'))</td>
                      <td><span class="badge bg-success">{{ rtrim(rtrim(number_format($d->percentage ?? 0, 2), '0'), '.') }}%</span></td>
                      <td class="small">
                        @if(empty($d->provinces) || !is_array($d->provinces) || count($d->provinces) === 0)
                          <span class="text-muted">همه</span>
                        @else
                          @php
                            $provinces = json_decode(file_get_contents(public_path('data/provinces.json')), true);
                            $provinceMap = collect($provinces)->keyBy('provinceId');
                            $selectedNames = collect($d->provinces)->map(function($id) use ($provinceMap) {
                              return $provinceMap[$id]['provinceName'] ?? $id;
                            })->take(2);
                          @endphp
                          {{ $selectedNames->implode('، ') }}
                          @if(count($d->provinces) > 2)
                            <span class="text-muted">+{{ count($d->provinces) - 2 }}</span>
                          @endif
                        @endif
                      </td>
                      <td>
                        <span class="badge {{ $active?'bg-success':'bg-light text-muted' }}">
                          {{ $active?'جاری':'-' }}
                        </span>
                      </td>
                      <td class="text-end">
                        <form method="POST" action="{{ route('admin.discount_periods.destroy',$d) }}" onsubmit="return confirm('حذف این بازه؟')">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-2 mb-md-0">
      <i class="bi {{ $role==='admin' ? 'bi-buildings text-primary' : 'bi-house-door text-primary' }} me-2"></i>
      {{ $role==='admin' ? 'مدیریت اقامت‌گاه‌ها' : 'اقامت‌گاه‌های من' }}
    </h4>
    <div class="d-flex gap-2">
      <form id="searchForm" method="GET" class="d-flex">
        <div class="input-group">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="جستجو عنوان یا شناسه...">
          <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>

    </div>
  </div>

  {{-- Session messages are handled by admin.partials.messages in admin layout --}}
  @if($errors->has('overlap'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.showError) {
          window.showError('{{ $errors->first('overlap') }}');
        }
      });
    </script>
  @endif

  <div class="card shadow-sm border-0 rounded-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr class="text-nowrap small">
            <th>#</th>
            <th>عنوان</th>
            <th>دسته‌بندی</th>
            <th>استان / شهر</th>
            @if($role==='admin')
              <th>میزبان</th>
              <th>تأیید</th>
            @else
              <th>ظرفیت (پایه/کل)</th>
              <th>قیمت</th>
              <th>کمیسیون%</th>
            @endif
            <th>وضعیت</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
        @forelse($stays as $stay)
          <tr class="fade-in">
            <td class="text-muted small">{{ $stay->id }}</td>
            <td class="fw-semibold">{{ Str::limit($stay->title,40) }}</td>
            <td><span class="badge bg-info-subtle text-info">{{ stayTypeToPersian($stay->category) }}</span></td>
            <td class="small">{{ $stay->province_name }} / {{ $stay->city_name }}</td>

            @if($role==='admin')
              <td class="small">{{ $stay->host->name ?? $stay->host->full_name ?? '—' }}</td>
              <td>
                @php $ms = $stay->moderation_status; @endphp
                <span class="badge {{ $ms==='approved' ? 'bg-success' : ($ms==='rejected' ? 'bg-danger' : 'bg-secondary') }}">
                  {{ $ms==='approved' ? 'تأیید شده' : ($ms==='rejected' ? 'رد شده' : 'در انتظار') }}
                </span>
                @if($stay->reject_reason)
                  <div class="small text-muted" title="{{ $stay->reject_reason }}">علت: {{ \Illuminate\Support\Str::limit($stay->reject_reason,30) }}</div>
                @endif
              </td>
            @else
              <td class="small">{{ $stay->base_capacity }} / {{ $stay->capacity }}</td>
              <td class="small">
                @php $mode=$stay->pricing_mode ?? 'per_person'; @endphp
                @if($mode==='per_night')
                  {!! displayStayPrice($stay, 'per_night') !!} <span class="text-muted">/ شب</span>
                @else
                  {!! displayStayPrice($stay, 'per_person') !!} <span class="text-muted">/ نفر</span>
                @endif
              </td>
              <td class="small">{{ rtrim(rtrim(number_format($stay->site_commission,2),'0'),'.') }}</td>
            @endif

            <td>
              @if($stay->is_active)
                <span class="badge bg-success"><i class="bi bi-check-circle"></i> فعال</span>
              @else
                <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> در انتظار انتشار</span>
              @endif
              @if($stay->is_peak)
                <span class="badge bg-warning text-dark">پیک</span>
              @endif
            </td>
            <td class="text-center">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('stays.show',$stay) }}" class="btn btn-outline-secondary" title="نمایش"><i class="bi bi-eye"></i></a>
                <a href="{{ $role==='admin' ? route('admin.stays.edit',$stay) : route('host.stays.edit',$stay) }}" class="btn btn-outline-primary" title="ویرایش"><i class="bi bi-pencil-square"></i></a>
                @if($role==='admin')
                  @if($stay->moderation_status === 'pending')
                    <button type="button" class="btn btn-outline-success btn-approve" data-url="{{ route('admin.stays.approve',$stay) }}" title="تأیید"><i class="bi bi-check2"></i></button>
                    <button type="button" class="btn btn-outline-warning btn-reject" data-url="{{ route('admin.stays.reject',$stay) }}" title="رد"><i class="bi bi-x-lg"></i></button>
                  @elseif($stay->moderation_status === 'approved')
                    <button type="button" class="btn btn-outline-{{ $stay->is_active ? 'secondary' : 'success' }} btn-toggle-status" 
                            data-url="{{ route('admin.stays.toggleStatus',$stay) }}" 
                            data-active="{{ $stay->is_active ? '1' : '0' }}"
                            title="{{ $stay->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                      <i class="bi {{ $stay->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                    </button>
                  @endif
                @endif
                <button type="button" class="btn btn-outline-danger delete-btn"
                  data-title="{{ Str::limit($stay->title,30) }}"
                  data-url="{{ $role==='admin' ? route('admin.stays.destroy',$stay) : route('host.stays.destroy',$stay) }}"
                  title="حذف"><i class="bi bi-trash3"></i></button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $role==='admin' ? 8 : 9 }}" class="text-center py-4 text-muted">
              <i class="bi bi-inbox-fill fs-3 d-block mb-2"></i>
              هیچ اقامت‌گاهی ثبت نشده است.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($stays instanceof \Illuminate\Pagination\AbstractPaginator && $stays->hasPages())
      <div class="card-footer bg-transparent">
        {{ $stays->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>

<style>
.fade-in { animation: fadeIn .35s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(4px);} to {opacity:1; transform:translateY(0);} }
</style>
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  min-height: 38px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
  border-color: #86b7fe;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  background-color: #0d6efd;
  border: 1px solid #0d6efd;
  color: #fff;
  padding: 2px 8px;
  margin: 2px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
  color: #fff;
  margin-right: 5px;
}
</style>
@endpush
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    Swal.fire({
      title:'حذف اقامت‌گاه؟',
      html:'<div class="small text-muted">«'+btn.dataset.title+'»</div>',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'حذف',
      cancelButtonText:'انصراف',
      confirmButtonColor:'#dc3545',
      cancelButtonColor:'#6c757d'
    }).then(r=>{
      if(r.isConfirmed){
        const f=document.createElement('form');
        f.method='POST'; f.action=btn.dataset.url;
        f.innerHTML='@csrf @method("DELETE")';
        document.body.appendChild(f); f.submit();
      }
    });
  });
});
document.querySelectorAll('.btn-approve').forEach(btn=>{
  btn.addEventListener('click',()=>{
    Swal.fire({
      title:'تأیید اقامت‌گاه؟',
      icon:'question',
      showCancelButton:true,
      confirmButtonText:'تأیید',
      cancelButtonText:'انصراف',
      confirmButtonColor:'#22c55e',
      cancelButtonColor:'#6c757d'
    }).then(r=>{ if(r.isConfirmed) postPatch(btn.dataset.url); });
  });
});
document.querySelectorAll('.btn-reject').forEach(btn=>{
  btn.addEventListener('click',async ()=>{
    const {value: reason, isConfirmed} = await Swal.fire({
      title:'علت رد (اختیاری)',
      input:'text',
      inputPlaceholder:'علت رد...',
      showCancelButton:true,
      confirmButtonText:'رد کردن',
      cancelButtonText:'انصراف',
      confirmButtonColor:'#fd7e14',
      cancelButtonColor:'#6c757d'
    });
    if(isConfirmed){ postPatch(btn.dataset.url, {reason}); }
  });
});
document.querySelectorAll('.btn-toggle-status').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const isActive = btn.dataset.active === '1';
    const action = isActive ? 'غیرفعال' : 'فعال';
    Swal.fire({
      title: `آیا می‌خواهید اقامت‌گاه را ${action} کنید؟`,
      icon:'question',
      showCancelButton:true,
      confirmButtonText: action,
      cancelButtonText:'انصراف',
      confirmButtonColor:'#22c55e',
      cancelButtonColor:'#6c757d'
    }).then(r=>{ 
      if(r.isConfirmed) postPatch(btn.dataset.url); 
    });
  });
});
function postPatch(url, data={}){
  const f=document.createElement('form');
  f.method='POST'; f.action=url;
  f.innerHTML=`@csrf @method('PATCH')`;
  for(const k in data){
    const i=document.createElement('input'); i.type='hidden'; i.name=k; i.value=data[k]; f.appendChild(i);
  }
  document.body.appendChild(f); f.submit();
}
</script>
<script>
@if($role==='admin')
document.addEventListener('DOMContentLoaded',function(){
  function greg(detail, fallback){
    try{
      if(detail?.date?.gregorian?.date) return detail.date.gregorian.date;
      if(detail?.date?.gregorian) return detail.date.gregorian;
      if(typeof detail?.date?.format==='function') return detail.date.format('YYYY-MM-DD','en');
    }catch(_){}
    // If we can't get Gregorian from event, return the fallback (which should be the Jalali date string)
    // The backend normalizeDate function will handle the conversion
    return fallback;
  }
  
  // Peak Period date pickers
  const ps = document.getElementById('peak_start_display');
  const pe = document.getElementById('peak_end_display');
  const phs = document.getElementById('peak_start');
  const phe = document.getElementById('peak_end');
  ps?.addEventListener('jdp:change', ev => { 
    if(phs) {
      const gregorian = greg(ev.detail, ps.value);
      phs.value = gregorian;
    }
  });
  pe?.addEventListener('jdp:change', ev => { 
    if(phe) {
      const gregorian = greg(ev.detail, pe.value);
      phe.value = gregorian;
    }
  });
  // Fallback for manual input
  ps?.addEventListener('change', ()=> { if(phs && !phs.value) phs.value = ps.value; });
  pe?.addEventListener('change', ()=> { if(phe && !phe.value) phe.value = pe.value; });
  
  // Discount Period date pickers
  const ds = document.getElementById('discount_start_display');
  const de = document.getElementById('discount_end_display');
  const dhs = document.getElementById('discount_start');
  const dhe = document.getElementById('discount_end');
  ds?.addEventListener('jdp:change', ev => { 
    if(dhs) {
      const gregorian = greg(ev.detail, ds.value);
      dhs.value = gregorian;
    }
  });
  de?.addEventListener('jdp:change', ev => { 
    if(dhe) {
      const gregorian = greg(ev.detail, de.value);
      dhe.value = gregorian;
    }
  });
  // Fallback for manual input
  ds?.addEventListener('change', ()=> { if(dhs && !dhs.value) dhs.value = ds.value; });
  de?.addEventListener('change', ()=> { if(dhe && !dhe.value) dhe.value = de.value; });
  
  // Form validation functions
  window.validatePeakForm = function() {
    if(!phs?.value || !phe?.value) {
      if (window.showWarning) {
        window.showWarning('لطفا تاریخ‌ها را انتخاب کنید');
      }
      return false;
    }
    return true;
  };
  
  window.validateDiscountForm = function() {
    if(!dhs?.value || !dhe?.value) {
      if (window.showWarning) {
        window.showWarning('لطفا تاریخ‌ها را انتخاب کنید');
      }
      return false;
    }
    return true;
  };
  
  // Initialize Select2 for province selects
  if(typeof $ !== 'undefined' && $.fn.select2) {
    $('#peak_provinces, #discount_provinces').select2({
      placeholder: 'انتخاب استان‌ها (خالی = همه)',
      allowClear: true,
      dir: 'rtl',
      language: {
        noResults: function() { return 'نتیجه‌ای یافت نشد'; },
        searching: function() { return 'در حال جستجو...'; }
      },
      width: '100%'
    });
  } else {
    // Fallback: Load jQuery and Select2 if not available
    const script1 = document.createElement('script');
    script1.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    script1.onload = function() {
      const script2 = document.createElement('script');
      script2.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
      script2.onload = function() {
        $('#peak_provinces, #discount_provinces').select2({
          placeholder: 'انتخاب استان‌ها (خالی = همه)',
          allowClear: true,
          dir: 'rtl',
          language: {
            noResults: function() { return 'نتیجه‌ای یافت نشد'; },
            searching: function() { return 'در حال جستجو...'; }
          },
          width: '100%'
        });
      };
      document.head.appendChild(script2);
    };
    document.head.appendChild(script1);
  }
});
@endif
</script>
@endpush