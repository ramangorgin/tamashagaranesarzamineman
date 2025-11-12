<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ورود یا ثبت‌نام میزبان | تماشاگران سرزمین من</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="{{ asset('css/host-login.css') }}" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header bg-primary text-white text-center rounded-top-4 py-3">
            <h4 class="fw-bold"><i class="bi bi-house-door"></i> ورود / ثبت‌نام میزبان</h4>
          </div>

          <div class="card-body p-4">
            <!-- مرحله ۱: ورود شماره موبایل -->
            <div id="step1" class="wizard-step active">
              <h5 class="text-center mb-3">شماره موبایل خود را وارد کنید</h5>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                <input type="tel" id="hostPhone" class="form-control" placeholder="مثلاً 09123456789">
              </div>
              <button class="btn btn-primary w-100" id="sendHostOtp">ارسال کد تایید</button>
              <small id="hostOtpStatus" class="text-muted d-block mt-2 text-center"></small>
            </div>

            <!-- مرحله ۲: تأیید کد -->
            <div id="step2" class="wizard-step d-none fade-in">
              <h5 class="text-center mb-3">کد ارسال‌شده را وارد کنید</h5>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="text" id="hostOtpCode" maxlength="6" class="form-control" placeholder="کد تایید">
              </div>
              <div class="d-flex justify-content-between">
                <button class="btn btn-success" id="verifyHostOtp">تایید</button>
                <button class="btn btn-link text-secondary" id="resendHostOtp" disabled>ارسال مجدد (60s)</button>
              </div>
              <small id="hostVerifyStatus" class="text-muted d-block mt-2 text-center"></small>
            </div>

            <!-- مرحله ۳: اطلاعات میزبان -->
            <div id="step3" class="wizard-step d-none fade-in">
              <h5 class="text-center mb-3">تکمیل اطلاعات میزبان</h5>

              <form id="hostInfoForm">
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
                    <input type="text" name="national_id" maxlength="10" class="form-control" placeholder="مثلاً 0012345678" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">شهر محل اقامت</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="city" class="form-control" placeholder="مثلاً شیراز" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">نوع اقامتگاه</label>
                  <select name="stay_type" class="form-select" required>
                    <option value="">انتخاب کنید...</option>
                    <option value="villa">ویلا</option>
                    <option value="apartment">آپارتمان</option>
                    <option value="traditional">خانه سنتی</option>
                    <option value="eco">اقامتگاه بوم‌گردی</option>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">
                  <i class="bi bi-check-circle"></i> ثبت اطلاعات
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/host-login.js') }}"></script>
</body>
</html>
