@extends('layouts.app')

@section('title', 'صفحه اصلی')

@section('content')

<!-- ========================= HERO SECTION ========================= -->
  <section class="hero-section text-center text-white d-flex align-items-center">
    <div class="container">
      <h1 class="fw-bold mb-3 fade-in">سفرتو با بهترین اقامتگاه شروع کن</h1>
      <p class="lead mb-4 fade-in-delay">رزرو آسان، پشتیبانی ۲۴ ساعته، و تجربه‌ی اقامتی فراموش‌نشدنی در سراسر ایران</p>

      <div class="search-box mx-auto shadow-lg p-3 rounded-4 bg-white text-dark fade-in">
        <form class="row g-2">
          <div class="col-md-4">
            <input type="text" class="form-control" placeholder="کجا می‌خوای بری؟">
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" placeholder="تاریخ ورود">
          </div>
          <div class="col-md-3">
            <input type="date" class="form-control" placeholder="تاریخ خروج">
          </div>
          <div class="col-md-2">
            <button class="btn btn-secondary w-100"><i class="bi bi-search"></i> جستجو</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- ========================= FEATURE SECTION ========================= -->
  <section class="container py-5">
    <h2 class="text-center fw-bold mb-4">چرا تماشاگران سرزمین من؟</h2>
    <div class="row text-center">
      <div class="col-md-3 col-6 mb-4">
        <i class="bi bi-shield-check fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">رزرو امن</h6>
        <p class="small text-muted">تضمین امنیت پرداخت و رزرو شما در تمامی مراحل</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <i class="bi bi-headset fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">پشتیبانی ۲۴ ساعته</h6>
        <p class="small text-muted">در هر ساعت از شبانه‌روز پاسخگوی شما هستیم</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <i class="bi bi-geo-alt fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">اقامتگاه در سراسر ایران</h6>
        <p class="small text-muted">از شمال تا جنوب، هرکجا بخواهید اقامتگاه داریم</p>
      </div>
      <div class="col-md-3 col-6 mb-4">
        <i class="bi bi-wallet2 fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">بهترین قیمت</h6>
        <p class="small text-muted">با کمترین قیمت و بیشترین امکانات رزرو کنید</p>
      </div>
    </div>
  </section>

  <!-- ========================= POPULAR PLACES ========================= -->
  <section class="popular-section py-5 bg-light">
    <div class="container">
      <h2 class="text-center fw-bold mb-4">محبوب‌ترین مقاصد</h2>
      <div class="row g-4">
        <!-- 🔹 اینجا عکس‌های واقعی شهرها قرار بگیرد -->
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="/images/places/tehran.jpg" alt="تهران" class="card-img-top">
            <div class="card-body text-center">
              <h6 class="fw-bold">تهران</h6>
              <p class="small text-muted">۱۵۲ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="/images/places/rasht.jpg" alt="رشت" class="card-img-top">
            <div class="card-body text-center">
              <h6 class="fw-bold">رشت</h6>
              <p class="small text-muted">۹۸ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="/images/places/shiraz.jpg" alt="شیراز" class="card-img-top">
            <div class="card-body text-center">
              <h6 class="fw-bold">شیراز</h6>
              <p class="small text-muted">۷۵ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="/images/places/mazandaran.jpg" alt="مازندران" class="card-img-top">
            <div class="card-body text-center">
              <h6 class="fw-bold">مازندران</h6>
              <p class="small text-muted">۲۱۲ اقامتگاه</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================= CALL TO ACTION ========================= -->
  <section class="cta-section text-center text-white py-5">
    <div class="container">
      <h2 class="fw-bold mb-3">میزبان شو و کسب درآمد کن!</h2>
      <p class="lead mb-4">اقامتگاه خودت رو ثبت کن و از رزروها درآمد کسب کن.</p>
      <a href="{{ route('login.form', ['role' => 'host']) }}" class="btn btn-light btn-lg text-primary fw-bold">
        <i class="bi bi-plus-circle"></i> ثبت اقامتگاه
      </a>
    </div>
  </section>

@endsection
