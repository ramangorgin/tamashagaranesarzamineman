<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <style>
        body {background-color:#f8f9fb;overflow-x:hidden;}
        .sidebar {position:fixed;top:0;right:0;width:240px;height:100vh;background-color:#1e293b;color:#fff;display:flex;flex-direction:column;transition:.3s;z-index:1050;}
        .sidebar h4{ text-align:center;margin:1.2rem 0;font-weight:600;}
        .sidebar a{color:#cbd5e1;text-decoration:none;display:flex;align-items:center;padding:12px 18px;transition:.2s;border-right:3px solid transparent;}
        .sidebar a:hover,.sidebar a.active{color:#fff;background-color:#334155;border-right:3px solid #0d6efd;}
        .sidebar i{margin-left:10px;font-size:1.2rem;}
        .admin-header{background:#fff;height:60px;display:flex;align-items:center;justify-content:space-between;padding:0 1.5rem;padding-right:17rem!important;box-shadow:0 2px 8px rgba(0,0,0,.05);position:sticky;top:0;z-index:1040;}
        main{margin-right:240px;padding:2rem;transition:.3s;}
        @media (max-width:992px){.sidebar{right:-240px;} .sidebar.active{right:0;} main{margin-right:0;padding:1.2rem;} .admin-header{padding-right:1rem!important;}}
        .breadcrumb-container{background:#f8f9fa;border-radius:12px;padding:12px 20px;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;border:1px solid #e2e8f0;box-shadow:0 2px 6px rgba(0,0,0,.03);}
        .breadcrumb{margin:0;background:transparent;font-size:.95rem;}
        .breadcrumb-item a{color:#0d6efd;text-decoration:none;transition:.2s;}
        .breadcrumb-item a:hover{text-decoration:underline;}
        .breadcrumb-item.active{color:#6c757d;font-weight:500;}
        .btn-return{background:#0d6efd;color:#fff;border:none;border-radius:8px;padding:6px 16px;font-size:.9rem;display:flex;align-items:center;transition:.25s;box-shadow:0 2px 5px rgba(13,110,253,.2);}
        .btn-return:hover{background:#0b5ed7;transform:translateY(-2px);box-shadow:0 3px 8px rgba(13,110,253,.3);}

        /* Header icon improvements */
        .header-icons{display:flex;align-items:center;gap:1rem;}
        .header-icons i{
            font-size:1.55rem;
            width:44px;
            height:44px;
            border-radius:50%;
            background:#f1f5f9;
            color:#334155;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            transition:.25s;
            cursor:pointer;
        }
        .header-icons i:hover{
            background:#0d6efd;
            color:#fff;
            box-shadow:0 4px 12px rgba(13,110,253,.35);
            transform:translateY(-2px);
        }
        .notification-dot{
            position:absolute;
            top:8px;
            left:10px;
            width:10px;
            height:10px;
            background:#dc3545;
            border-radius:50%;
            box-shadow:0 0 0 2px #fff;
            animation:pulse 1.6s infinite;
        }
        @keyframes pulse{
            0%{transform:scale(1);opacity:1;}
            60%{transform:scale(1.6);opacity:.3;}
            100%{transform:scale(1);opacity:1;}
        }
    </style>
    @stack('styles')  <!-- ensure page styles like Leaflet CSS are loaded -->
</head>
<body>
    <aside id="sidebar" class="sidebar">
        <h4>🎯 مدیریت وبسایت</h4>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> داشبورد
        </a>
        <a href="{{ route('admin.hosts.index') }}" class="{{ request()->routeIs('admin.hosts.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> مدیریت میزبانان
        </a>
        <a href="{{ route('admin.stays.index') }}" class="{{ request()->routeIs('admin.stays.*') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> مدیریت اقامت‌گاه‌ها
        </a>
        <a href="{{ route('admin.discount_contracts.index') }}" class="{{ request()->routeIs('admin.discount_contracts.*') ? 'active' : '' }}">
            <i class="bi bi-percent"></i> تخفیفات سازمانی
        </a>
        <a href="{{ route('admin.reserves.index') }}" class="{{ request()->routeIs('admin.reserves.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> رزروها
        </a>
        <a href="{{ route('logout',['role'=>'admin']) }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-left"></i> خروج
        </a>
        <form id="logout-form" action="{{ route('logout',['role'=>'admin']) }}" method="POST" class="d-none">@csrf</form>
    </aside>

    <header id="header" class="admin-header">
        <div class="d-flex align-items-center">
            <i class="bi bi-list fs-3 me-3 d-lg-none" id="sidebarToggle" style="cursor:pointer;"></i>
            <h5>پنل مدیریت - تماشاگران سرزمین من</h5>
        </div>
        <div class="header-icons d-flex align-items-center">
            <i class="bi bi-bell position-relative"><span class="notification-dot"></span></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </header>

    <main>
        @include('admin.partials.messages')

        @hasSection('breadcrumb')
            <div class="breadcrumb-container mb-4 rounded-3 p-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-return d-flex align-items-center">
                        <i class="bi bi-arrow-right-circle-fill me-2 fs-5"></i> بازگشت
                    </a>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
                <div>@yield('breadcrumb-actions')</div>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const sidebar=document.getElementById('sidebar');
            const toggleBtn=document.getElementById('sidebarToggle');
            const overlay=document.createElement('div');
            overlay.id='sidebar-overlay';
            Object.assign(overlay.style,{position:'fixed',top:0,left:0,width:'100%',height:'100%',background:'rgba(0,0,0,0.4)',zIndex:'1045',display:'none',transition:'opacity .3s'});
            document.body.appendChild(overlay);
            toggleBtn?.addEventListener('click',()=>{const active=sidebar.classList.toggle('active');overlay.style.display=active?'block':'none';overlay.style.opacity=active?'1':'0';});
            overlay.addEventListener('click',()=>{sidebar.classList.remove('active');overlay.style.opacity='0';setTimeout(()=>overlay.style.display='none',200);});
            document.addEventListener('keydown',e=>{if(e.key==='Escape'&&sidebar.classList.contains('active')){sidebar.classList.remove('active');overlay.style.opacity='0';setTimeout(()=>overlay.style.display='none',200);}});
        });
    </script>
    <script src="{{ asset('js/number-helper.js') }}"></script>
    @stack('scripts')
</body>
</html>
