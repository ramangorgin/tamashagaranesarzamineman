<!-- رزرو -->
<div
  class="modal fade"
  id="bookingModal"
  tabindex="-1"
  data-bs-backdrop="false"
  data-bs-keyboard="true"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content booking-modal-neo">
      <div class="modal-header booking-gradient">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bi bi-calendar2-check pulse-icon"></i>
          رزرو اقامت‌گاه
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <div class="booking-layout">
          <!-- نوار مراحل عمودی -->
          <aside class="steps-pane">
            <div class="steps-header d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-list-check text-white fs-5"></i>
              <span class="fw-semibold text-white small">مراحل رزرو</span>
            </div>
            <div class="steps-wrapper">
              <div class="step active" data-step="phoneOtp"><span>۱</span><small>موبایل/OTP</small></div>
              <div class="step" data-step="dates"><span>۲</span><small>تاریخ‌ها</small></div>
              <div class="step" data-step="guests"><span>۳</span><small>نفرات</small></div>
              <div class="step" data-step="info"><span>۴</span><small>مشخصات</small></div>
              <div class="step" data-step="review"><span>۵</span><small>محاسبه</small></div>
            </div>
          </aside>
          <div class="content-pane p-4">
        <!-- مرحله ۱: موبایل + OTP -->
        <div id="step-phoneOtp">
          <h6 class="fw-bold mb-3"><i class="bi bi-phone-vibrate text-primary me-1"></i> موبایل و تایید</h6>
          <div class="mb-3">
            <label class="form-label">شماره موبایل</label>
            <input type="text" id="phoneInput" class="form-control" placeholder="09123456789" autocomplete="tel">
            <div id="phoneError" class="invalid-feedback"></div>
          </div>
          <div id="otpSection" class="d-none">
            <div class="mb-3 text-center">
              <input type="text" id="otpInput" class="form-control text-center fw-bold" maxlength="6" placeholder="کد ۶ رقمی">
              <div id="otpError" class="invalid-feedback text-center"></div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span id="otpTimer" class="badge bg-light text-dark"></span>
              <button id="resendOtpBtn" class="btn btn-outline-secondary btn-sm" disabled>ارسال مجدد <i class="bi bi-arrow-clockwise"></i></button>
            </div>
            <button class="btn btn-success w-100" id="verifyOtpBtn">تایید کد <i class="bi bi-check-circle ms-1"></i></button>
          </div>
          <button class="btn btn-primary w-100" id="sendOtpBtn">ارسال کد تأیید <i class="bi bi-send ms-1"></i></button>
        </div>

        <!-- مرحله ۳: تاریخ‌ها -->
        <div id="step-dates" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-calendar-range text-danger me-1"></i> انتخاب تاریخ‌ها (شمسی)</h6>
          <form id="datesForm">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">تاریخ شروع</label>
                <input type="text" id="startDateDisplay" class="form-control" placeholder="انتخاب">
                <input type="hidden" name="start_date" id="startDate">
                <div id="startDateError" class="invalid-feedback"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label">تاریخ پایان</label>
                <input type="text" id="endDateDisplay" class="form-control" placeholder="انتخاب">
                <input type="hidden" name="end_date" id="endDate">
                <div id="endDateError" class="invalid-feedback"></div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">ثبت تاریخ‌ها</button>
          </form>
        </div>

        <!-- مرحله ۴: نفرات -->
        <div id="step-guests" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-people-fill text-info me-1"></i> تعداد نفرات</h6>
            <form id="guestsForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">
                    نفرات پایه (حداکثر @faNum($stay->base_capacity))
                  </label>
                  <input type="number" min="1" max="{{ $stay->base_capacity }}" value="1" class="form-control" id="base_guestsInput">
                  <div id="base_guestsError" class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">
                    نفرات اضافه (حداکثر @faNum($stay->extra_capacity))
                  </label>
                  <input type="number" min="0" max="{{ $stay->extra_capacity }}" value="0" class="form-control" id="extra_guestsInput">
                  <div id="extra_guestsError" class="invalid-feedback"></div>
                </div>
              </div>
              <div class="mt-3 p-3 rounded bg-light small">
                <div>قیمت هر نفر پایه: @faNum(number_format($stay->price_per_person)) تومان</div>
                <div>قیمت هر نفر اضافه: @faNum(number_format($stay->extra_person_price ?? 0)) تومان</div>
                <div id="liveGuestsSummary" class="fw-bold text-primary mt-2"></div>
              </div>
              <button type="submit" class="btn btn-primary w-100 mt-3">ثبت ظرفیت</button>
            </form>
        </div>

        <!-- مرحله ۴: مشخصات -->
        <div id="step-info" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-person-badge text-secondary me-1"></i> اطلاعات شخصی</h6>
          <form id="infoForm">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">نام</label>
                <input type="text" class="form-control" id="first_nameInput">
                <div id="first_nameError" class="invalid-feedback"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label">نام خانوادگی</label>
                <input type="text" class="form-control" id="last_nameInput">
                <div id="last_nameError" class="invalid-feedback"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label">کد ملی</label>
                <input type="text" class="form-control" id="national_idInput">
                <div id="national_idError" class="invalid-feedback"></div>
              </div>
              <div class="col-md-4">
                 <label class="form-label">شماره موبایل (تایید شده)</label>
                 <input type="text" class="form-control" id="infoPhoneInput" readonly>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">محاسبه قیمت</button>
          </form>
        </div>

        <!-- مرحله ۵: بررسی و محاسبه نهایی -->
        <div id="step-review" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-search-heart text-warning me-1"></i> بررسی و محاسبه مبلغ</h6>
          <div class="rounded shadow-sm p-3 booking-summary-box">
            <ul class="list-unstyled mb-2 small">
              <li><i class="bi bi-calendar-event text-primary"></i> <span id="revDates"></span></li>
              <li><i class="bi bi-people text-info"></i> <span id="revGuests"></span></li>
              <li><i class="bi bi-person text-secondary"></i> <span id="revName"></span></li>
            </ul>
            <div class="pricing-preview small">
              <div><span class="text-muted">شب‌ها:</span> <span id="revNights"></span></div>
              <div><span class="text-muted">مبلغ پایه:</span> <span id="revBase"></span></div>
              <div><span class="text-muted">مبلغ اضافه:</span> <span id="revExtra"></span></div>
              <div><span class="text-muted">تخفیف اقامت‌گاه:</span> <span id="revStayDiscount"></span></div>
              <div><span class="text-muted">تخفیف سازمانی:</span> <span id="revOrgDiscount"></span></div>
              <hr class="my-2">
              <div class="fw-bold fs-5 text-success">مبلغ نهایی: <span id="revFinal"></span> تومان</div>
            </div>
          </div>
          <button id="goToPayment" class="btn btn-success w-100 mt-3">رفتن به پرداخت <i class="bi bi-credit-card ms-1"></i></button>
        </div>

        <!-- حذف مرحله نتیجه: پرداخت مستقیم پس از بررسی -->

        <!-- لودر -->
        <div id="loadingOverlay" class="booking-loading d-none">
          <div class="spinner-border text-primary"></div>
          <p class="mt-3 fw-semibold text-primary">در حال پردازش...</p>
        </div>
          </div> <!-- /content-pane -->
        </div> <!-- /booking-layout -->
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-modal.css') }}">
<style>
.invalid-feedback{display:block;width:100%;margin-top:.25rem;font-size:.875em;color:#dc3545}
.form-control.is-invalid{border-color:#dc3545}
.booking-modal-neo{border-radius:1.25rem;overflow:hidden;position:relative}
.booking-gradient{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
.booking-layout{display:flex;min-height:600px;background:#fff}
.steps-pane{width:200px;background:linear-gradient(180deg,#1e3a8a,#1d4ed8);padding:1.25rem;display:flex;flex-direction:column}
.steps-header{border-bottom:1px solid rgba(255,255,255,.15);padding-bottom:.5rem;margin-bottom:.75rem}
.steps-wrapper{display:flex;flex-direction:column;gap:.6rem}
.steps-wrapper .step{background:rgba(255,255,255,.15);color:#f1f5f9;border-radius:.65rem;padding:.55rem .4rem;display:flex;align-items:center;gap:.55rem;font-size:.75rem;cursor:pointer;position:relative;transition:.25s}
.steps-wrapper .step span{flex:0 0 30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(255,255,255,.35);font-weight:600;font-size:.8rem}
.steps-wrapper .step.active{background:#fff;color:#1d4ed8;box-shadow:0 4px 12px rgba(0,0,0,.15)}
.steps-wrapper .step.active span{background:#1d4ed8;color:#fff}
.steps-wrapper .step.completed{background:rgba(255,255,255,.35)}
.steps-wrapper .step.completed span{background:#10b981;color:#fff}
.content-pane{flex:1;overflow-y:auto}
.content-pane h6{display:flex;align-items:center}
.content-pane::-webkit-scrollbar{width:8px}
.content-pane::-webkit-scrollbar-track{background:#f1f5f9}
.content-pane::-webkit-scrollbar-thumb{background:#94a3b8;border-radius:4px}
.booking-loading{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;align-items:center;background:rgba(255,255,255,.85);backdrop-filter:blur(4px);z-index:50}
.booking-loading.d-none{display:none!important}
#bookingModal .modal-dialog{z-index:1060;max-width:920px}
.modal-backdrop.show{opacity:.4}
.pulse-icon{animation:pulse 1.4s infinite}
.bounce-icon{animation:bounce 1.2s infinite}
@keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.15)}100%{transform:scale(1)}}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
.btn-gradient-pay{background:linear-gradient(135deg,#16a34a,#0d8236);color:#fff}
.btn-gradient-pay:hover{background:linear-gradient(135deg,#0d8236,#16a34a)}
.booking-summary-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:1rem}
.pricing-preview div{display:flex;justify-content:space-between}
@media(max-width:992px){.booking-layout{flex-direction:column}.steps-pane{width:100%;flex-direction:row;align-items:center}.steps-wrapper{flex-direction:row;flex-wrap:wrap}.steps-wrapper .step{flex:1 1 70px;justify-content:center}}
@media(max-width:576px){.steps-wrapper .step small{display:none}}
</style>
@endpush

@push('scripts')
<script>
// Scope all selectors to the booking modal to avoid ID collisions with other modals
const bookingModalEl = document.getElementById('bookingModal');
const q  = (sel) => bookingModalEl?.querySelector(sel);
const qa = (sel) => Array.from(bookingModalEl?.querySelectorAll(sel) || []);

// Helpers for error display (scoped)
function clearErrors() {
  qa('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  qa('.invalid-feedback').forEach(el => el.textContent = '');
}

function displayErrors(errors) {
  clearErrors();
  for (const field in errors) {
    const input = q('#' + field + 'Input'); // e.g., #phoneInput
    const errorDiv = q('#' + field + 'Error'); // e.g., #phoneError
    if (input) {
      input.classList.add('is-invalid');
    }
    if (errorDiv) {
      errorDiv.textContent = errors[field][0];
    }
  }
}

// نیاز به persianDatepicker و jQuery
const stepsOrder=['phoneOtp','dates','guests','info','review'];
let currentStep='phoneOtp';
function goStep(name){
  clearErrors(); // Clear errors when changing steps
  stepsOrder.forEach((s,i)=>{
    q('#step-'+s)?.classList.add('d-none');
    const el=q('.steps-pane .step[data-step="'+s+'"]');
    el?.classList.remove('active');
    // Mark completed if before target step
    if(stepsOrder.indexOf(name)>i) el?.classList.add('completed');
  });
  q('#step-'+name)?.classList.remove('d-none');
  const current=q('.steps-pane .step[data-step="'+name+'"]');
  current?.classList.add('active');
  currentStep=name;
}
function showLoader(show=true){
  q('#loadingOverlay')?.classList.toggle('d-none',!show);
}

// Enable clicking previous completed steps
document.addEventListener('click',function(e){
  const target=e.target.closest('.steps-pane .step');
  if(!target) return;
  const stepName=target.getAttribute('data-step');
  // Allow navigation forward only one step ahead, or any previous/completed/current
  const targetIndex=stepsOrder.indexOf(stepName);
  const currentIndex=stepsOrder.indexOf(currentStep);
  if(targetIndex<=currentIndex+1){
    goStep(stepName);
  }
});

/* مرحله ۱ ارسال OTP (شبه) */
function toFaDigits(str){return (str+'').replace(/[0-9]/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]);}
let verifiedPhone=null;
function handleSendOtp(){
  const phone=(q('#phoneInput')?.value || '').trim();
  showLoader(true);
  fetch("{{ route('booking.otp.send') }}",{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'Accept':'application/json',
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body:JSON.stringify({phone})
  }).then(async r=>{
      let data; let isJson = (r.headers.get('content-type')||'').includes('application/json');
      try{ data = isJson ? await r.json() : {message: await r.text()}; }catch(e){ data={message:'واکنش نامعتبر سرور'}; }
      return {ok:r.ok,status:r.status,body:data};
  }).then(res=>{
      showLoader(false);
      if(res.ok && res.body.success){
        q('#otpSection')?.classList.remove('d-none');
        startOtpTimer();
        verifiedPhone=phone;
      }else{
        const defaultMessage = 'خطا در ارسال کد';
        if (res.status === 422 && res.body.errors) {
          displayErrors(res.body.errors);
        } else {
          alert(res.body.message || defaultMessage);
        }
      }
  }).catch(err=>{
      showLoader(false);
      console.error('OTP send error',err);
      alert('خطای ارتباط با سرور');
  });
}
// Bind with jQuery
$(document).on('click', '#sendOtpBtn', function(e) {
    e.preventDefault();
    clearErrors();
    handleSendOtp();
});

/* مرحله ۲ تایید OTP (شبه) */
function handleVerifyOtp(){
  const code=(q('#otpInput')?.value || '').trim();
  showLoader(true);
  fetch("{{ route('booking.otp.verify') }}",{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'Accept':'application/json',
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body:JSON.stringify({phone:verifiedPhone,code})
  }).then(async r=>{
      let data; let isJson=(r.headers.get('content-type')||'').includes('application/json');
      try{ data=isJson? await r.json(): {message: await r.text()}; }catch(e){ data={message:'واکنش نامعتبر سرور'}; }
      return {ok:r.ok,status:r.status,body:data};
  }).then(res=>{
      showLoader(false);
      if(res.ok && res.body.success){
        if(q('#infoPhoneInput')) q('#infoPhoneInput').value=verifiedPhone;
        goStep('dates');
      }else{
        const defaultMessage = 'کد اشتباه است';
        if (res.status === 422 && res.body.errors) {
            // Custom mapping for otp code
            displayErrors({ otp: res.body.errors.code || [res.body.message] });
        } else {
            alert(res.body.message || defaultMessage);
        }
      }
  }).catch(err=>{
      showLoader(false);
      console.error('OTP verify error',err);
      alert('خطای ارتباط با سرور');
  });
}
// Bind with jQuery
$(document).on('click', '#verifyOtpBtn', function(e) {
    e.preventDefault();
    clearErrors();
    handleVerifyOtp();
});

/* تاریخ‌ها */
$('#datesForm').on('submit',function(e){
  e.preventDefault();
  clearErrors();
  // const sd=$('#startDate').val();
  // const ed=$('#endDate').val();
  // if(!sd || !ed){
  //   displayErrors({ startDate: ['تاریخ شروع را انتخاب کنید.'], endDate: ['تاریخ پایان را انتخاب کنید.'] });
  //   return;
  // }
  // if(new Date(ed)<=new Date(sd)){
  //   displayErrors({ endDate: ['تاریخ پایان باید بعد از شروع باشد.'] });
  //   return;
  // }
  goStep('guests');
});

/* نفرات */
function updateGuestsPreview(){
  const base=parseInt($('#base_guestsInput').val()||0);
  const extra=parseInt($('#extra_guestsInput').val()||0);
  $('#liveGuestsSummary').text(`پایه: ${toFaDigits(base)} نفر | اضافه: ${toFaDigits(extra)} نفر`);
  const maxBase={{ $stay->base_capacity }};
  if(base===maxBase){ $('#extra_guestsInput').prop('disabled',false); } else { $('#extra_guestsInput').prop('disabled',true).val(0); }
}
$('#base_guestsInput,#extra_guestsInput').on('input',updateGuestsPreview);
// ابتدایی: فیلد اضافه غیر فعال
$('#extra_guestsInput').prop('disabled',true);
$('#guestsForm').on('submit',function(e){
  e.preventDefault();
  clearErrors();
  updateGuestsPreview();
  goStep('info');
});

/* مشخصات */
$('#infoForm').on('submit',function(e){
  e.preventDefault();
  clearErrors();
  const firstName = $('#first_nameInput').val().trim();
  const lastName = $('#last_nameInput').val().trim();
  const nationalId = $('#national_idInput').val().trim();
  // let errors = {};
  // if(!firstName) errors.first_name = ['نام الزامی است.'];
  // if(!lastName) errors.last_name = ['نام خانوادگی الزامی است.'];
  // if(!nationalId) errors.national_id = ['کد ملی الزامی است.'];

  // if(Object.keys(errors).length > 0) {
  //   displayErrors(errors);
  //   return;
  // }

  // پیش‌نمایش قیمت از سرور
  showLoader(true);
  const payload={
    _token:"{{ csrf_token() }}",
    stay_id:"{{ $stay->id }}",
    phone:verifiedPhone,
    first_name:firstName,
    last_name:lastName,
    national_id:nationalId,
    start_date:$('#startDate').val(),
    end_date:$('#endDate').val(),
    base_guests:$('#base_guestsInput').val(),
    extra_guests:$('#extra_guestsInput').val()
  };
  fetch("{{ route('bookings.preview') }}",{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify(payload)})
    .then(r=>r.json().then(j=>({ok:r.ok,body:j,status:r.status})))
    .then(res=>{
      showLoader(false);
      if(res.ok && res.body.success){
        fillReview(res.body);
        goStep('review');
      } else {
        const defaultMessage = 'خطا در محاسبه';
        if (res.status === 422 && res.body.errors) {
          displayErrors(res.body.errors);
        } else {
          alert(res.body.message || defaultMessage);
        }
      }
    }).catch(()=>{showLoader(false);alert('خطای ارتباط با سرور')});
});

/* مرور و محاسبه اولیه (سمت کلاینت تقریبی) */
function fillReview(data){
  const sd=$('#startDate').val(), ed=$('#endDate').val();
  const baseGuests=parseInt($('#base_guestsInput').val());
  const extraGuests=parseInt($('#extra_guestsInput').val());
  $('#revDates').text(toFaDigits(sd)+' تا '+toFaDigits(ed));
  $('#revGuests').text(`پایه ${toFaDigits(baseGuests)} / اضافه ${toFaDigits(extraGuests)}`);
  $('#revName').text($('#first_nameInput').val()+' '+$('#last_nameInput').val());
  $('#revNights').text(toFaDigits(data.nights));
  $('#revBase').text(toFaDigits(data.base_price.toLocaleString()));
  $('#revExtra').text(toFaDigits(data.extra_cost.toLocaleString()));
  $('#revStayDiscount').text(toFaDigits(data.stay_discount_amount.toLocaleString())+` (${toFaDigits(data.stay_discount_percent)}%)`);
  if(data.org_discount_percent>0){
    $('#revOrgDiscount').text(toFaDigits(data.org_discount_amount.toLocaleString())+` (${toFaDigits(data.org_discount_percent)}%)`);
  }else{
    $('#revOrgDiscount').text('—');
  }
  $('#revFinal').text(toFaDigits(data.final_price.toLocaleString()));
}

/* ثبت رزرو نهایی */
$('#goToPayment').on('click',function(){
  showLoader(true);
  const payload={
    _token:"{{ csrf_token() }}",
    stay_id:"{{ $stay->id }}",
    phone:verifiedPhone,
    first_name:$('#first_nameInput').val(),
    last_name:$('#last_nameInput').val(),
    national_id:$('#national_idInput').val(),
    start_date:$('#startDate').val(),
    end_date:$('#endDate').val(),
    base_guests:$('#base_guestsInput').val(),
    extra_guests:$('#extra_guestsInput').val()
  };
  fetch("{{ route('bookings.store') }}",{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify(payload)})
    .then(r=>r.json().then(j=>({ok:r.ok,body:j,status:r.status})))
    .then(res=>{
      showLoader(false);
      if(res.ok && res.body.success){
        // انتقال به صفحه پرداخت آزمایشی (فعلی)
        window.location.href='{{ url('/payment/test') }}/'+res.body.booking_id;
      }else{ alert(res.body.message||'خطا در ثبت رزرو'); }
    }).catch(()=>{showLoader(false);alert('خطای ارتباط با سرور')});
});

/* پرداخت (شبه) */
// حذف رویداد قدیمی پرداخت

/* OTP Timer (شبه) */
let otpSeconds=120, otpInterval=null;
function startOtpTimer(){
  otpSeconds=120;
  $('#resendOtpBtn').prop('disabled',true);
  updateOtpLabel();
  otpInterval=setInterval(()=>{
    otpSeconds--;
    updateOtpLabel();
    if(otpSeconds<=0){
      clearInterval(otpInterval);
      $('#resendOtpBtn').prop('disabled',false);
    }
  },1000);
}
function updateOtpLabel(){ $('#otpTimer').text('انقضا: '+otpSeconds+' ثانیه'); }

/* Persian Date Pickers */
$(function(){
  if(typeof $.fn.persianDatepicker!=='undefined'){
    $('#startDateDisplay').persianDatepicker({
      format:'YYYY/MM/DD', autoClose:true,
      onSelect:function(unix){
        const g=new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        $('#startDate').val(g);
      }
    });
    $('#endDateDisplay').persianDatepicker({
      format:'YYYY/MM/DD', autoClose:true,
      onSelect:function(unix){
        const g=new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        $('#endDate').val(g);
      }
    });
  }
});
</script>
@endpush

