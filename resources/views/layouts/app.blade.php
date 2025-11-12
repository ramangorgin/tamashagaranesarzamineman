<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تمشاگران سرزمین من')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="{{ asset('css/auth-modal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
</head>
   <style>
        body {
            font-family: Vazirmatn, sans-serif;
        }
    </style>
    @stack('styles')

</head>
<body class="bg-light">

    <!-- ========================= HEADER ========================= -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#">
            <img src="/images/logo.png" alt="لوگو سایت" height="40"> <!-- 🔹 اینجا لوگوی واقعی سایت -->
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="#">خانه</a></li>
            <li class="nav-item"><a class="nav-link" href="#">اقامتگاه‌ها</a></li>
            <li class="nav-item"><a class="nav-link" href="#">درباره ما</a></li>
            <li class="nav-item"><a class="nav-link" href="#">تماس با ما</a></li>
            </ul>

            <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#authModal">
                ورود | ثبت‌نام
            </button>
            <a href="{{ route('host.login') }}" class="btn btn-warning">
                 ورود میزبان
            </a>
            </div>
        </div>
        </div>
    </header>
    @include('partials.auth-modal')


    <!-- ========================= MAIN ========================= -->
    <main class="container-fluid">
        @yield('content')
    </main>

    <!-- ========================= FOOTER ========================= -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
            <h5 class="fw-bold">تماشاگران سرزمین من</h5>
            <p class="small text-secondary">
                پلتفرم رزرو آنلاین اقامتگاه در سراسر ایران، با امنیت، پشتیبانی و تجربه‌ای لذت‌بخش.
            </p>
            </div>
            <div class="col-md-4 mb-3">
            <h6 class="fw-bold">لینک‌های مفید</h6>
            <ul class="list-unstyled small">
                <li><a href="#" class="text-white-50 text-decoration-none">درباره ما</a></li>
                <li><a href="#" class="text-white-50 text-decoration-none">قوانین و مقررات</a></li>
                <li><a href="#" class="text-white-50 text-decoration-none">پشتیبانی</a></li>
            </ul>
            </div>
            <div class="col-md-4 mb-3 text-center">
            <h6 class="fw-bold">ما را دنبال کنید</h6>
            <div class="d-flex justify-content-center gap-3 fs-4">
                <a href="#" class="text-white-50"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white-50"><i class="bi bi-telegram"></i></a>
                <a href="#" class="text-white-50"><i class="bi bi-whatsapp"></i></a>
            </div>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center small text-white-50 mb-0">© 2025 تماشاگران سرزمین من - تمام حقوق محفوظ است</p>
        </div>
    </footer>


    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.js"></script>
    <script src="{{ asset('js/number-helper.js') }}"></script>
    <script src="{{ asset('js/auth-modal.js') }}"></script>

    @stack('scripts')
</body>
</html>




