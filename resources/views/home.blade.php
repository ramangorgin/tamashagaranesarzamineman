@extends('layouts.app')

@section('title', 'صفحه اصلی')

@section('content')

<!-- ========================= HERO SECTION ========================= -->
  <section class="hero-section text-center text-white d-flex align-items-center anim fade-up">
    <div class="container">
      <h1 class="fw-bold mb-3 anim fade-up delay-1">سفرتو با بهترین اقامتگاه شروع کن</h1>
      <p class="lead mb-4 anim fade-up delay-2">رزرو آسان، پشتیبانی ۲۴ ساعته، و تجربه‌ی اقامتی فراموش‌نشدنی در سراسر ایران</p>

      <div class="search-box mx-auto shadow-lg p-3 rounded-4 bg-white text-dark anim scale-in delay-3">
        <form class="row g-2">
          <div class="col-md-4">
            <input type="text" class="form-control" placeholder="کجا می‌خوای بری؟">
          </div>
          <div class="col-md-3">
            <input type="text" id="start_date_display" class="form-control" autocomplete="off" placeholder="تاریخ ورود">
            <input type="hidden" name="start_date" id="start_date">
          </div>
          <div class="col-md-3">
            <input type="text" id="end_date_display" class="form-control" autocomplete="off" placeholder="تاریخ خروج">
            <input type="hidden" name="end_date" id="end_date">
          </div>
          <div class="col-md-2">
            <button class="btn btn-secondary w-100"><i class="bi bi-search"></i> جستجو</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- ========================= FEATURE SECTION ========================= -->
  <section class="container py-5 anim fade-up">
    <h2 class="text-center fw-bold mb-5">چرا تماشاگران سرزمین من؟</h2>
    <div class="row text-center">
      <div class="col-md-3 col-6 mb-4 anim fade-up delay-1">
        <i class="bi bi-shield-check fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">رزرو امن</h6>
        <p class="small text-muted">تضمین امنیت پرداخت و رزرو شما در تمامی مراحل</p>
      </div>
      <div class="col-md-3 col-6 mb-4 anim fade-up delay-2">
        <i class="bi bi-headset fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">پشتیبانی ۲۴ ساعته</h6>
        <p class="small text-muted">در هر ساعت از شبانه‌روز پاسخگوی شما هستیم</p>
      </div>
      <div class="col-md-3 col-6 mb-4 anim fade-up delay-3">
        <i class="bi bi-geo-alt fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">اقامتگاه در سراسر ایران</h6>
        <p class="small text-muted">از شمال تا جنوب، هرکجا بخواهید اقامتگاه داریم</p>
      </div>
      <div class="col-md-3 col-6 mb-4 anim fade-up delay-4">
        <i class="bi bi-wallet2 fs-1 text-primary"></i>
        <h6 class="fw-bold mt-2">بهترین قیمت</h6>
        <p class="small text-muted">با کمترین قیمت و بیشترین امکانات رزرو کنید</p>
      </div>
    </div>
  </section>

  <!-- ========================= POPULAR PLACES ========================= -->
  <section class="popular-section py-5 bg-light anim fade-up">
    <div class="container">
      <h2 class="text-center fw-bold mb-5">محبوب‌ترین مقاصد</h2>
      <div class="row g-4">
        <!-- 🔹 اینجا عکس‌های واقعی شهرها قرار بگیرد -->
        <div class="col-md-3 col-6 anim scale-in delay-1">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="{{ asset('images/places/tehran.jpg') }}" alt="تهران" class="card-img-top" loading="lazy" decoding="async">
            <div class="card-body text-center">
              <h6 class="fw-bold">تهران</h6>
              <p class="small text-muted">۱۵۲ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6 anim scale-in delay-2">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="{{ asset('images/places/rasht.jpg') }}" alt="رشت" class="card-img-top" loading="lazy" decoding="async">
            <div class="card-body text-center">
              <h6 class="fw-bold">رشت</h6>
              <p class="small text-muted">۹۸ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6 anim scale-in delay-3">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="{{ asset('images/places/shiraz.jpg') }}" alt="شیراز" class="card-img-top" loading="lazy" decoding="async">
            <div class="card-body text-center">
              <h6 class="fw-bold">شیراز</h6>
              <p class="small text-muted">۷۵ اقامتگاه</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6 anim scale-in delay-4">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <img src="{{ asset('images/places/mazandaran.jpg') }}" alt="مازندران" class="card-img-top" loading="lazy" decoding="async">
            <div class="card-body text-center">
              <h6 class="fw-bold">مازندران</h6>
              <p class="small text-muted">۲۱۲ اقامتگاه</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================= FEATURED STAYS (LIVEWIRE) ========================= -->
  <section class="featured-stays-section py-5 anim fade-up">
    <div class="container">
      @livewire('home-stays')
    </div>
  </section>

  <!-- ========================= CALL TO ACTION ========================= -->
  <section class="cta-section text-center text-white py-5 anim fade-up">
    <div class="container">
      <h2 class="fw-bold mb-3">میزبان شو و کسب درآمد کن!</h2>
      <p class="lead mb-4">اقامتگاه خودت رو ثبت کن و از رزروها درآمد کسب کن.</p>
      <a href="{{ route('login.form', ['role' => 'host']) }}" class="btn btn-light btn-lg text-primary fw-bold">
        <i class="bi bi-plus-circle"></i> ثبت اقامتگاه
      </a>
    </div>
  </section>

@push('scripts')
<script>
// IntersectionObserver to add .in-view
document.addEventListener('DOMContentLoaded', function(){
  const items = document.querySelectorAll('.anim');
  const obs = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in-view'); obs.unobserve(e.target);} });
  },{threshold:.12, rootMargin:'0px 0px -10% 0px'});
  items.forEach(i=>obs.observe(i));
});
</script>
<script>
  // Jalali date pickers for start/end dates
  $(document).ready(function() {
    function toEnglishDigits(str){
      var map = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9'};
      return str.replace(/[۰-۹]/g, d => map[d] || d);
    }

    // Init start date picker
    $('#start_date_display').persianDatepicker({
      format: 'YYYY/MM/DD',
      initialValue: false,
      autoClose: true,
      toolbox: { calendarSwitch: { enabled: false } },
      onSelect: function(unix){
        var gregorian = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        gregorian = toEnglishDigits(gregorian);
        $('#start_date').val(gregorian);
        // constrain end date min
        if(window.endPicker){
          endPicker.options.minDate = unix;
        }
      }
    });

    // Init end date picker with dependency on start
    var endPicker = $('#end_date_display').persianDatepicker({
      format: 'YYYY/MM/DD',
      initialValue: false,
      autoClose: true,
      toolbox: { calendarSwitch: { enabled: false } },
      onSelect: function(unix){
        var gregorian = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
        gregorian = toEnglishDigits(gregorian);
        $('#end_date').val(gregorian);
      }
    }).data('datepicker');
    window.endPicker = endPicker; // expose for start picker minDate adjustment
  });
</script>
@endpush
@endsection
