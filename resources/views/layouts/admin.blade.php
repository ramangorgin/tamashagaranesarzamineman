<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت')</title>

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Font --}}
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />

    {{-- Persian Datepicker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">

    <style>
        body {
            font-family: "Vazirmatn", sans-serif;
            background-color: #f8f9fb;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 240px;
            height: 100vh;
            background-color: #1e293b;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
            z-index: 1050;
        }

        .sidebar h4 {
            text-align: center;
            margin: 1.2rem 0;
            font-weight: 600;
        }

        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            transition: 0.2s;
            border-right: 3px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background-color: #334155;
            border-right: 3px solid #0d6efd;
        }

        .sidebar i {
            margin-left: 10px;
            font-size: 1.2rem;
        }

        /* Header */
        .admin-header {
            background-color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            padding-right: 17rem !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .admin-header h5 {
            color: #1e293b;
            font-weight: 600;
            margin: 0;
        }

        .header-icons i {
            font-size: 1.4rem;
            margin-left: 1rem;
            color: #475569;
            cursor: pointer;
            transition: 0.2s;
            position: relative;
        }

        .header-icons i:hover {
            color: #0d6efd;
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background: red;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            right: 8px;
        }

        /* Main Content */
        main {
            margin-right: 240px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        /* Sidebar Toggle (Mobile) */
        .sidebar.hidden {
            right: -240px;
        }

        @media (max-width: 992px) {
            .sidebar {
                right: -240px;
            }
            #sidebar-overlay {
                backdrop-filter: blur(2px);
            }
            .sidebar.active {
                right: 0;
            }
            main {
                margin-right: 0;
                padding: 1.2rem;
            }
            .admin-header {
                padding-right: 1rem !important;
            }
        }

        /* Cards */
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            color: #fff;
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .bg-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .bg-green { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .bg-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
        .bg-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        /* Smooth fade for messages */
        .alert {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-10px);}
            to {opacity: 1; transform: translateY(0);}
        }
            /* Breadcrumb Section */
        .breadcrumb-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .breadcrumb {
            margin: 0;
            background: transparent;
            font-size: 0.95rem;
        }

        .breadcrumb-item a {
            color: #0d6efd;
            text-decoration: none;
            transition: 0.2s;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 500;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #adb5bd;
        }

        /* دکمه بازگشت */
        .btn-return {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: all 0.25s ease;
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
        }

        .btn-return i {
            margin-left: 8px;
            font-size: 1.1rem;
        }

        .btn-return:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(13, 110, 253, 0.3);
        }

    </style>
</head>
<body>

    {{-- Sidebar --}}
    <aside id="sidebar" class="sidebar">
        <h4>🎯 مدیریت وبسایت</h4>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> داشبورد
        </a>
        <a href="{{ route('stays.index') }}" class="{{ request()->routeIs('stays.*') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> اقامت‌گاه‌ها
        </a>
        <a href="{{ route('discount-contracts.index') }}" class="{{ request()->routeIs('discount-contracts.*') ? 'active' : '' }}">
            <i class="bi bi-percent"></i> تخفیفات سازمانی
        </a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-left"></i> خروج
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </aside>

    {{-- Header --}}
    <header id="header" class="admin-header">
        <div class="d-flex align-items-center">
            <i class="bi bi-list fs-3 me-3 d-lg-none" id="sidebarToggle" style="cursor:pointer;"></i>
            <h5>پنل مدیریت - تماشاگران سرزمین من</h5>
        </div>
        <div class="header-icons d-flex align-items-center">
            <i class="bi bi-bell position-relative">
                <span class="notification-dot"></span>
            </i>
            <i class="bi bi-person-circle"></i>
        </div>
    </header>

    {{-- Content --}}
    <main>
        {{-- بخش پیام‌ها و ارورها --}}
        @include('admin.partials.messages')

        {{-- بخش بردکرامب --}}
        @include('admin.partials.breadcrumb', ['pageTitle' => $pageTitle ?? null])

        {{-- محتوای اصلی --}}
        @yield('content')
    </main>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.js"></script>
    <script src="{{ asset('js/number-helper.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            // ایجاد یک لایه تار برای بیرون سایدبار
            const overlay = document.createElement('div');
            overlay.id = 'sidebar-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = 0;
            overlay.style.left = 0;
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.background = 'rgba(0,0,0,0.4)';
            overlay.style.zIndex = '1045';
            overlay.style.display = 'none';
            overlay.style.transition = 'opacity 0.3s ease';
            document.body.appendChild(overlay);

            // باز و بسته کردن سایدبار
            toggleBtn.addEventListener('click', function() {
                const isActive = sidebar.classList.toggle('active');
                overlay.style.display = isActive ? 'block' : 'none';
                overlay.style.opacity = isActive ? '1' : '0';
            });

            // بستن با کلیک روی بیرون
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 200);
            });

            // بستن با فشار دادن کلید ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === "Escape" && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.style.display = 'none', 200);
                }
            });
        });
    </script>


    @stack('scripts')
</body>
</html>
