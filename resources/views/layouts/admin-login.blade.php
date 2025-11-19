<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ورود مدیر | تماشاگران سرزمین من')</title>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vazirmatn Font -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            font-family: "Vazirmatn", sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(15px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .login-card {
            background: #ffffff;
            color: #1e293b;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            padding: 2rem 2.2rem;
            position: relative;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            from {transform: scale(0.95); opacity: 0;}
            to {transform: scale(1); opacity: 1;}
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header i {
            font-size: 2.5rem;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }

        .login-header h4 {
            font-weight: 700;
            color: #0f172a;
        }

        .btn-primary {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #1e40af, #1d4ed8);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(90deg, #059669, #047857);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: linear-gradient(90deg, #065f46, #047857);
            transform: translateY(-1px);
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* Mobile-friendly */
        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-shield-lock-fill"></i>
            <h4>ورود مدیر</h4>
            <p class="text-muted small">سامانه مدیریت تماشاگران سرزمین من</p>
        </div>

        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
