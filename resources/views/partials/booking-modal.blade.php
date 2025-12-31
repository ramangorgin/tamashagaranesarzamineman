<!-- رزرو اقامت‌گاه -->
<div
  class="modal fade"
  id="bookingModal"
  tabindex="-1"
  data-bs-backdrop="static"
  data-bs-keyboard="false"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content booking-modal-content">
      <div class="modal-header booking-header">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bi bi-calendar2-check"></i>
          <span>رزرو اقامت‌گاه</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="بستن"></button>
      </div>

      <div class="modal-body p-0 position-relative">
        <!-- Stepper -->
        <div class="booking-stepper">
          <div class="stepper-container">
            <div class="stepper-step" data-step="1">
              <div class="stepper-circle">
                <span class="stepper-number">1</span>
                <i class="bi bi-check-lg stepper-check d-none"></i>
              </div>
              <span class="stepper-label">تاریخ و نفرات</span>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-step" data-step="2">
              <div class="stepper-circle">
                <span class="stepper-number">2</span>
                <i class="bi bi-check-lg stepper-check d-none"></i>
              </div>
              <span class="stepper-label">تایید و اطلاعات</span>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-step" data-step="3">
              <div class="stepper-circle">
                <span class="stepper-number">3</span>
                <i class="bi bi-check-lg stepper-check d-none"></i>
              </div>
              <span class="stepper-label">بررسی و پرداخت</span>
            </div>
          </div>
        </div>

        <!-- Content Area -->
        <div class="booking-content">
          <!-- Step 1: Dates & Guests -->
          <div id="step-1" class="booking-step active">
            <div class="step-header">
              <h6 class="step-title">
                <i class="bi bi-calendar-range text-primary"></i>
                انتخاب تاریخ و تعداد نفرات
              </h6>
              <p class="step-subtitle">تاریخ ورود، خروج و تعداد مهمانان را مشخص کنید</p>
            </div>

            <form id="datesGuestsForm" class="booking-form">
              <!-- Dates Section -->
              <div class="form-section">
                <h6 class="section-title">تاریخ‌ها</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">تاریخ ورود (شمسی)</label>
                    <input type="text" id="startDateDisplay" data-jdp class="form-control" placeholder="انتخاب تاریخ" required>
                    <input type="hidden" name="start_date" id="startDate">
                    <div id="startDateError" class="invalid-feedback"></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">تاریخ خروج (شمسی)</label>
                    <input type="text" id="endDateDisplay" data-jdp class="form-control" placeholder="انتخاب تاریخ" required>
                    <input type="hidden" name="end_date" id="endDate">
                    <div id="endDateError" class="invalid-feedback"></div>
                  </div>
                </div>
              </div>


              <!-- Guests Section -->
              <div class="form-section">
                <h6 class="section-title">تعداد نفرات</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">
                      نفرات پایه
                      <span class="text-muted small">(حداکثر {{ number_format($stay->base_capacity) }})</span>
                    </label>
                    <input type="number" min="1" max="{{ $stay->base_capacity }}" value="1" class="form-control" id="base_guestsInput" required>
                    <div id="base_guestsError" class="invalid-feedback"></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">
                      نفرات اضافه
                      <span class="text-muted small">(حداکثر {{ number_format($stay->extra_capacity) }})</span>
                    </label>
                    <input type="number" min="0" max="{{ $stay->extra_capacity }}" value="0" class="form-control" id="extra_guestsInput">
                    <div id="extra_guestsError" class="invalid-feedback"></div>
                  </div>
                </div>
                <div class="price-preview mt-3 p-3 bg-light rounded">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">قیمت هر نفر پایه:</span>
                    <strong>{!! displayStayPrice($stay, 'per_person') !!} ریال</strong>
                  </div>
                  @if(!empty($stay->extra_person_price))
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">قیمت هر نفر اضافه:</span>
                      <strong>{!! displayStayPrice($stay, 'extra_person') !!} ریال</strong>
                    </div>
                  @endif
                  <div id="livePricePreview" class="mt-2 pt-2 border-top">
                    <div class="d-flex justify-content-between">
                      <span class="fw-semibold">مبلغ پایه:</span>
                      <span id="previewBasePrice" class="fw-bold text-primary">—</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                      <span>تخفیف اقامت‌گاه:</span>
                      <span id="previewStayDiscount">—</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                  ادامه
                  <i class="bi bi-arrow-left ms-2"></i>
                </button>
              </div>
            </form>
          </div>

          <!-- Step 2: Phone Verification + Identity -->
          <div id="step-2" class="booking-step">
            <div class="step-header">
              <h6 class="step-title">
                <i class="bi bi-phone text-success"></i>
                تایید شماره موبایل و اطلاعات شخصی
              </h6>
              <p class="step-subtitle">شماره موبایل خود را وارد کرده و کد تایید را دریافت کنید</p>
            </div>

            <form id="phoneIdentityForm" class="booking-form">
              <!-- Phone Section -->
              <div class="form-section">
                <h6 class="section-title">شماره موبایل</h6>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-phone"></i></span>
                  <input type="text" id="phoneInput" class="form-control" placeholder="09123456789" autocomplete="tel" required>
                </div>
                <div id="phoneError" class="invalid-feedback"></div>
                <button type="button" class="btn btn-outline-primary w-100 mt-2" id="sendOtpBtn">
                  <i class="bi bi-send me-2"></i>
                  ارسال کد تایید
                </button>
              </div>

              <!-- OTP Section -->
              <div id="otpSection" class="form-section d-none">
                <h6 class="section-title">کد تایید</h6>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                  <input type="text" id="otpInput" class="form-control text-center fw-bold" maxlength="6" placeholder="کد ۶ رقمی" required>
                </div>
                <div id="otpError" class="invalid-feedback"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <span id="otpTimer" class="badge bg-light text-dark"></span>
                  <button type="button" id="resendOtpBtn" class="btn btn-link btn-sm p-0" disabled>
                    ارسال مجدد
                    <i class="bi bi-arrow-clockwise"></i>
                  </button>
                </div>
                <button type="button" class="btn btn-success w-100 mt-2" id="verifyOtpBtn">
                  <i class="bi bi-check-circle me-2"></i>
                  تایید کد
                </button>
              </div>

              <!-- Identity Fields (shown after OTP verification) -->
              <div id="identitySection" class="form-section d-none">
                <h6 class="section-title">اطلاعات شخصی</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">نام</label>
                    <input type="text" class="form-control" id="first_nameInput" required>
                    <div id="first_nameError" class="invalid-feedback"></div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">نام خانوادگی</label>
                    <input type="text" class="form-control" id="last_nameInput" required>
                    <div id="last_nameError" class="invalid-feedback"></div>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">کد ملی</label>
                    <input type="text" class="form-control" id="national_idInput" required>
                    <div id="national_idError" class="invalid-feedback"></div>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">شماره موبایل (تایید شده)</label>
                    <input type="text" class="form-control" id="infoPhoneInput" readonly>
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button type="button" class="btn btn-outline-secondary me-2" id="backToStep1">
                  <i class="bi bi-arrow-right me-2"></i>
                  بازگشت
                </button>
                <button type="submit" class="btn btn-primary btn-lg flex-grow-1" id="proceedToReview" disabled>
                  محاسبه قیمت
                  <i class="bi bi-arrow-left ms-2"></i>
                </button>
              </div>
            </form>
          </div>

          <!-- Step 3: Review & Payment -->
          <div id="step-3" class="booking-step">
            <div class="step-header">
              <h6 class="step-title">
                <i class="bi bi-receipt text-warning"></i>
                بررسی نهایی و پرداخت
              </h6>
              <p class="step-subtitle">اطلاعات رزرو را بررسی کرده و به پرداخت بروید</p>
            </div>

            <div class="review-summary">
              <div class="summary-card">
                <h6 class="summary-title">خلاصه رزرو</h6>
                <div class="summary-item">
                  <i class="bi bi-calendar-event"></i>
                  <div>
                    <span class="label">تاریخ‌ها:</span>
                    <span id="revDates" class="value">—</span>
                  </div>
                </div>
                <div class="summary-item">
                  <i class="bi bi-clock"></i>
                  <div>
                    <span class="label">ساعت ورود/خروج:</span>
                    <span id="revTimes" class="value">—</span>
                  </div>
                </div>
                <div class="summary-item">
                  <i class="bi bi-people"></i>
                  <div>
                    <span class="label">تعداد نفرات:</span>
                    <span id="revGuests" class="value">—</span>
                  </div>
                </div>
                <div class="summary-item">
                  <i class="bi bi-person"></i>
                  <div>
                    <span class="label">مهمان:</span>
                    <span id="revName" class="value">—</span>
                  </div>
                </div>
              </div>

              <div class="pricing-card">
                <h6 class="summary-title">جزئیات قیمت</h6>
                <div class="pricing-row">
                  <span>تعداد شب:</span>
                  <strong id="revNights">—</strong>
                </div>
                <div class="pricing-row">
                  <span>مبلغ پایه:</span>
                  <strong id="revBase">—</strong>
                </div>
                <div class="pricing-row">
                  <span>مبلغ اضافه:</span>
                  <strong id="revExtra">—</strong>
                </div>
                <div class="pricing-row text-muted">
                  <span>تخفیف اقامت‌گاه:</span>
                  <span id="revStayDiscount">—</span>
                </div>
                <div class="pricing-row text-success" id="orgDiscountRow" style="display: none;">
                  <span>تخفیف سازمانی:</span>
                  <strong id="revOrgDiscount">—</strong>
                </div>
                <div id="orgDiscountMessage" class="alert alert-info d-none mt-2">
                  <i class="bi bi-info-circle me-2"></i>
                  <span id="orgDiscountText"></span>
                </div>
                <hr>
                <div class="pricing-row final-price">
                  <span>مبلغ نهایی:</span>
                  <strong id="revFinal" class="text-success">—</strong>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-outline-secondary me-2" id="backToStep2">
                <i class="bi bi-arrow-right me-2"></i>
                بازگشت
              </button>
              <button type="button" class="btn btn-success btn-lg flex-grow-1" id="goToPayment">
                <i class="bi bi-credit-card me-2"></i>
                پرداخت و تکمیل رزرو
              </button>
            </div>
          </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="booking-loading d-none">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">در حال پردازش...</span>
          </div>
          <p class="mt-3 fw-semibold text-primary">در حال پردازش...</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-modal.css') }}">
