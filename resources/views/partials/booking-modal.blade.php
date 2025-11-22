<!-- رزرو -->
<div class="modal fade" id="bookingModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content booking-modal-neo">
      <div class="modal-header booking-gradient">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bi bi-calendar2-check pulse-icon"></i>
          رزرو اقامت‌گاه
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <!-- نوار مراحل -->
        <div class="booking-steps mb-4">
          <div class="step active" data-step="phone"><span>۱</span><small>موبایل</small></div>
          <div class="step" data-step="otp"><span>۲</span><small>کد تایید</small></div>
          <div class="step" data-step="dates"><span>۳</span><small>تاریخ‌ها</small></div>
          <div class="step" data-step="guests"><span>۴</span><small>نفرات</small></div>
          <div class="step" data-step="info"><span>۵</span><small>مشخصات</small></div>
          <div class="step" data-step="review"><span>۶</span><small>بررسی</small></div>
          <div class="step" data-step="result"><span>۷</span><small>پرداخت</small></div>
        </div>

        <!-- مرحله ۱ -->
        <div id="step-phone">
          <h6 class="fw-bold mb-3"><i class="bi bi-phone-vibrate text-primary me-1"></i> شماره موبایل</h6>
          <div class="mb-3">
            <label class="form-label">شماره موبایل</label>
            <input type="text" id="phoneInput" class="form-control" placeholder="09123456789">
            <div id="phoneError" class="invalid-feedback"></div>
          </div>
          <button class="btn btn-primary w-100" id="sendOtpBtn">ارسال کد تأیید <i class="bi bi-send ms-1"></i></button>
        </div>

        <!-- مرحله ۲ -->
        <div id="step-otp" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock text-warning me-1"></i> تأیید شماره</h6>
          <div class="mb-3 text-center">
            <input type="text" id="otpCode" class="form-control text-center fs-5 fw-bold" maxlength="6" placeholder="کد ۶ رقمی">
            <div id="otpError" class="invalid-feedback text-center"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span id="otpTimer" class="badge bg-light text-dark"></span>
            <button id="resendOtpBtn" class="btn btn-outline-secondary btn-sm" disabled>ارسال مجدد <i class="bi bi-arrow-clockwise"></i></button>
          </div>
          <button class="btn btn-success w-100 mt-3" id="verifyOtpBtn">بررسی کد <i class="bi bi-check-circle ms-1"></i></button>
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
              </div>
              <div class="col-md-6">
                <label class="form-label">تاریخ پایان</label>
                <input type="text" id="endDateDisplay" class="form-control" placeholder="انتخاب">
                <input type="hidden" name="end_date" id="endDate">
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
                  <input type="number" min="1" max="{{ $stay->base_capacity }}" value="1" class="form-control" id="baseGuestsInput">
                </div>
                <div class="col-md-6">
                  <label class="form-label">
                    نفرات اضافه (حداکثر @faNum($stay->extra_capacity))
                  </label>
                  <input type="number" min="0" max="{{ $stay->extra_capacity }}" value="0" class="form-control" id="extraGuestsInput">
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

        <!-- مرحله ۵: مشخصات -->
        <div id="step-info" class="d-none">
          <h6 class="fw-bold mb-3"><i class="bi bi-person-badge text-secondary me-1"></i> اطلاعات شخصی</h6>
          <form id="infoForm">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">نام</label>
                <input type="text" class="form-control" id="firstNameInput">
              </div>
              <div class="col-md-4">
                <label class="form-label">نام خانوادگی</label>
                <input type="text" class="form-control" id="lastNameInput">
              </div>
              <div class="col-md-4">
                <label class="form-label">کد ملی</label>
                <input type="text" class="form-control" id="nationalIdInput">
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">ادامه</button>
          </form>
        </div>

        <!-- مرحله ۶: بررسی -->
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
          <button id="confirmBookingBtn" class="btn btn-success w-100 mt-3">
            ثبت رزرو و محاسبه نهایی <i class="bi bi-check2-circle ms-1"></i>
          </button>
        </div>

        <!-- مرحله ۷: نتیجه -->
        <div id="step-result" class="d-none text-center">
          <i class="bi bi-gift-fill text-success fs-1 bounce-icon"></i>
          <h5 class="mt-3 fw-bold" id="discountMsg"></h5>
          <p class="fs-5 text-primary mt-2">مبلغ قابل پرداخت: <span id="finalPrice"></span> تومان</p>
          <button id="goToPayment" class="btn btn-gradient-pay w-100 mt-3">
            پرداخت <i class="bi bi-credit-card ms-1"></i>
          </button>
        </div>

        <!-- لودر -->
        <div id="loadingOverlay" class="booking-loading d-none">
          <div class="spinner-border text-primary"></div>
          <p class="mt-3 fw-semibold text-primary">در حال پردازش...</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-modal.css') }}">
