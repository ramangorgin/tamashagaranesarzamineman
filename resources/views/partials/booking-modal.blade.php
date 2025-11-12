<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-door-open me-2"></i> رزرو اقامت‌گاه
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <!-- مرحله 1: شماره موبایل -->
        <div id="step-phone">
          <h6 class="fw-bold text-dark mb-3">۱️⃣ وارد کردن شماره موبایل</h6>
          <div class="mb-3">
            <label class="form-label">شماره موبایل</label>
            <input type="text" id="phoneInput" class="form-control" placeholder="مثلاً 09123456789">
            <div id="phoneError" class="invalid-feedback"></div>
          </div>
          <button class="btn btn-primary w-100" id="sendOtpBtn">
            ارسال کد تأیید <i class="bi bi-send ms-1"></i>
          </button>
          <!-- 🔄 Loading Overlay -->
            <div id="loadingOverlay" class="d-none">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 fw-semibold text-primary">لطفاً صبر کنید...</p>
                </div>
            </div>

        </div>

        <!-- مرحله 2: کد تایید -->
        <div id="step-otp" class="d-none">
          <h6 class="fw-bold text-dark mb-3">۲️⃣ تأیید شماره موبایل</h6>
          <div class="mb-3 text-center">
            <input type="text" id="otpCode" class="form-control text-center fs-5 fw-bold"
                   maxlength="6" placeholder="کد ۶ رقمی پیامک‌شده را وارد کنید">
            <div id="otpError" class="invalid-feedback text-center"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span id="otpTimer" class="text-muted small"></span>
            <button id="resendOtpBtn" class="btn btn-outline-secondary btn-sm" disabled>
              ارسال مجدد <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
          <button class="btn btn-success w-100 mt-3" id="verifyOtpBtn">
            بررسی کد <i class="bi bi-check-circle ms-1"></i>
          </button>
        </div>

        <!-- مرحله 3: مشخصات -->
        <div id="step-info" class="d-none">
          <h6 class="fw-bold text-dark mb-3">۳️⃣ وارد کردن اطلاعات شخصی</h6>
          <form id="bookingForm">
            @csrf
            <input type="hidden" name="stay_id" value="{{ $stay->id }}">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام</label>
                <input type="text" name="first_name" class="form-control" required>
                <div class="invalid-feedback">نام را وارد کنید.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">نام خانوادگی</label>
                <input type="text" name="last_name" class="form-control" required>
                <div class="invalid-feedback">نام خانوادگی را وارد کنید.</div>
              </div>
              <div class="col-md-12">
                <label class="form-label">کد ملی</label>
                <input type="text" name="national_id" class="form-control" required>
                <div class="invalid-feedback">کد ملی را وارد کنید.</div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">
              بررسی تخفیف و مبلغ نهایی
            </button>
          </form>
        </div>

        <!-- مرحله 4: نتیجه -->
        <div id="step-result" class="d-none text-center">
          <i class="bi bi-gift-fill text-success fs-1"></i>
          <h5 class="mt-3 fw-bold text-dark" id="discountMsg"></h5>
          <p class="fs-5 text-primary mt-2">مبلغ قابل پرداخت: <span id="finalPrice"></span> تومان</p>
          <button id="goToPayment" class="btn btn-success w-100 mt-3">
            پرداخت <i class="bi bi-credit-card ms-1"></i>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