<link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">
<style>
/* Booking Modal Styles */
.booking-modal-content {
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.booking-header {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: white;
  border-bottom: none;
  padding: 1.25rem 1.5rem;
}

.booking-header .modal-title {
  font-weight: 600;
  font-size: 1.1rem;
}

.booking-stepper {
  background: #f8fafc;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.stepper-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.stepper-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
  min-width: 80px;
}

.stepper-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  position: relative;
  transition: all 0.3s ease;
}

.stepper-step.active .stepper-circle {
  background: #2563eb;
  color: white;
  transform: scale(1.1);
}

.stepper-step.completed .stepper-circle {
  background: #10b981;
  color: white;
}

.stepper-check {
  position: absolute;
  font-size: 1.2rem;
}

.stepper-step.completed .stepper-number {
  display: none;
}

.stepper-step.completed .stepper-check {
  display: block !important;
}

.stepper-label {
  font-size: 0.75rem;
  color: #64748b;
  text-align: center;
  font-weight: 500;
}

.stepper-step.active .stepper-label {
  color: #2563eb;
  font-weight: 600;
}

.stepper-line {
  flex: 1;
  height: 2px;
  background: #e2e8f0;
  min-width: 30px;
  max-width: 60px;
  transition: background 0.3s ease;
}

.stepper-step.completed + .stepper-line {
  background: #10b981;
}