<style>
.booking-modal-neo{border-radius:1.25rem;overflow:hidden;position:relative}
.booking-gradient{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
.booking-steps{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:space-between}
.booking-steps .step{flex:1;min-width:75px;background:#f1f5f9;border-radius:.75rem;padding:.4rem .35rem;text-align:center;position:relative;font-size:.7rem;cursor:default;transition:.25s}
.booking-steps .step span{display:inline-flex;justify-content:center;align-items:center;width:28px;height:28px;border-radius:50%;background:#e2e8f0;font-weight:600;margin-bottom:.25rem;font-size:.8rem}
.booking-steps .step.active{background:#2563eb;color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.25)}
.booking-steps .step.active span{background:#fff;color:#2563eb}
.booking-loading{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;align-items:center;background:rgba(255,255,255,.85);backdrop-filter:blur(4px);z-index:50}
.pulse-icon{animation:pulse 1.4s infinite}
.bounce-icon{animation:bounce 1.2s infinite}
@keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.15)}100%{transform:scale(1)}}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
.btn-gradient-pay{background:linear-gradient(135deg,#16a34a,#0d8236);color:#fff}
.btn-gradient-pay:hover{background:linear-gradient(135deg,#0d8236,#16a34a)}
.booking-summary-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:1rem}
.pricing-preview div{display:flex;justify-content:space-between}
@media(max-width:768px){.booking-steps .step small{display:none}}
</style>
@endpush

@push('scripts')
<script>
// نیاز به persianDatepicker و jQuery
const stepsOrder=['phone','otp','dates','guests','info','review','result'];
function goStep(name){
  stepsOrder.forEach(s=>{
    document.getElementById('step-'+s)?.classList.add('d-none');
    document.querySelector('.booking-steps .step[data-step="'+s+'"]')?.classList.remove('active');
  });
  document.getElementById('step-'+name)?.classList.remove('d-none');
  document.querySelector('.booking-steps .step[data-step="'+name+'"]')?.classList.add('active');
}
function showLoader(show=true){
  document.getElementById('loadingOverlay').classList.toggle('d-none',!show);
}

/* مرحله ۱ ارسال OTP (شبه) */
$('#sendOtpBtn').on('click',function(){
  const phone=$('#phoneInput').val().trim();
  if(!/^09\d{9}$/.test(phone)){ $('#phoneError').text('شماره معتبر نیست.').show(); $('#phoneInput').addClass('is-invalid'); return; }
  $('#phoneInput').removeClass('is-invalid').addClass('is-valid'); $('#phoneError').hide();
  // AJAX ارسال کد (فرضی)
  goStep('otp'); startOtpTimer();
});

/* مرحله ۲ تایید OTP (شبه) */
$('#verifyOtpBtn').on('click',function(){
  const code=$('#otpCode').val().trim();
  if(code.length!==6){ $('#otpError').text('کد ۶ رقمی وارد کنید').show(); return; }
  $('#otpError').hide();
  goStep('dates');
});

/* تاریخ‌ها */
$('#datesForm').on('submit',function(e){
  e.preventDefault();
  if(!$('#startDate').val() || !$('#endDate').val()){ alert('تاریخ‌ها را انتخاب کنید'); return; }
  goStep('guests');
});

/* نفرات */
function updateGuestsPreview(){
  const base=parseInt($('#baseGuestsInput').val()||0);
  const extra=parseInt($('#extraGuestsInput').val()||0);
  $('#liveGuestsSummary').text(`پایه: ${base} نفر | اضافه: ${extra} نفر`);
}
$('#baseGuestsInput,#extraGuestsInput').on('input',updateGuestsPreview);
$('#guestsForm').on('submit',function(e){
  e.preventDefault();
  updateGuestsPreview();
  goStep('info');
});

/* مشخصات */
$('#infoForm').on('submit',function(e){
  e.preventDefault();
  if(!$('#firstNameInput').val().trim()||!$('#lastNameInput').val().trim()||!$('#nationalIdInput').val().trim()){ alert('همه فیلدها را کامل کنید'); return; }
  fillReview(); goStep('review');
});

/* مرور و محاسبه اولیه (سمت کلاینت تقریبی) */
function fillReview(){
  const sd=$('#startDate').val(), ed=$('#endDate').val();
  const start=new Date(sd), end=new Date(ed);
  const nights=Math.max(1,(end-start)/(1000*60*60*24));
  const baseGuests=parseInt($('#baseGuestsInput').val());
  const extraGuests=parseInt($('#extraGuestsInput').val());
  const basePer={{ $stay->price_per_person }};
  const extraPer={{ (int)($stay->extra_person_price ?? 0) }};
  const basePrice=basePer*baseGuests*nights;
  const extraCost=extraPer*extraGuests*nights;
  const peak={{ \App\Models\PeakPeriod::isNowPeak()?'true':'false' }};
  const stayDiscPercent= peak ? {{ $stay->max_discount_peak }} : {{ $stay->max_discount_normal }};
  const stayDiscAmount=(basePrice+extraCost)*(stayDiscPercent/100);
  // سازمانی داخل کنترلر محاسبه می‌شود => اینجا صفر
  $('#revDates').text(sd+' تا '+ed);
  $('#revGuests').text(`پایه ${baseGuests} / اضافه ${extraGuests}`);
  $('#revName').text($('#firstNameInput').val()+' '+$('#lastNameInput').val());
  $('#revNights').text(nights);
  $('#revBase').text(basePrice.toLocaleString());
  $('#revExtra').text(extraCost.toLocaleString());
  $('#revStayDiscount').text(stayDiscAmount.toLocaleString()+' ('+stayDiscPercent+'%)');
  $('#revOrgDiscount').text('— محاسبه پس از ارسال');
  $('#revFinal').text((basePrice+extraCost-stayDiscAmount).toLocaleString());
}

/* ثبت رزرو نهایی */
$('#confirmBookingBtn').on('click',function(){
  showLoader(true);
  $.ajax({
    url:"{{ route('bookings.store') }}",
    method:'POST',
    data:{
      _token:"{{ csrf_token() }}",
      stay_id:"{{ $stay->id }}",
      phone:$('#phoneInput').val(),
      first_name:$('#firstNameInput').val(),
      last_name:$('#lastNameInput').val(),
      national_id:$('#nationalIdInput').val(),
      start_date:$('#startDate').val(),
      end_date:$('#endDate').val(),
      base_guests:$('#baseGuestsInput').val(),
      extra_guests:$('#extraGuestsInput').val()
    },
    success:function(r){
      showLoader(false);
      if(r.success){
        $('#discountMsg').text(r.message||'رزرو ثبت شد');
        $('#finalPrice').text(r.final_price);
        goStep('result');
      }else{
        alert(r.message||'خطا');
      }
    },
    error:function(){
      showLoader(false);
      alert('خطای ارتباط با سرور');
    }
  });
});

/* پرداخت (شبه) */
$('#goToPayment').on('click',function(){
  alert('انتقال به درگاه پرداخت (پیاده‌سازی نشده)');
});

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

