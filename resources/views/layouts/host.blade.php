<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'پنل میزبان')</title>

  {{-- Bootstrap & Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

  <style>
    body {
      font-family: "Vazirmatn", sans-serif;
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
      transition: transform 0.3s ease-in-out;
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
      .sidebar {
        transform: translateX(250px);
      }
      .sidebar.active {
        transform: translateX(0);
      }
      main {
        margin-right: 0;
        padding: 1.5rem;
      }
      .host-header {
        padding-right: 1.5rem;
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
    <a href="#"><i class="bi bi-building"></i> اقامت‌گاه‌های من</a>
    <a href="#"><i class="bi bi-wallet2"></i> درآمدها و تسویه‌حساب‌ها</a>
    <a href="#"><i class="bi bi-chat-left-text"></i> پیام‌ها و درخواست‌ها</a>
    <a href="#"><i class="bi bi-gear"></i> تنظیمات پروفایل</a>
    <a href="#" onclick="logout()"><i class="bi bi-box-arrow-left"></i> خروج</a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
  </aside>

  {{-- Header --}}
  <header class="host-header">
    <div class="d-flex align-items-center">
      <i class="bi bi-list toggle-btn me-3" id="toggleSidebar"></i>
      <h5 class="mb-0 text-dark fw-semibold">به پنل میزبان خوش آمدید</h5>
    </div>
    <div class="header-icons d-flex align-items-center">
      <i class="bi bi-bell position-relative"><span class="notification-dot"></span></i>
      <i class="bi bi-person-circle"></i>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Sidebar toggle
    $('#toggleSidebar').on('click', function(){
      $('.sidebar').toggleClass('active');
    });
    // Logout
    function logout(){
      document.getElementById('logout-form').submit();
    }
  </script>
  @stack('scripts')
</body>
</html>