.booking-content {
  padding: 2rem 1.5rem;
  min-height: 400px;
  position: relative;
}

.booking-step {
  display: none;
  animation: fadeIn 0.3s ease-in;
}

.booking-step.active {
  display: block;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateX(10px); }
  to { opacity: 1; transform: translateX(0); }
}

.step-header {
  margin-bottom: 1.5rem;
}

.step-title {
  font-weight: 600;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.step-subtitle {
  color: #64748b;
  font-size: 0.9rem;
  margin: 0;
}

.booking-form {
  max-width: 100%;
}

.form-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
  border-bottom: none;
}

.section-title {
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 1rem;
  color: #1e293b;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.price-preview {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
}

.review-summary {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.summary-card, .pricing-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 1.25rem;
}

.summary-title {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 1rem;
  color: #1e293b;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.summary-item i {
  color: #2563eb;
  font-size: 1.1rem;
  width: 24px;
}

.summary-item .label {
  color: #64748b;
  margin-left: 0.5rem;
}

.summary-item .value {
  font-weight: 600;
  color: #1e293b;
}

.pricing-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  font-size: 0.95rem;
}

.pricing-row.final-price {
  font-size: 1.1rem;
  padding-top: 0.75rem;
  border-top: 2px solid #e2e8f0;
  margin-top: 0.5rem;
}

