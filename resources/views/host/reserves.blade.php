@extends('layouts.host')

@section('title','رزروها')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
<style>
  .reserves-header{display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;margin-bottom:1.25rem}
  .filter-box{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:.85rem 1rem;display:flex;flex-wrap:wrap;gap:.75rem;box-shadow:0 4px 12px rgba(0,0,0,.05)}
  .filter-box .form-control,.filter-box select{min-width:160px}
  .reserve-table-wrapper{background:#ffffff;border:1px solid #e2e8f0;border-radius:1rem;overflow:hidden}
  table.reserves-table{width:100%;margin:0;font-size:.82rem}
  table.reserves-table thead{background:linear-gradient(90deg,#2563eb,#1d4ed8);color:#fff}
  table.reserves-table th,table.reserves-table td{padding:.7rem .6rem;vertical-align:middle}
  table.reserves-table tbody tr{cursor:pointer;transition:.25s}
  table.reserves-table tbody tr:hover{background:#f1f5f9}
  .status-badge span{white-space:nowrap}
  .empty-state{padding:2.25rem;text-align:center;color:#64748b}
  .empty-state i{font-size:3rem;color:#1d4ed8;opacity:.25}
  /* Mobile */
  @media(max-width:768px){
    .filter-box{flex-direction:column}
    .filter-box .form-control,.filter-box select{width:100%;min-width:0}
    table.reserves-table thead{display:none}
    table.reserves-table tbody tr{display:block;padding:.85rem .85rem;border-bottom:1px solid #e2e8f0}
    table.reserves-table tbody tr td{display:flex;justify-content:space-between;padding:.25rem 0;font-size:.78rem}
    table.reserves-table tbody tr td::before{content:attr(data-label);font-weight:600;color:#334155}
  }
  .modal-gradient-header{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff}
  .guest-info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.75rem;margin-top:.5rem}
  .guest-info-grid .info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;padding:.6rem .7rem;font-size:.75rem;display:flex;flex-direction:column;gap:.25rem}
  .guest-info-grid .info-box span.label{font-size:.65rem;color:#64748b}
  .instruction-box{background:#ffffff;border:1px dashed #2563eb;border-radius:1rem;padding:1rem .95rem;margin-top:1rem;position:relative}
  .instruction-box::before{content:"";position:absolute;inset:0;border-radius:1rem;padding:2px;background:linear-gradient(135deg,#2563eb,#9333ea,#fb923c);-webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);-webkit-mask-composite:x;mask-composite:exclude;opacity:.35}
  .instruction-box h6{font-weight:700;display:flex;align-items:center;gap:.4rem;color:#1d4ed8}
  .instruction-list{margin:0;padding-left:1.1rem;padding-right:.3rem;font-size:.72rem;display:grid;gap:.35rem}
  .instruction-list li{background:#f1f5f9;border-radius:.55rem;padding:.4rem .55rem;list-style:none;position:relative}
  .instruction-list li i{color:#2563eb;margin-left:.35rem}
  .price-chip{display:inline-flex;align-items:center;gap:.35rem;background:#1d4ed8;color:#fff;padding:.35rem .7rem;border-radius:2rem;font-size:.7rem}
  .price-chip i{font-size:.9rem}
  /* Persian datepicker z-index fix to appear above layout header */
  .pdp-container{z-index:1055!important}
  .date-filter-group{position:relative;display:flex;align-items:center}
  .date-filter-group .calendar-btn{position:absolute;left:.55rem;top:50%;transform:translateY(-50%);background:transparent;border:none;color:#2563eb;cursor:pointer;font-size:1rem;padding:0;display:flex;align-items:center}
  .date-filter-group .calendar-btn:focus{outline:none}
</style>
@endpush

@section('content')
  <div class="reserves-header">
    <h5 class="mb-0 d-flex align-items-center gap-2 text-primary fw-semibold"><i class="bi bi-calendar-check"></i> رزروهای اقامت‌گاه‌های شما</h5>
    <span class="badge bg-light text-dark border border-primary fw-normal">{{ $bookings->total() }} رزرو ثبت شده</span>
  </div>

  <form method="GET" class="filter-box mb-3">
    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="جستجوی مهمان / عنوان">
    <select name="status" class="form-select form-select-sm">
      <option value="">همه وضعیت‌ها</option>
      <option value="pending" @selected($filters['status']==='pending')>در انتظار پرداخت</option>
      <option value="paid" @selected($filters['status']==='paid')>پرداخت‌شده</option>
      <option value="cancelled" @selected($filters['status']==='cancelled')>لغوشده</option>
    </select>
    <div class="date-filter-group">
      <input type="text" id="from_display" class="form-control form-control-sm" placeholder="از تاریخ" autocomplete="off">
      <button type="button" class="calendar-btn" id="from_calendar_btn" aria-label="انتخاب تاریخ از"><i class="bi bi-calendar-event"></i></button>
      <input type="hidden" name="from" id="from" value="{{ $filters['from'] }}">
    </div>
    <div class="date-filter-group">
      <input type="text" id="to_display" class="form-control form-control-sm" placeholder="تا تاریخ" autocomplete="off">
      <button type="button" class="calendar-btn" id="to_calendar_btn" aria-label="انتخاب تاریخ تا"><i class="bi bi-calendar-range"></i></button>
      <input type="hidden" name="to" id="to" value="{{ $filters['to'] }}">
    </div>
    <div class="d-flex gap-2 ms-auto">
      <button class="btn btn-sm btn-primary d-flex align-items-center gap-1"><i class="bi bi-search"></i> اعمال</button>
      <a href="{{ route('host.reserves') }}" class="btn btn-sm btn-outline-secondary">ریست</a>
    </div>
  </form>

  <div class="reserve-table-wrapper">
    @if($bookings->count())
      <table class="reserves-table">
        <thead>
          <tr>
            <th>#</th>
            <th>اقامت‌گاه</th>
            <th>مهمان</th>
            <th>تاریخ</th>
            <th>شب‌ها</th>
            <th>نفرات</th>
            <th>مبلغ نهایی</th>
            <th>وضعیت</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bookings as $b)
            @php($nights = $b->nights)
            <tr
              data-bs-toggle="modal"
              data-bs-target="#bookingDetailModal"
              data-booking-id="{{ $b->id }}"
              data-user-name="{{ $b->user?->name }}"
              data-user-phone="{{ $b->user?->phone }}"
              data-user-national="{{ $b->user?->national_id ?? '—' }}"
              data-stay-title="{{ $b->stay?->title }}"
              data-stay-address="{{ $b->stay?->address ?? '—' }}"
              data-dates="{{ $b->start_date->format('Y-m-d') }} تا {{ $b->end_date->format('Y-m-d') }}"
              data-nights="{{ $nights }}"
              data-guests="پایه {{ $b->base_guests }} / اضافه {{ $b->extra_guests }}"
              data-price="{{ number_format($b->final_price) }} تومان"
              data-status-html="{!! $b->status_badge !!}" >
              <td data-label="#">{{ $b->id }}</td>
              <td data-label="اقامت‌گاه" class="fw-semibold text-primary">{{ Str::limit($b->stay?->title,28,'…') }}</td>
              <td data-label="مهمان">{{ $b->user?->name ?? '—' }}</td>
              <td data-label="تاریخ">{{ $b->start_date->format('Y/m/d') }}<span class="text-muted"> تا </span>{{ $b->end_date->format('Y/m/d') }}</td>
              <td data-label="شب‌ها">{{ $nights }}</td>
              <td data-label="نفرات">{{ $b->base_guests }}<span class="text-muted"> / </span>{{ $b->extra_guests }}</td>
              <td data-label="مبلغ" class="text-success fw-semibold">{{ number_format($b->final_price) }}</td>
              <td data-label="وضعیت" class="status-badge">{!! $b->status_badge !!}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="p-3">
        {{ $bookings->links() }}
      </div>
    @else
      <div class="empty-state">
        <i class="bi bi-calendar2-x"></i>
        <p class="mt-3 mb-1">هیچ رزروی یافت نشد.</p>
        <small>پس از ثبت رزرو توسط کاربران، اینجا نمایش داده می‌شود.</small>
      </div>
    @endif
  </div>

  <!-- Modal -->
  <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header modal-gradient-header">
          <h6 class="modal-title d-flex align-items-center gap-2"><i class="bi bi-info-circle"></i> جزئیات رزرو <span id="mdlBookingId" class="badge bg-light text-dark"></span></h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="guest-info-grid">
            <div class="info-box"><span class="label">نام مهمان</span><strong id="mdlGuestName">—</strong></div>
            <div class="info-box"><span class="label">شماره تماس</span><strong id="mdlGuestPhone">—</strong></div>
            <div class="info-box"><span class="label">کد ملی</span><strong id="mdlGuestNational">—</strong></div>
            <div class="info-box"><span class="label">اقامت‌گاه</span><strong id="mdlStayTitle">—</strong></div>
            <div class="info-box"><span class="label">آدرس</span><strong id="mdlStayAddress">—</strong></div>
            <div class="info-box"><span class="label">تاریخ‌ها</span><strong id="mdlDates">—</strong></div>
            <div class="info-box"><span class="label">تعداد شب</span><strong id="mdlNights">—</strong></div>
            <div class="info-box"><span class="label">نفرات</span><strong id="mdlGuests">—</strong></div>
            <div class="info-box"><span class="label">وضعیت</span><strong id="mdlStatus">—</strong></div>
            <div class="info-box"><span class="label">مبلغ نهایی</span><strong class="price-chip" id="mdlPrice"><i class="bi bi-currency-dollar"></i> —</strong></div>
          </div>
          <div class="instruction-box">
            <h6><i class="bi bi-lightbulb"></i> راهنمای انجام رزرو</h6>
            <ul class="instruction-list">
              <li><i class="bi bi-key"></i> لطفاً در زمان ورود (ساعت ثبت شده در قوانین اقامت‌گاه) کلیدها را حضوری تحویل مهمان دهید.</li>
              <li><i class="bi bi-shield-check"></i> قبل از تحویل، مدارک هویتی مهمان را با اطلاعات رزرو تطبیق دهید.</li>
              <li><i class="bi bi-door-open"></i> شرایط استفاده از فضا و امکانات حساس (مانند استخر یا باربیکیو) را شفاهی توضیح دهید.</li>
              <li><i class="bi bi-cash-coin"></i> مبلغ رزرو پس از پایان سفر و عدم ثبت شکایت، به حساب شما تسویه خواهد شد.</li>
              <li><i class="bi bi-chat-dots"></i> برای هماهنگی‌های بیشتر از طریق بخش پیام‌ها در پنل اقدام کنید.</li>
              <li><i class="bi bi-exclamation-triangle"></i> در صورت بروز خسارت، حداکثر تا ۱۲ ساعت پس از خروج مهمان آن را گزارش نمایید.</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
          <small class="text-muted">رعایت جزئیات فوق موجب تسویه سریع‌تر و تجربه بهتر مهمان می‌شود.</small>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">بستن</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.0.6/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('table.reserves-table tbody tr').forEach(function(row){
      row.addEventListener('click', function(){
        const d=row.dataset;
        document.getElementById('mdlBookingId').textContent='#'+(d.bookingId||'—');
        document.getElementById('mdlGuestName').textContent=d.userName||'—';
        document.getElementById('mdlGuestPhone').textContent=d.userPhone||'—';
        document.getElementById('mdlGuestNational').textContent=d.userNational||'—';
        document.getElementById('mdlStayTitle').textContent=d.stayTitle||'—';
        document.getElementById('mdlStayAddress').textContent=d.stayAddress||'—';
        document.getElementById('mdlDates').textContent=d.dates||'—';
        document.getElementById('mdlNights').textContent=d.nights||'—';
        document.getElementById('mdlGuests').textContent=d.guests||'—';
        document.getElementById('mdlStatus').innerHTML=d.statusHtml||'—';
        document.getElementById('mdlPrice').innerHTML='<i class="bi bi-currency-dollar"></i> '+(d.price||'—');
      });
    });
  });
</script>
<script>
  // Persian (Jalali) datepickers for filters converting to Gregorian hidden inputs
  $(function(){
    function toEnglishDigits(str){return (str+'').replace(/[۰-۹]/g,d=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(d))}
    // Init pickers and keep instance references
    var fromPickerInstance = $('#from_display').persianDatepicker({
      format:'YYYY/MM/DD',initialValue:false,autoClose:true,
      onSelect:function(unix){
        const g = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        document.getElementById('from').value = toEnglishDigits(g);
      }
    });
    var toPickerInstance = $('#to_display').persianDatepicker({
      format:'YYYY/MM/DD',initialValue:false,autoClose:true,
      onSelect:function(unix){
        const g = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        document.getElementById('to').value = toEnglishDigits(g);
      }
    });
    // Open on icon click (use instance.show if available; fallback focus)
    $('#from_calendar_btn').on('click', function(){
      try { if(fromPickerInstance && typeof fromPickerInstance.show==='function'){ fromPickerInstance.show(); return; } } catch(e) {}
      $('#from_display').trigger('focus');
    });
    $('#to_calendar_btn').on('click', function(){
      try { if(toPickerInstance && typeof toPickerInstance.show==='function'){ toPickerInstance.show(); return; } } catch(e) {}
      $('#to_display').trigger('focus');
    });
    // Also open when input itself clicked (in case readonly removed)
    $('#from_display').on('click', function(){ $('#from_calendar_btn').click(); });
    $('#to_display').on('click', function(){ $('#to_calendar_btn').click(); });
    // Pre-fill display fields if query has gregorian values
    const fromVal='{{ $filters['from'] }}';
    const toVal='{{ $filters['to'] }}';
    function gToPersian(g){
      if(!g) return '';
      const parts=g.split('-').map(Number);
      const unix=new Date(parts[0],parts[1]-1,parts[2]).getTime();
      return new persianDate(unix).format('YYYY/MM/DD');
    }
    if(fromVal){ $('#from_display').val(gToPersian(fromVal)); }
    if(toVal){ $('#to_display').val(gToPersian(toVal)); }
  });
</script>
@endpush
