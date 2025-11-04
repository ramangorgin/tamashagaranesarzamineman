<!DOCTYPE html>
<html lang="fa">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title', 'سامانه تمشاگران سرزمین من')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <style>
            body {
                font-family: Vazirmatn, sans-serif;
            }
        </style>
        @stack('styles')
    </head>
    <body dir="rtl">

        <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('stays.index') }}">پنل مدیریت</a>
            </div>
        </nav>

        <main>
            @include('alerts')
            @yield('content')
        </main>
        
        @stack('scripts')
    </body>
</html>