<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="authModalLabel"><i class="bi bi-shield-lock"></i> ورود یا ثبت‌نام</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <form id="userLoginForm">
            <div class="mb-3">
                <label class="form-label">نام و نام خانوادگی</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="مثلاً علی احمدی" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">کد ملی</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                    <input type="text" name="national_id" class="form-control" maxlength="10" placeholder="مثلاً 0012345678" required>
                </div>
            </div>

            <div class="mb-3 position-relative">
                <label class="form-label">شماره موبایل</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="tel" name="phone" id="phoneInput" class="form-control" placeholder="مثلاً 09123456789" required>
                </div>
                <button type="button" id="sendOtpBtn" class="btn btn-primary w-100 mt-3">
                    ارسال کد تایید
                </button>
            </div>

            <!-- بخش OTP (بعد از کلیک نمایش داده می‌شود) -->
            <div id="otpSection" class="d-none mt-3 fade-in">
                <label class="form-label">کد تایید</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="text" name="otp" id="otpInput" maxlength="6" class="form-control" placeholder="کد ارسال‌شده را وارد کنید">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="button" id="verifyOtpBtn" class="btn btn-success">تایید</button>
                    <button type="button" id="resendOtpBtn" class="btn btn-link text-secondary" disabled>ارسال مجدد (60s)</button>
                </div>
                <small id="otpStatus" class="text-muted mt-2 d-block"></small>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" id="finalSubmitBtn" class="btn btn-primary w-100 d-none">
                    <i class="bi bi-check-circle"></i> تکمیل ثبت‌نام
                </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
