@extends('layouts.app')
@section('title','درباره ما')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5 anim fade-up">
        <h1 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-1"></i>درباره تماشاگران سرزمین من</h1>
        <p class="lead text-secondary mb-0">پلتفرمی برای اتصال سفرها به تجربه‌های اصیل و امن در سراسر ایران.</p>
    </div>
    <div class="row g-4 align-items-stretch">
        <div class="col-md-4 anim fade-up delay-1">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="bi bi-bullseye text-primary me-1"></i>ماموریت ما</h5>
                    <p class="small text-secondary mb-0">تسهیل رزرو اقامتگاه معتبر با فرایندهای شفاف، کمک به رشد گردشگری داخلی و ایجاد فرصت‌های اقتصادی پایدار برای جوامع محلی. ما اعتماد، سرعت و تجربه کاربری را در اولویت قرار داده‌ایم.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 anim fade-up delay-2">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="bi bi-shield-check text-primary me-1"></i>ارزش‌های اصلی</h5>
                    <ul class="small text-secondary mb-0 list-unstyled">
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>اعتماد و امنیت داده</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>پشتیبانی ۲۴ ساعته</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>شفافیت قیمت و قوانین</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>تمرکز بر کیفیت میزبان‌ها</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>نوآوری در تجربه کاربری</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4 anim fade-up delay-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-2"><i class="bi bi-graph-up text-primary me-1"></i>چشم‌انداز</h5>
                    <p class="small text-secondary mb-0">ایجاد شبکه‌ای هوشمند از اقامتگاه‌های بوم‌گردی، ویلا و آپارتمان با استانداردهای اعتبارسنجی یکپارچه و ابزارهای تحلیلی برای بهینه‌سازی ظرفیت و کیفیت سفرهای داخلی.</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="row g-4">
        <div class="col-lg-6 anim fade-up">
            <h4 class="fw-bold mb-3"><i class="bi bi-lightbulb-fill text-warning me-1"></i>چرا این پلتفرم را ساختیم؟</h4>
            <p class="small text-secondary">سفر داخلی در ایران ظرفیت‌های فرهنگی، طبیعی و اقتصادی فراوانی دارد اما پراکندگی اطلاعات، نبود معیارهای یکپارچه اعتبارسنجی و دشواری مقایسه قیمت باعث شده تجربه رزرو آنلاین همیشه ایده‌آل نباشد. ما تلاش می‌کنیم با استانداردسازی داده‌ها، ساده‌سازی فرایند رزرو، و ایجاد کانال ارتباطی سالم بین میزبان و مسافر، کیفیت و امنیت تجربه سفر را افزایش دهیم.</p>
            <p class="small text-secondary mb-0">تمامی میزبان‌ها پیش از فعال شدن توسط تیم ما اعتبارسنجی می‌شوند و اقامتگاه‌ها به‌صورت دوره‌ای ارزیابی کیفی می‌گردند تا اعتماد بلندمدت شکل بگیرد.</p>
        </div>
        <div class="col-lg-6 anim fade-up delay-2">
            <h4 class="fw-bold mb-3"><i class="bi bi-layers-fill text-info me-1"></i>آنچه متمایزمان می‌کند</h4>
            <ul class="small text-secondary list-unstyled mb-0">
                <li class="mb-2"><i class="bi bi-star-fill text-warning me-1"></i>تمرکز بر اقامتگاه‌های منتخب با کیفیت تأییدشده</li>
                <li class="mb-2"><i class="bi bi-star-fill text-warning me-1"></i>قیمت‌گذاری شفاف و نمایش دوره‌های اوج (Peak)</li>
                <li class="mb-2"><i class="bi bi-star-fill text-warning me-1"></i>پشتیبانی واکنش‌گرا از لحظه جستجو تا پایان اقامت</li>
                <li class="mb-2"><i class="bi bi-star-fill text-warning me-1"></i>رابط کاربری فارسی‌سازی شده و بومی برای تجربه سریع‌تر</li>
                <li class="mb-2"><i class="bi bi-star-fill text-warning me-1"></i>تمرکز بر توسعه ابزارهای هوشمند پیشنهاد اقامتگاه</li>
            </ul>
        </div>
    </div>
</div>
@endsection