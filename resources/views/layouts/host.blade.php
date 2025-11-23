<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'پنل میزبان')</title>

  {{-- Bootstrap & Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f9fafb;
      overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      right: 0;
      width: 250px;
      height: 100vh;
      background: linear-gradient(180deg, #2563eb, #1e3a8a);
      color: #fff;
      display: flex;
      flex-direction: column;
      transition: right 0.3s ease-in-out;
      z-index: 1000;
    }
    .sidebar.collapsed {
      transform: translateX(250px);
    }
    .sidebar h4 {
      text-align: center;
      margin: 1.2rem 0;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .sidebar a {
      color: #e0e7ff;
      text-decoration: none;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      border-right: 3px solid transparent;
      transition: 0.2s;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255,255,255,0.15);
      border-right: 3px solid #fff;
      color: #fff;
    }
    .sidebar i {
      margin-left: 10px;
      font-size: 1.2rem;
    }

    /* Header */
    .host-header {
      height: 60px;
      background-color: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
      padding-right: 17rem;
      position: sticky;
      top: 0;
      z-index: 900;
    }
    .header-icons i {
      font-size: 1.5rem;
      margin-left: 1rem;
      color: #475569;
      cursor: pointer;
      position: relative;
      transition: 0.2s;
    }
    .header-icons i:hover {
      color: #2563eb;
    }
    .notification-dot {
      width: 8px;
      height: 8px;
      background: #f87171;
      border-radius: 50%;
      position: absolute;
      top: 2px;
      right: 8px;
    }

    /* Content */
    main {
      margin-right: 250px;
      padding: 2rem;
      transition: all 0.3s ease;
    }

    /* Mobile adjustments */
    @media (max-width: 992px) {
      .sidebar { right: -250px; }
      .sidebar.active { right: 0; }
      main {
        margin-right: 0;
        padding: 1.5rem;
      }
      .host-header {
        padding-right: 1.5rem;
        align-items: center;
      }
    }

    /* Cards */
    .stat-card {
      border-radius: 15px;
      padding: 20px;
      color: #fff;
      transition: 0.3s;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .bg-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .bg-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
    .bg-green { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .bg-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .toggle-btn {
      display: none;
      font-size: 1.8rem;
      color: #2563eb;
      cursor: pointer;
    }

    @media (max-width: 992px) {
      .toggle-btn {
        display: inline-block;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

  {{-- Sidebar --}}
  <aside class="sidebar animate__animated animate__fadeInRight">
    <h4><i class="bi bi-person-workspace me-2"></i>پنل میزبان</h4>
    <a href="{{ route('host.dashboard') }}" class="{{ request()->routeIs('host.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> داشبورد</a>
    <a href="{{ route('host.stays.index') }}"><i class="bi bi-building"></i> اقامت‌گاه‌های من</a>
    <a href="#"><i class="bi bi-wallet2"></i> درآمدها و تسویه‌حساب‌ها</a>
    <a href="#"><i class="bi bi-chat-left-text"></i> پیام‌ها و درخواست‌ها</a>
    <a href="#"><i class="bi bi-gear"></i> تنظیمات پروفایل</a>
    <a href="{{ route('logout', ['role' => 'host']) }}" onclick="event.preventDefault(); logout()">
      <i class="bi bi-box-arrow-left"></i> خروج
    </a>
    <form id="logout-form" action="{{ route('logout', ['role' => 'host']) }}" method="POST" class="d-none">
      @csrf
    </form>
  </aside>

  {{-- Header --}}
  <header class="host-header">
    <div class="d-flex align-items-center">
      <i class="bi bi-list fs-3 me-3 mt-2 d-lg-none" id="sidebarToggle" style="cursor:pointer;"></i>
      <h5 class="mb-0 text-dark fw-semibold">به پنل میزبان خوش آمدید</h5>
    </div>
    <div class="header-icons d-flex align-items-center">
      <i class="bi bi-bell position-relative"><span class="notification-dot"></span></i>
      <i class="bi bi-person-circle"></i>
    </div>
  </header>

  <main>
    @if(View::hasSection('breadcrumb') || View::hasSection('breadcrumb-actions'))
      <div class="container-fluid px-0 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><a href="{{ route('host.dashboard') }}">داشبورد</a></li>
              @yield('breadcrumb')
            </ol>
          </nav>
          <div class="d-flex align-items-center gap-2">
            @yield('breadcrumb-actions')
          </div>
        </div>
      </div>
    @endif
    @yield('content')
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Sidebar: mirror admin overlay behavior (mobile)
    document.addEventListener('DOMContentLoaded',function(){
      const sidebar=document.querySelector('.sidebar');
      const toggleBtn=document.getElementById('sidebarToggle');
      const overlay=document.createElement('div');
      overlay.id='host-sidebar-overlay';
      Object.assign(overlay.style,{position:'fixed',top:0,left:0,width:'100%',height:'100%',background:'rgba(0,0,0,0.35)',zIndex:'995',display:'none',opacity:'0',transition:'opacity .3s'});
      document.body.appendChild(overlay);

      const open=()=>{ sidebar.classList.add('active'); overlay.style.display='block'; requestAnimationFrame(()=>overlay.style.opacity='1'); };
      const close=()=>{ sidebar.classList.remove('active'); overlay.style.opacity='0'; setTimeout(()=>overlay.style.display='none',200); };

      toggleBtn?.addEventListener('click', e=>{ e.preventDefault(); sidebar.classList.contains('active') ? close() : open(); });
      overlay.addEventListener('click', close);
      document.addEventListener('keydown', e=>{ if(e.key==='Escape' && sidebar.classList.contains('active')) close(); });
      // Close on link click (mobile only)
      const mq = window.matchMedia('(max-width: 992px)');
      document.querySelectorAll('.sidebar a').forEach(a=> a.addEventListener('click', ()=>{ if(mq.matches) close(); }));
    });
    // Logout
    function logout(){ document.getElementById('logout-form').submit(); }
  </script>
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
  @stack('scripts')
</body>
</html>