.booking-loading {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(4px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  border-radius: 0 0 1rem 1rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .booking-modal-content .modal-dialog {
    margin: 0.5rem;
  }

  .booking-stepper {
    padding: 1rem;
  }

  .stepper-step {
    min-width: 60px;
  }

  .stepper-circle {
    width: 36px;
    height: 36px;
    font-size: 0.9rem;
  }

  .stepper-label {
    font-size: 0.65rem;
  }

  .stepper-line {
    min-width: 20px;
    max-width: 40px;
  }

  .booking-content {
    padding: 1.5rem 1rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .form-actions .btn {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .stepper-label {
    display: none;
  }

  .booking-content {
    padding: 1rem 0.75rem;
  }

  .step-title {
    font-size: 1rem;
  }

  .step-subtitle {
    font-size: 0.85rem;
  }
}

/* Form Validation */
.form-control.is-invalid {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}

.form-control:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

/* Ensure calendar overlays above modal (same as home.blade.php) */
.jalali-datepicker {
  z-index: 1065 !important;
}
.jalali-datepicker .jalali-datepicker-legend {
  z-index: 1066 !important;
}
.jalali-datepicker-portal {
  z-index: 1065 !important;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
  'use strict';

  // Scope all selectors to the booking modal
  const modal = document.getElementById('bookingModal');
  if (!modal) return;

  const q = (sel) => modal.querySelector(sel);
  const qa = (sel) => Array.from(modal.querySelectorAll(sel) || []);

  // State management
  let currentStep = 1;
  let verifiedPhone = null;
  let otpVerified = false;
  let bookingData = {};
  let otpTimer = null;
  let otpSeconds = 0;

  // Helper functions
  function toFaDigits(str) {
    return (str + '').replace(/[0-9]/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
  }

  function clearErrors() {
    qa('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    qa('.invalid-feedback').forEach(el => el.textContent = '');
  }

  function displayError(field, message) {
    const input = q('#' + field);
    const errorDiv = q('#' + field + 'Error');
    if (input) {
      input.classList.add('is-invalid');
    }
    if (errorDiv) {
      errorDiv.textContent = message;
    }
  }

  function showLoader(show = true) {
    const overlay = q('#loadingOverlay');
    if (overlay) {
      overlay.classList.toggle('d-none', !show);
    }
  }

  // Step navigation
  function goToStep(step) {
    if (step < 1 || step > 3) return;
    
    // Hide all steps
    qa('.booking-step').forEach(s => s.classList.remove('active'));
    
    // Update stepper
    qa('.stepper-step').forEach((s, i) => {
      const stepNum = i + 1;
      s.classList.remove('active', 'completed');
      if (stepNum < step) {
        s.classList.add('completed');
      } else if (stepNum === step) {
        s.classList.add('active');
      }
    });

    // Show target step
    const targetStep = q('#step-' + step);
    if (targetStep) {
      targetStep.classList.add('active');
    }

    currentStep = step;
    clearErrors();
  }

  // Step 1: Dates & Guests Form
  const datesGuestsForm = q('#datesGuestsForm');
  if (datesGuestsForm) {
    datesGuestsForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      clearErrors();

      const startDate = q('#startDate').value;
      const endDate = q('#endDate').value;
      const baseGuests = parseInt(q('#base_guestsInput').value) || 0;
      const extraGuests = parseInt(q('#extra_guestsInput').value) || 0;

      // Validation
      let hasError = false;
      if (!startDate) {
        displayError('startDate', 'تاریخ ورود را انتخاب کنید');
        hasError = true;
      }
      if (!endDate) {
        displayError('endDate', 'تاریخ خروج را انتخاب کنید');
        hasError = true;
      }
      if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
        displayError('endDate', 'تاریخ خروج باید بعد از تاریخ ورود باشد');
        hasError = true;
      }
      if (baseGuests < 1 || baseGuests > {{ $stay->base_capacity }}) {
        displayError('base_guests', 'تعداد نفرات پایه نامعتبر است');
        hasError = true;
      }
      if (extraGuests < 0 || extraGuests > {{ $stay->extra_capacity }}) {
        displayError('extra_guests', 'تعداد نفرات اضافه نامعتبر است');
        hasError = true;
      }

      if (hasError) return;

      // Store data (times are set by host, not user)
      bookingData = {
        start_date: startDate,
        end_date: endDate,
        base_guests: baseGuests,
        extra_guests: extraGuests
      };

      // Calculate and show price preview
      showLoader(true);
      try {
        const response = await fetch("{{ route('bookings.preview') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            stay_id: {{ $stay->id }},
            start_date: startDate,
            end_date: endDate,
            base_guests: baseGuests,
            extra_guests: extraGuests,
            phone: '',
            first_name: '',
            last_name: '',
            national_id: ''
          })
        });

        const data = await response.json();
        showLoader(false);

        if (data.success) {
          // Update price preview
          const nights = data.nights;
          const basePrice = data.base_price;
          q('#previewBasePrice').textContent = toFaDigits(basePrice.toLocaleString()) + ' ریال';
          if (data.stay_discount_amount > 0) {
            q('#previewStayDiscount').textContent = toFaDigits(data.stay_discount_amount.toLocaleString()) + ' ریال';
          } else {
            q('#previewStayDiscount').textContent = '—';
          }

          // Proceed to next step
          goToStep(2);
        } else {
          if (window.showError) {
            window.showError(data.message || 'خطا در محاسبه قیمت');
          } else {
            alert(data.message || 'خطا در محاسبه قیمت');
          }
        }
      } catch (error) {
        showLoader(false);
        console.error('Error:', error);
        if (window.showError) {
          window.showError('خطای ارتباط با سرور');
        } else {
          alert('خطای ارتباط با سرور');
        }
      }
    });
  }

  // Step 2: Phone & OTP
  const sendOtpBtn = q('#sendOtpBtn');
  if (sendOtpBtn) {
    sendOtpBtn.addEventListener('click', async function() {
      clearErrors();
      const phone = (q('#phoneInput').value || '').trim();
      
      if (!phone || !/^09\d{9}$/.test(phone)) {
        displayError('phone', 'شماره موبایل معتبر وارد کنید (مثال: 09123456789)');
        return;
      }

      showLoader(true);
      try {
        const response = await fetch("{{ route('booking.otp.send') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ phone })
        });

        const data = await response.json();
        showLoader(false);

        if (data.success || response.ok) {
          verifiedPhone = phone;
          q('#otpSection').classList.remove('d-none');
          q('#infoPhoneInput').value = phone;
          startOtpTimer();
        } else {
          displayError('phone', data.message || 'خطا در ارسال کد');
          if (window.showError) {
            window.showError(data.message || 'خطا در ارسال کد');
          }
        }
      } catch (error) {
        showLoader(false);
        console.error('Error:', error);
        if (window.showError) {
          window.showError('خطای ارتباط با سرور');
        } else {
          alert('خطای ارتباط با سرور');
        }
      }
    });
  }

  const verifyOtpBtn = q('#verifyOtpBtn');
  if (verifyOtpBtn) {
    verifyOtpBtn.addEventListener('click', async function() {
      clearErrors();
      const code = (q('#otpInput').value || '').trim();

      if (!code || code.length !== 6) {
        displayError('otp', 'کد تایید ۶ رقمی را وارد کنید');
        return;
      }

      showLoader(true);
      try {
        const response = await fetch("{{ route('booking.otp.verify') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ phone: verifiedPhone, code })
        });

        const data = await response.json();
        showLoader(false);

        if (data.success) {
          otpVerified = true;
          q('#identitySection').classList.remove('d-none');
          q('#proceedToReview').disabled = false;
          if (window.showSuccess) {
            window.showSuccess('شماره موبایل با موفقیت تایید شد');
          }
        } else {
          displayError('otp', data.message || 'کد تایید اشتباه است');
          if (window.showError) {
            window.showError(data.message || 'کد تایید اشتباه است');
          }
        }
      } catch (error) {
        showLoader(false);
        console.error('Error:', error);
        if (window.showError) {
          window.showError('خطای ارتباط با سرور');
        } else {
          alert('خطای ارتباط با سرور');
        }
      }
    });
  }

  const resendOtpBtn = q('#resendOtpBtn');
  if (resendOtpBtn) {
    resendOtpBtn.addEventListener('click', function() {
      if (sendOtpBtn) sendOtpBtn.click();
    });
  }

  function startOtpTimer() {
    otpSeconds = 120;
    if (otpTimer) clearInterval(otpTimer);
    if (resendOtpBtn) resendOtpBtn.disabled = true;

    updateOtpTimer();
    otpTimer = setInterval(() => {
      otpSeconds--;
      updateOtpTimer();
      if (otpSeconds <= 0) {
        clearInterval(otpTimer);
        if (resendOtpBtn) resendOtpBtn.disabled = false;
      }
    }, 1000);
  }

  function updateOtpTimer() {
    const timerEl = q('#otpTimer');
    if (timerEl) {
      const minutes = Math.floor(otpSeconds / 60);
      const seconds = otpSeconds % 60;
      timerEl.textContent = `${toFaDigits(minutes)}:${toFaDigits(seconds.toString().padStart(2, '0'))}`;
    }
  }

  // Step 2: Identity Form
  const phoneIdentityForm = q('#phoneIdentityForm');
  if (phoneIdentityForm) {
    phoneIdentityForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      if (!otpVerified) {
        if (window.showWarning) {
          window.showWarning('لطفا ابتدا شماره موبایل را تایید کنید');
        } else {
          alert('لطفا ابتدا شماره موبایل را تایید کنید');
        }
        return;
      }

      clearErrors();
      const firstName = (q('#first_nameInput').value || '').trim();
      const lastName = (q('#last_nameInput').value || '').trim();
      const nationalId = (q('#national_idInput').value || '').trim();

      let hasError = false;
      if (!firstName) {
        displayError('first_name', 'نام را وارد کنید');
        hasError = true;
      }
      if (!lastName) {
        displayError('last_name', 'نام خانوادگی را وارد کنید');
        hasError = true;
      }
      if (!nationalId || nationalId.length < 8) {
        displayError('national_id', 'کد ملی معتبر وارد کنید');
        hasError = true;
      }

      if (hasError) return;

      // Store identity data
      bookingData.phone = verifiedPhone;
      bookingData.first_name = firstName;
      bookingData.last_name = lastName;
      bookingData.national_id = nationalId;

      // Calculate final price with discounts
      showLoader(true);
      try {
        const response = await fetch("{{ route('bookings.preview') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            stay_id: {{ $stay->id }},
            ...bookingData
          })
        });

        const data = await response.json();
        showLoader(false);

        if (data.success) {
          fillReview(data);
          goToStep(3);
        } else {
          if (window.showError) {
            window.showError(data.message || 'خطا در محاسبه قیمت');
          } else {
            alert(data.message || 'خطا در محاسبه قیمت');
          }
        }
      } catch (error) {
        showLoader(false);
        console.error('Error:', error);
        if (window.showError) {
          window.showError('خطای ارتباط با سرور');
        } else {
          alert('خطای ارتباط با سرور');
        }
      }
    });
  }

  function fillReview(data) {
    const startDate = bookingData.start_date;
    const endDate = bookingData.end_date;
    const baseGuests = bookingData.base_guests;
    const extraGuests = bookingData.extra_guests;
    const fullName = bookingData.first_name + ' ' + bookingData.last_name;

    // Get times from stay (set by host, not user)
    const checkinTime = '{{ $stay->checkin_time ? \Carbon\Carbon::parse($stay->checkin_time)->format("H:i") : "14:00" }}';
    const checkoutTime = '{{ $stay->checkout_time ? \Carbon\Carbon::parse($stay->checkout_time)->format("H:i") : "12:00" }}';

    q('#revDates').textContent = toFaDigits(startDate) + ' تا ' + toFaDigits(endDate);
    q('#revTimes').textContent = toFaDigits(checkinTime) + ' / ' + toFaDigits(checkoutTime);
    q('#revGuests').textContent = `پایه: ${toFaDigits(baseGuests)} نفر${extraGuests > 0 ? ' / اضافه: ' + toFaDigits(extraGuests) + ' نفر' : ''}`;
    q('#revName').textContent = fullName;
    q('#revNights').textContent = toFaDigits(data.nights) + ' شب';
    q('#revBase').textContent = toFaDigits(data.base_price.toLocaleString()) + ' ریال';
    q('#revExtra').textContent = toFaDigits(data.extra_cost.toLocaleString()) + ' ریال';
    
    if (data.stay_discount_amount > 0) {
      q('#revStayDiscount').textContent = toFaDigits(data.stay_discount_amount.toLocaleString()) + ' ریال';
    } else {
      q('#revStayDiscount').textContent = '—';
    }

    if (data.org_discount_percent > 0) {
      q('#revOrgDiscount').textContent = toFaDigits(data.org_discount_amount.toLocaleString()) + ' ریال (' + toFaDigits(data.org_discount_percent) + '%)';
      q('#orgDiscountRow').style.display = 'flex';
      q('#orgDiscountMessage').classList.remove('d-none');
      q('#orgDiscountText').textContent = 'شما واجد شرایط تخفیف سازمانی هستید.';
    } else {
      q('#orgDiscountRow').style.display = 'none';
      q('#orgDiscountMessage').classList.add('d-none');
      q('#revOrgDiscount').textContent = '—';
    }

    q('#revFinal').textContent = toFaDigits(data.final_price.toLocaleString()) + ' ریال';
  }

  // Step 3: Payment
  const goToPaymentBtn = q('#goToPayment');
  if (goToPaymentBtn) {
    goToPaymentBtn.addEventListener('click', async function() {
      showLoader(true);
      try {
        const response = await fetch("{{ route('bookings.store') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            stay_id: {{ $stay->id }},
            ...bookingData
          })
        });

        const data = await response.json();
        showLoader(false);

        if (data.success) {
          if (window.showSuccess) {
            window.showSuccess('رزرو با موفقیت ثبت شد. در حال انتقال به صفحه پرداخت...');
          }
          setTimeout(() => {
            window.location.href = '{{ url('/payment/test') }}/' + data.booking_id;
          }, 1000);
        } else {
          if (window.showError) {
            window.showError(data.message || 'خطا در ثبت رزرو');
          } else {
            alert(data.message || 'خطا در ثبت رزرو');
          }
        }
      } catch (error) {
        showLoader(false);
        console.error('Error:', error);
        if (window.showError) {
          window.showError('خطای ارتباط با سرور');
        } else {
          alert('خطای ارتباط با سرور');
        }
      }
    });
  }

  // Back buttons
  const backToStep1 = q('#backToStep1');
  if (backToStep1) {
    backToStep1.addEventListener('click', () => goToStep(1));
  }

  const backToStep2 = q('#backToStep2');
  if (backToStep2) {
    backToStep2.addEventListener('click', () => goToStep(2));
  }

  // Allow clicking on completed stepper steps to navigate back
  qa('.stepper-step').forEach((step, index) => {
    step.addEventListener('click', function() {
      const stepNum = index + 1;
      // Only allow navigation to completed steps or current step
      if (stepNum <= currentStep) {
        goToStep(stepNum);
      }
    });
    // Add cursor pointer for completed/current steps
    step.style.cursor = 'pointer';
  });

  // Jalali Date Picker handlers - Exact same approach as home.blade.php
  function setupJalaliDatePicker() {
    const sd = document.getElementById('startDateDisplay');
    const ed = document.getElementById('endDateDisplay');
    const sh = document.getElementById('startDate');
    const eh = document.getElementById('endDate');

    if (!sd || !ed) return;

    function greg(detail, fallback) {
      try {
        if (detail?.date?.gregorian?.date) return detail.date.gregorian.date;
        if (detail?.date?.gregorian) return detail.date.gregorian;
        if (typeof detail?.date?.format === 'function') return detail.date.format('YYYY-MM-DD', 'en');
      } catch (_) {}
      return fallback;
    }

    // Set up event listeners first (EXACT SAME AS home.blade.php)
    sd?.addEventListener('jdp:change', function(e) {
      if (sh) sh.value = greg(e.detail, sd.value);
    });
    ed?.addEventListener('jdp:change', function(e) {
      if (eh) eh.value = greg(e.detail, ed.value);
    });
    sd?.addEventListener('change', function() {
      if (sh) sh.value = sd.value;
    });
    ed?.addEventListener('change', function() {
      if (eh) eh.value = ed.value;
    });

    // Explicitly attach pickers in case startWatch didn't bind yet (EXACT SAME AS home.blade.php)
    if (window.jalaliDatepicker && typeof jalaliDatepicker.attach === 'function') {
      try {
        jalaliDatepicker.attach('#startDateDisplay', { autoHide: true });
      } catch (e) {
        console.warn('Failed to attach startDateDisplay:', e);
      }
      try {
        jalaliDatepicker.attach('#endDateDisplay', { autoHide: true });
      } catch (e) {
        console.warn('Failed to attach endDateDisplay:', e);
      }
    } else {
      // Library not loaded yet, wait a bit and try again
      setTimeout(function() {
        if (window.jalaliDatepicker && typeof jalaliDatepicker.attach === 'function') {
          try {
            jalaliDatepicker.attach('#startDateDisplay', { autoHide: true });
            jalaliDatepicker.attach('#endDateDisplay', { autoHide: true });
          } catch (e) {
            console.warn('Failed to attach date pickers:', e);
          }
        }
      }, 300);
    }
  }

  // Initialize when modal is shown (Bootstrap 5 event) - EXACT SAME AS home.blade.php
  if (modal) {
    modal.addEventListener('shown.bs.modal', function() {
      // Small delay to ensure DOM is fully ready and library is loaded
      setTimeout(function() {
        // Ensure startWatch is running first (in case it wasn't initialized globally)
        if (window.jalaliDatepicker && typeof jalaliDatepicker.startWatch === 'function') {
          try {
            jalaliDatepicker.startWatch({ usePersianDigits: true });
          } catch (e) {
            // Already initialized, ignore
          }
        }
        // Then setup the date pickers
        setupJalaliDatePicker();
      }, 200);
    });
  }

  // Also try on initial DOMContentLoaded (fallback) - EXACT SAME AS home.blade.php
  document.addEventListener('DOMContentLoaded', function() {
    // Setup immediately if modal inputs exist (they might be in DOM but hidden)
    setTimeout(setupJalaliDatePicker, 200);
  });

  // Initialize
  goToStep(1);
})();
</script>
<script>
  jalaliDatepicker.startWatch({ zIndex:2000 });
</script>
@endpush
