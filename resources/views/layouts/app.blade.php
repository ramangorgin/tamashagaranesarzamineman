<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تمشاگران سرزمین من')</title>
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
  
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/jalalidatepicker/jalalidatepicker.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="{{ asset('css/auth-modal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{ asset('css/preloader.css') }}" rel="stylesheet">
    @if(class_exists('Livewire\\Livewire'))
        @livewireStyles
    @endif
</head>
   <style>
        .text-justify{
            text-align: justify;
        }
    </style>
    @stack('styles')

</head>
<body class="bg-light">

    <!-- ========================= PRELOADER ========================= -->
    <div id="preloader" aria-hidden="true">
        <div class="preloader-inner">
            <div class="preloader-ring">
                <img src="/images/logo.png" alt="در حال بارگذاری" class="preloader-logo" loading="eager">
            </div>
        </div>
    </div>

    <!-- ========================= HEADER ========================= -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#">
            <img src="/images/logo.png" alt="لوگو سایت" height="50"> <!-- 🔹 اینجا لوگوی واقعی سایت -->
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">خانه</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('stays.*') ? 'active' : '' }}" href="{{ route('stays.show', ['stay'=>1]) }}">اقامتگاه‌ها</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">درباره ما</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}" href="{{ route('support') }}">پشتیبانی</a></li>
            </ul>

                        <div class="d-flex gap-2">
                                @php
                                    $isAdmin = Auth::guard('admin')->check();
                                    $isHost  = Auth::guard('host')->check();
                                    $isUser  = Auth::guard('web')->check() && !$isAdmin && !$isHost; // regular user
                                @endphp

                                @if(!$isAdmin && !$isHost && !$isUser)
                                    {{-- Guest view (default buttons) --}}
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#authModal">
                                            ورود | ثبت‌نام
                                    </button>
                                    <a href="{{ route('login.form', ['role' => 'host']) }}" class="btn btn-warning">
                                            ورود میزبان
                                    </a>
                                @else
                                    {{-- Authenticated role view --}}
                                    @if($isAdmin)
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-speedometer2"></i> داشبورد مدیر
                                        </a>
                                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> خروج</button>
                                        </form>
                                    @elseif($isHost)
                                        <a href="{{ route('host.dashboard') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-house-door"></i> داشبورد میزبان
                                        </a>
                                        <form action="{{ route('host.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> خروج</button>
                                        </form>
                                    @elseif($isUser)
                                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-person"></i> حساب من
                                        </a>
                                        <form action="{{ route('user.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> خروج</button>
                                        </form>
                                    @endif
                                @endif
                        </div>
        </div>
        </div>
    </header>
    @include('partials.auth-modal')
    @include('partials.snackbar')

    <!-- ========================= MAIN ========================= -->
    <main class="container-fluid p-0">
        @yield('content')
    </main>

    <!-- ========================= FOOTER ========================= -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
        <div class="row">
            <!-- About Us -->
            <div class="col-md-3 mb-3">
                <h5 class="fw-bold">تماشاگران سرزمین من</h5>
                <p class="small text-secondary mb-0 text-justify">
                    تماشاگران سرزمین من یک پلتفرم یکپارچه برای جستجو، مقایسه و رزرو آنلاین انواع اقامتگاه در شهرها و روستاهای سراسر ایران است. 
                </p>
            </div>
            <!-- Useful Links -->
            <div class="col-md-3 mb-3 d-flex flex-column justify-content-start align-items-center text-center">
                <h6 class="fw-bold">لینک‌های مفید</h6>
                <ul class="list-unstyled small mb-0 footer-links">
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white-50 text-decoration-none">درباره ما</a></li>
                    <li class="mb-2"><a href="{{ route('terms') }}" class="text-white-50 text-decoration-none">قوانین و مقررات</a></li>
                    <li class="mb-2"><a href="{{ route('support') }}" class="text-white-50 text-decoration-none">پشتیبانی</a></li>
                </ul>
            </div>
            <!-- Social -->
            <div class="col-md-3 mb-3 text-center">
                <h6 class="fw-bold">ما را دنبال کنید</h6>
                <div class="d-flex justify-content-center gap-3 fs-4">
                    <a href="#" class="text-white-50"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-telegram"></i></a>
                    <a href="#" class="text-white-50"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <!-- Enamad Placeholder -->
            <div class="col-md-3 mb-3 text-center">
                <h6 class="fw-bold">نماد اعتماد الکترونیکی</h6>
                <div class="bg-white rounded-3 p-2 d-inline-block shadow-sm enamad-box">
                    <a href="https://trustseal.ecommerce.gov.ir/" target="_blank" rel="noopener" class="d-block">
                        <img src="/images/enamad-placeholder.png" alt="نماد اعتماد" style="height:80px" loading="lazy">
                    </a>
                </div>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center small text-white-50 mb-0">© ۱۴۰۴ تماشاگران سرزمین من - تمام حقوق محفوظ است</p>
        </div>
    </footer>


    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('vendor/jalalidatepicker/jalalidatepicker.min.js') }}"></script>
    <script>try{ jalaliDatepicker.startWatch({ usePersianDigits:true }); }catch(e){}</script>
    <script src="{{ asset('js/number-helper.js') }}"></script>
    <script src="{{ asset('js/auth-modal.js') }}"></script>

     <script>
        (function(){
        const map = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                    '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
        const pattern = /[۰-۹٠-٩]/g;
        function normalize(str){
            return str.replace(pattern, d => map[d] || d);
        }
        function bind(el){
            el.addEventListener('input', e => {
            const v = e.target.value;
            if (pattern.test(v)) {
                const caret = e.target.selectionStart;
                e.target.value = normalize(v);
                e.target.setSelectionRange(caret, caret);
            }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('input[type="text"],input[type="tel"],input[type="number"],input[type="password"],input:not([type]),textarea')
            .forEach(bind);
            // MutationObserver to handle dynamically added inputs
            new MutationObserver(muts => {
            muts.forEach(m => m.addedNodes.forEach(n => {
                if (n.nodeType===1) {
                if (n.matches && n.matches('input,textarea')) bind(n);
                n.querySelectorAll?.('input,textarea').forEach(bind);
                }
            }));
            }).observe(document.body,{childList:true,subtree:true});
        });
        })();
    </script>
    
    <script>
    // Global intersection observer for .anim elements
    document.addEventListener('DOMContentLoaded', function(){
        const items = document.querySelectorAll('.anim');
        const obs = new IntersectionObserver((entries)=>{
            entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in-view'); obs.unobserve(e.target);} });
        },{threshold:.12, rootMargin:'0px 0px -10% 0px'});
        items.forEach(i=>obs.observe(i));
    });
    </script>
    @stack('scripts')
    @if(class_exists('Livewire\\Livewire'))
        @livewireScripts
    @endif
    <script>
    // Hide preloader after full load (images + optional fonts)
    (function(){
  function removePreloader(){
    document.body.classList.add('preloader-loaded');
    setTimeout(()=>document.getElementById('preloader')?.remove(),600);
  }
  window.addEventListener('load', removePreloader);
  setTimeout(()=>{ if(!document.body.classList.contains('preloader-loaded')) removePreloader(); },4000);
})();
    </script>
</body>
</html>




