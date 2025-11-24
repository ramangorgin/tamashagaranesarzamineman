@extends('layouts.admin')

@section('title','مدیریت رزروها')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
<style>
  .admin-reserve-header{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.1rem}
  .filter-bar{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:.9rem 1rem;display:flex;flex-wrap:wrap;gap:.7rem;box-shadow:0 4px 12px rgba(0,0,0,.04)}
  .filter-bar .form-control,.filter-bar select{min-width:150px}
  .reserve-table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;overflow:hidden}
  table.admin-reserves{width:100%;margin:0;font-size:.78rem}
  table.admin-reserves thead{background:linear-gradient(90deg,#0d6efd,#2563eb);color:#fff}
  table.admin-reserves th,table.admin-reserves td{padding:.6rem .55rem;vertical-align:middle}
  table.admin-reserves tbody tr{cursor:pointer;transition:.25s}
  table.admin-reserves tbody tr:hover{background:#f1f5f9}
  .badge-status span{white-space:nowrap}
  .empty-box{padding:2.2rem;text-align:center;color:#64748b}
  .empty-box i{font-size:2.8rem;color:#0d6efd;opacity:.25}
  @media(max-width:768px){
    .filter-bar{flex-direction:column}
    .filter-bar .form-control,.filter-bar select{width:100%;min-width:0}
    table.admin-reserves thead{display:none}
    table.admin-reserves tbody tr{display:block;padding:.75rem .75rem;border-bottom:1px solid #e2e8f0}
    table.admin-reserves tbody tr td{display:flex;justify-content:space-between;padding:.2rem 0;font-size:.72rem}
    table.admin-reserves tbody tr td::before{content:attr(data-label);font-weight:600;color:#334155}
  }
  .pdp-container{z-index:1060!important}
  .date-input-group{position:relative;display:flex;align-items:center}
  .date-input-group button{position:absolute;left:.55rem;top:50%;transform:translateY(-50%);background:transparent;border:none;color:#0d6efd;cursor:pointer;font-size:1rem}
  .date-input-group button:focus{outline:none}
  .modal-gradient-header{background:linear-gradient(135deg,#0d6efd,#2563eb);color:#fff}
  .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.75rem;margin-top:.5rem}
  .info-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:.7rem;padding:.55rem .6rem;font-size:.7rem;display:flex;flex-direction:column;gap:.25rem}
  .info-card span.label{font-size:.6rem;color:#64748b}
  .action-box{background:#fff;border:1px dashed #0d6efd;border-radius:1rem;padding:.9rem .85rem;margin-top:.9rem;position:relative}
  .action-box::before{content:"";position:absolute;inset:0;border-radius:1rem;padding:2px;background:linear-gradient(135deg,#0d6efd,#9333ea,#fb923c);-webkit-mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);-webkit-mask-composite:x;mask-composite:exclude;opacity:.35}
  .action-box h6{font-weight:700;display:flex;align-items:center;gap:.4rem;color:#0d6efd;margin-bottom:.6rem}
  .reject-reason{resize:vertical;min-height:70px}
  .price-chip{display:inline-flex;align-items:center;gap:.35rem;background:#0d6efd;color:#fff;padding:.35rem .7rem;border-radius:2rem;font-size:.65rem}
</style>
@endpush

@section('content')
  <div class="admin-reserve-header">
    <h5 class="mb-0 d-flex align-items-center gap-2 text-primary fw-semibold"><i class="bi bi-calendar-check"></i> مدیریت رزروها</h5>
    <span class="badge bg-light text-dark border border-primary fw-normal">{{ $bookings->total() }} رزرو</span>
  </div>

  <form method="GET" class="filter-bar mb-3">
    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="جستجوی مهمان / اقامت‌گاه">
    <select name="status" class="form-select form-select-sm">
      <option value="">همه وضعیت‌ها</option>
      <option value="pending" @selected($filters['status']==='pending')>در انتظار پرداخت</option>
      <option value="approved" @selected($filters['status']==='approved')>تایید شده</option>
      <option value="rejected" @selected($filters['status']==='rejected')>رد شده</option>
      <option value="paid" @selected($filters['status']==='paid')>پرداخت‌شده</option>
      <option value="cancelled" @selected($filters['status']==='cancelled')>لغو شده</option>
    </select>
    <div class="date-input-group">
      <input type="text" id="from_display" class="form-control form-control-sm" placeholder="از تاریخ" autocomplete="off">
      <button type="button" id="from_btn" aria-label="انتخاب از"><i class="bi bi-calendar-event"></i></button>
      <input type="hidden" name="from" id="from" value="{{ $filters['from'] }}">
    </div>
    <div class="date-input-group">
      <input type="text" id="to_display" class="form-control form-control-sm" placeholder="تا تاریخ" autocomplete="off">
      <button type="button" id="to_btn" aria-label="انتخاب تا"><i class="bi bi-calendar-range"></i></button>
      <input type="hidden" name="to" id="to" value="{{ $filters['to'] }}">
    </div>
    <div class="d-flex gap-2 ms-auto">
      <button class="btn btn-sm btn-primary d-flex align-items-center gap-1"><i class="bi bi-search"></i> اعمال</button>
      <a href="{{ route('admin.reserves.index') }}" class="btn btn-sm btn-outline-secondary">ریست</a>
    </div>
  </form>

  <div class="reserve-table-wrap">
    @if($bookings->count())
      <table class="admin-reserves">
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

  <!-- Modal for details and actions -->
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
            <div class="info-card"><span class="label">شماره تماس</span><strong id="admPhone">—</strong></div>
            <div class="info-card"><span class="label">کد ملی</span><strong id="admNational">—</strong></div>
            <div class="info-card"><span class="label">اقامت‌گاه</span><strong id="admStay">—</strong></div>
            <div class="info-card"><span class="label">میزبان</span><strong id="admHost">—</strong></div>
            <div class="info-card"><span class="label">تاریخ‌ها</span><strong id="admDates">—</strong></div>
            <div class="info-card"><span class="label">شب‌ها</span><strong id="admNights">—</strong></div>
            <div class="info-card"><span class="label">نفرات</span><strong id="admGuests">—</strong></div>
            <div class="info-card"><span class="label">وضعیت</span><strong id="admStatus">—</strong></div>
            <div class="info-card"><span class="label">مبلغ</span><strong class="price-chip" id="admPrice">—</strong></div>
          </div>
          <div class="action-box">
            <h6><i class="bi bi-gear"></i> عملیات مدیر</h6>
            <form id="bookingActionForm" method="POST" class="d-flex flex-column gap-2">
              @csrf
              @method('PATCH')
              <textarea name="reason" id="rejectReason" class="form-control form-control-sm reject-reason" placeholder="دلیل رد (اختیاری، فقط در رد)" ></textarea>
              <div class="d-flex flex-wrap gap-2">
                <button type="button" id="approveBtn" class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="bi bi-check2-circle"></i> تایید</button>
                <button type="button" id="rejectBtn" class="btn btn-danger btn-sm d-flex align-items-center gap-1"><i class="bi bi-x-circle"></i> رد</button>
                <button type="button" class="btn btn-secondary btn-sm ms-auto" data-bs-dismiss="modal">بستن</button>
              </div>
            </form>
            <small class="text-muted d-block mt-1">پس از تایید یا رد، وضعیت برای میزبان و مهمان قابل مشاهده خواهد بود.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    // Row click populate modal
    document.querySelectorAll('table.admin-reserves tbody tr').forEach(function(r){
      r.addEventListener('click', function(){
        const d=r.dataset;
        admBookingId.textContent='#'+(d.id||'—');
        admGuest.textContent=d.guest||'—';
        admPhone.textContent=d.phone||'—';
        admNational.textContent=d.national||'—';
        admStay.textContent=d.stay||'—';
        admHost.textContent=d.host||'—';
        admDates.textContent=d.dates||'—';
        admNights.textContent=d.nights||'—';
        admGuests.textContent=d.guests||'—';
        admStatus.innerHTML=d.statusHtml||'—';
        admPrice.textContent=(d.price? d.price+' تومان':'—');
        bookingActionForm.dataset.id = d.id;
      });
    });
    // Approve/Reject buttons
    approveBtn.addEventListener('click', function(){
      submitBookingAction('approve');
    });
    rejectBtn.addEventListener('click', function(){
      submitBookingAction('reject');
    });
  });
  function submitBookingAction(type){
    const id = bookingActionForm.dataset.id;
    if(!id) return alert('شناسه رزرو نامشخص است.');
    const url = type==='approve' ? `{{ url('admin/bookings') }}/${id}/approve` : `{{ url('admin/bookings') }}/${id}/reject`;
    const formData = new FormData(bookingActionForm);
    fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PATCH','Accept':'application/json'}, body:formData})
      .then(r=>r.text().then(t=>({ok:r.ok, text:t})))
      .then(res=>{
        if(res.ok){ location.reload(); } else { alert('خطا: '+res.text); }
      }).catch(()=> alert('خطای شبکه'));
  }
</script>
<script>
// Jalali date pickers for filters
$(function(){
  function toEnglishDigits(str){return (str+'').replace(/[۰-۹]/g,d=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(d))}
  var fromInstance = $('#from_display').persianDatepicker({format:'YYYY/MM/DD',initialValue:false,autoClose:true,onSelect:function(unix){const g=new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD'); $('#from').val(toEnglishDigits(g));}});
  var toInstance = $('#to_display').persianDatepicker({format:'YYYY/MM/DD',initialValue:false,autoClose:true,onSelect:function(unix){const g=new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD'); $('#to').val(toEnglishDigits(g));}});
  $('#from_btn').on('click',()=>{try{fromInstance.show();}catch(e){$('#from_display').trigger('focus');}});
  $('#to_btn').on('click',()=>{try{toInstance.show();}catch(e){$('#to_display').trigger('focus');}});
  const fromVal='{{ $filters['from'] }}', toVal='{{ $filters['to'] }}';
  function gToPersian(g){if(!g) return '';const p=g.split('-').map(Number);const unix=new Date(p[0],p[1]-1,p[2]).getTime();return new persianDate(unix).format('YYYY/MM/DD');}
  if(fromVal) $('#from_display').val(gToPersian(fromVal));
  if(toVal) $('#to_display').val(gToPersian(toVal));
});
</script>
@endpush
