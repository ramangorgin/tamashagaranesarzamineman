@extends('layouts.admin')

@section('title','مدیریت رزروها')

@push('styles')
<style>
  .admin-booking-header{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.1rem}
  .filter-bar{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:.9rem 1rem;display:flex;flex-wrap:wrap;gap:.7rem;box-shadow:0 4px 12px rgba(0,0,0,.04)}
  .filter-bar .form-control,.filter-bar select{min-width:150px}
  .booking-table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;overflow:hidden}
  table.admin-bookings{width:100%;margin:0;font-size:.78rem}
  table.admin-bookings thead{background:linear-gradient(90deg,#0d6efd,#2563eb);color:#fff}
  table.admin-bookings th,table.admin-bookings td{padding:.6rem .55rem;vertical-align:middle}
  table.admin-bookings tbody tr{cursor:pointer;transition:.25s}
  table.admin-bookings tbody tr:hover{background:#f1f5f9}
  .badge-status span{white-space:nowrap}
  .empty-box{padding:2.2rem;text-align:center;color:#64748b}
  .empty-box i{font-size:2.8rem;color:#0d6efd;opacity:.25}
  @media(max-width:768px){
    .filter-bar{flex-direction:column}
    .filter-bar .form-control,.filter-bar select{width:100%;min-width:0}
    table.admin-bookings thead{display:none}
    table.admin-bookings tbody tr{display:block;padding:.75rem .75rem;border-bottom:1px solid #e2e8f0}
    table.admin-bookings tbody tr td{display:flex;justify-content:space-between;padding:.2rem 0;font-size:.72rem}
    table.admin-bookings tbody tr td::before{content:attr(data-label);font-weight:600;color:#334155}
  }
  .pdp-container{z-index:1060!important}
  .date-input-group{position:relative;display:flex;align-items:center}
  .date-input-group button{position:absolute;left:.55rem;top:50%;transform:translateY(-50%);background:transparent;border:none;color:#0d6efd;cursor:pointer;font-size:1rem}
  .date-input-group button:focus{outline:none}
  .modal-gradient-header{background:linear-gradient(135deg,#0d6efd,#2563eb);color:#fff}
  .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.75rem;margin-top:.5rem}
  .info-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:.7rem;padding:.55rem .6rem;font-size:.7rem;display:flex;flex-direction:column;gap:.25rem}
  .info-card span.label{font-size:.6rem;color:#64748b}
  .price-chip{display:inline-flex;align-items:center;gap:.35rem;background:#0d6efd;color:#fff;padding:.35rem .7rem;border-radius:2rem;font-size:.65rem}
</style>
@endpush

@section('content')
  <div class="admin-booking-header">
    <h5 class="mb-0 d-flex align-items-center gap-2 text-primary fw-semibold"><i class="bi bi-calendar-check"></i> مدیریت رزروها</h5>
    <span class="badge bg-light text-dark border border-primary fw-normal">{{ $bookings->total() }} رزرو</span>
  </div>

  <form method="GET" class="filter-bar mb-3">
    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="جستجوی مهمان / اقامت‌گاه">
    <select name="status" class="form-select form-select-sm">
      <option value="">همه وضعیت‌ها</option>
      <option value="pending" @selected($filters['status']==='pending')>در انتظار پرداخت</option>
      <option value="paid" @selected($filters['status']==='paid')>پرداخت‌شده</option>
      <option value="cancelled" @selected($filters['status']==='cancelled')>لغو شده</option>
    </select>
    <div class="date-input-group">
      <input type="text" id="from_display" data-jdp class="form-control form-control-sm" placeholder="از تاریخ" autocomplete="off" value="{{ $filters['from'] }}">
      <button type="button" id="from_btn" aria-label="انتخاب از"><i class="bi bi-calendar-event"></i></button>
      <input type="hidden" name="from" id="from" value="{{ $filters['from'] }}">
    </div>
    <div class="date-input-group">
      <input type="text" id="to_display" data-jdp class="form-control form-control-sm" placeholder="تا تاریخ" autocomplete="off" value="{{ $filters['to'] }}">
      <button type="button" id="to_btn" aria-label="انتخاب تا"><i class="bi bi-calendar-range"></i></button>
      <input type="hidden" name="to" id="to" value="{{ $filters['to'] }}">
    </div>
    <div class="d-flex gap-2 ms-auto">
      <button class="btn btn-sm btn-primary d-flex align-items-center gap-1"><i class="bi bi-search"></i> اعمال</button>
      <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">ریست</a>
    </div>
  </form>

  <div class="booking-table-wrap">
    @if($bookings->count())
      <table class="admin-bookings">
        <thead>
          <tr>
            <th>#</th>
            <th>مهمان</th>
            <th>اقامت‌گاه</th>
            <th>میزبان</th>
            <th>تاریخ</th>
            <th>شب‌ها</th>
            <th>نفرات</th>
            <th>مبلغ</th>
            <th>وضعیت</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bookings as $b)
            @php($n=$b->nights)
            <tr data-bs-toggle="modal" data-bs-target="#bookingAdminModal"
                data-id="{{ $b->id }}"
                data-guest="{{ $b->user?->name }}"
                data-phone="{{ $b->user?->phone }}"
                data-national="{{ $b->user?->national_id ?? '—' }}"
                data-stay="{{ $b->stay?->title }}"
                data-host="{{ $b->stay?->host?->name ?? '—' }}"
                data-host-phone="{{ $b->stay?->host?->phone ?? '—' }}"
                data-host-email="{{ $b->stay?->host?->email ?? '—' }}"
                data-host-national="{{ $b->stay?->host?->national_id ?? '—' }}"
                data-address="{{ $b->stay?->address ?? '—' }}"
                data-dates="{{ $b->start_date->format('Y-m-d') }} تا {{ $b->end_date->format('Y-m-d') }}"
                data-nights="{{ $n }}"
                data-guests="پایه {{ $b->base_guests }} / اضافه {{ $b->extra_guests }}"
                data-price="{{ number_format($b->final_price) }}"
                data-status-html="{!! $b->status_badge !!}" >
              <td data-label="#">{{ $b->id }}</td>
              <td data-label="مهمان" class="fw-semibold text-primary">{{ Str::limit($b->user?->name,22,'…') }}</td>
              <td data-label="اقامت‌گاه">{{ Str::limit($b->stay?->title,24,'…') }}</td>
              <td data-label="میزبان">{{ Str::limit($b->stay?->host?->name,20,'…') }}</td>
              <td data-label="تاریخ">{{ $b->start_date->format('Y/m/d') }}<span class="text-muted"> تا </span>{{ $b->end_date->format('Y/m/d') }}</td>
              <td data-label="شب‌ها">{{ $n }}</td>
              <td data-label="نفرات">{{ $b->base_guests }} / {{ $b->extra_guests }}</td>
              <td data-label="مبلغ" class="text-success fw-semibold">{{ number_format($b->final_price) }}</td>
              <td data-label="وضعیت" class="badge-status">{!! $b->status_badge !!}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-3">{{ $bookings->links() }}</div>
    @else
      <div class="empty-box">
        <i class="bi bi-calendar2-x"></i>
        <p class="mt-3 mb-1">رزروی یافت نشد.</p>
        <small>با اعمال فیلتر دیگر امتحان کنید.</small>
      </div>
    @endif
  </div>

  <div class="modal fade" id="bookingAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header modal-gradient-header">
          <h6 class="modal-title d-flex align-items-center gap-2"><i class="bi bi-info-circle"></i> مدیریت رزرو <span id="admBookingId" class="badge bg-light text-dark"></span></h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="info-grid">
            <div class="info-card"><span class="label">مهمان</span><strong id="admGuest">—</strong></div>
            <div class="info-card"><span class="label">شماره تماس مهمان</span><strong id="admPhone">—</strong></div>
            <div class="info-card"><span class="label">کد ملی مهمان</span><strong id="admNational">—</strong></div>
            <div class="info-card"><span class="label">اقامت‌گاه</span><strong id="admStay">—</strong></div>
            <div class="info-card"><span class="label">آدرس اقامت‌گاه</span><strong id="admAddress">—</strong></div>
            <div class="info-card"><span class="label">میزبان</span><strong id="admHost">—</strong></div>
            <div class="info-card"><span class="label">شماره میزبان</span><strong id="admHostPhone">—</strong></div>
            <div class="info-card"><span class="label">ایمیل میزبان</span><strong id="admHostEmail">—</strong></div>
            <div class="info-card"><span class="label">کد ملی میزبان</span><strong id="admHostNational">—</strong></div>
            <div class="info-card"><span class="label">تاریخ‌ها</span><strong id="admDates">—</strong></div>
            <div class="info-card"><span class="label">شب‌ها</span><strong id="admNights">—</strong></div>
            <div class="info-card"><span class="label">نفرات</span><strong id="admGuests">—</strong></div>
            <div class="info-card"><span class="label">وضعیت</span><strong id="admStatus">—</strong></div>
            <div class="info-card"><span class="label">مبلغ</span><strong class="price-chip" id="admPrice">—</strong></div>
          </div>
          <div class="mt-3 text-muted small">فرآیند رزرو خودکار است و نیازی به تایید یا رد دستی توسط مدیر ندارد.</div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('table.admin-bookings tbody tr').forEach(function(r){
      r.addEventListener('click', function(){
        const d=r.dataset;
        admBookingId.textContent='#'+(d.id||'—');
        admGuest.textContent=d.guest||'—';
        admPhone.textContent=d.phone||'—';
        admNational.textContent=d.national||'—';
        admStay.textContent=d.stay||'—';
        admAddress.textContent=d.address||'—';
        admHost.textContent=d.host||'—';
        admHostPhone.textContent=d.hostPhone||'—';
        admHostEmail.textContent=d.hostEmail||'—';
        admHostNational.textContent=d.hostNational||'—';
        admDates.textContent=d.dates||'—';
        admNights.textContent=d.nights||'—';
        admGuests.textContent=d.guests||'—';
        admStatus.innerHTML=d.statusHtml||'—';
        admPrice.textContent=(d.price? d.price+' تومان':'—');
      });
    });
  });
</script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const fd=document.getElementById('from_display');
  const td=document.getElementById('to_display');
  const fh=document.getElementById('from');
  const th=document.getElementById('to');
  function greg(detail, fallback){
    try{
      if(detail?.date?.gregorian?.date) return detail.date.gregorian.date;
      if(detail?.date?.gregorian) return detail.date.gregorian;
      if(typeof detail?.date?.format==='function') return detail.date.format('YYYY-MM-DD','en');
    }catch(_){}
    return fallback;
  }
  fd?.addEventListener('jdp:change',e=>{ if(fh) fh.value = greg(e.detail, fd.value); });
  td?.addEventListener('jdp:change',e=>{ if(th) th.value = greg(e.detail, td.value); });
  fd?.addEventListener('change',()=>{ if(fh) fh.value = fd.value; });
  td?.addEventListener('change',()=>{ if(th) th.value = td.value; });
  document.getElementById('from_btn')?.addEventListener('click',()=> fd?.focus());
  document.getElementById('to_btn')?.addEventListener('click',()=> td?.focus());
});
</script>
@endpush
