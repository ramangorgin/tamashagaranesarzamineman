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
      $peakPeriods = \App\Models\PeakPeriod::orderBy('start_date')->take(10)->get();
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center">
        <div>
          <span class="badge {{ $nowPeak?'bg-warning text-dark':'bg-secondary' }}">
            {{ $nowPeak?'در حال حاضر پیک فعال است':'پیک فعال نیست' }}
          </span>
        </div>
        <form class="d-flex flex-wrap align-items-end gap-2" method="POST" action="{{ route('admin.peak_periods.store') }}">
          @csrf
          <div>
            <label class="form-label small mb-1">شروع (شمسی)</label>
            <input type="text" id="peak_start_display" class="form-control form-control-sm" placeholder="انتخاب">
            <input type="hidden" name="start_date" id="peak_start">
          </div>
          <div>
            <label class="form-label small mb-1">پایان (شمسی)</label>
            <input type="text" id="peak_end_display" class="form-control form-control-sm" placeholder="انتخاب">
            <input type="hidden" name="end_date" id="peak_end">
          </div>
          <div class="pt-2">
            <button class="btn btn-sm btn-warning text-dark"><i class="bi bi-plus-lg"></i> ثبت بازه پیک</button>
          </div>
        </form>
      </div>
      @if($peakPeriods->count())
        <div class="table-responsive px-3 pb-3">
          <table class="table table-sm table-borderless align-middle mb-0">
            <thead>
              <tr class="small">
                <th>#</th>
                <th>شروع</th>
                <th>پایان</th>
                <th>وضعیت</th>
                <th class="text-end">حذف</th>
              </tr>
            </thead>
            <tbody>
            @foreach($peakPeriods as $p)
              @php
                $active = now()->toDateString() >= $p->start_date && now()->toDateString() <= $p->end_date;
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>@faNum(verta($p->start_date)->format('Y/m/d'))</td>
                <td>@faNum(verta($p->end_date)->format('Y/m/d'))</td>
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

  @if(session('success'))
    <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
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
              <th>قیمت پایه</th>
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
              <td class="small">{{ number_format($stay->price_per_person) }}</td>
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
                  @if($stay->moderation_status!=='approved')
                    <button type="button" class="btn btn-outline-success btn-approve" data-url="{{ route('admin.stays.approve',$stay) }}" title="تأیید"><i class="bi bi-check2"></i></button>
                  @endif
                  @if($stay->moderation_status!=='rejected')
                    <button type="button" class="btn btn-outline-warning btn-reject" data-url="{{ route('admin.stays.reject',$stay) }}" title="رد"><i class="bi bi-x-lg"></i></button>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      confirmButtonColor:'#dc3545'
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
    Swal.fire({title:'تأیید اقامت‌گاه؟',icon:'question',showCancelButton:true,confirmButtonText:'تأیید'})
    .then(r=>{ if(r.isConfirmed) postPatch(btn.dataset.url); });
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
      confirmButtonColor:'#fd7e14'
    });
    if(isConfirmed){ postPatch(btn.dataset.url, {reason}); }
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
$(function(){
  $('#peak_start_display').persianDatepicker({
    format:'YYYY/MM/DD', autoClose:true,
    onSelect:u=>$('#peak_start').val(new persianDate(u).toCalendar('gregorian').format('YYYY-MM-DD'))
  });
  $('#peak_end_display').persianDatepicker({
    format:'YYYY/MM/DD', autoClose:true,
    onSelect:u=>$('#peak_end').val(new persianDate(u).toCalendar('gregorian').format('YYYY-MM-DD'))
  });
});
@endif
</script>
@endpush