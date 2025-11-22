@extends('layouts.app')
@section('title','قوانین و مقررات')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5 anim fade-up">
        <h1 class="fw-bold mb-3"><i class="bi bi-file-text text-primary me-1"></i>قوانین و مقررات سامانه</h1>
        <p class="lead text-secondary mb-0">مطالعه این بخش به شما کمک می‌کند تجربه‌ای امن و شفاف داشته باشید.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 anim fade-up">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ol class="small text-secondary mb-0 ps-3">
                        <li class="mb-3">مسافر موظف است اطلاعات هویتی خود را هنگام ثبت رزرو به‌صورت صحیح وارد کند. هرگونه مغایرت می‌تواند منجر به لغو رزرو شود.</li>
                        <li class="mb-3">لغو رزرو تابع سیاست لغو هر اقامتگاه است که قبل از پرداخت نمایش داده می‌شود. درصد بازگشت وجه بر اساس زمان باقی‌مانده تا شروع اقامت محاسبه می‌گردد.</li>
                        <li class="mb-3">میزبان مسئول صحت تصاویر، توضیحات و امکانات ثبت شده برای اقامتگاه است و متعهد می‌شود شرایط ارائه‌شده را در طول اقامت مسافر رعایت کند.</li>
                        <li class="mb-3">هرگونه خسارت به اقامتگاه که خارج از فرسایش عادی باشد طبق گزارش میزبان و مستندات قابل پیگیری بوده و ممکن است شامل پرداخت غرامت توسط مسافر گردد.</li>
                        <li class="mb-3">استفاده از پلتفرم برای مقاصد غیرقانونی، انتقال محتوای مخرب یا نقض حریم خصوصی سایر کاربران کاملاً ممنوع است و منجر به مسدودسازی حساب خواهد شد.</li>
                        <li class="mb-3">بازپرداخت‌ها (Refund) حداکثر طی ۷۲ ساعت کاری پس از تأیید لغو یا مشکل کیفی ثبت‌شده پردازش می‌شوند.</li>
                        <li class="mb-3">ثبت نظرات باید منصفانه، محترمانه و مرتبط با تجربه اقامت باشد. نظرات حاوی توهین، افشای اطلاعات شخصی یا تبلیغات حذف خواهند شد.</li>
                        <li class="mb-3">پشتیبانی ۲۴ ساعته در شرایط اضطراری (لغو ناگهانی میزبان، مشکلات ایمنی) در دسترس است و از طریق ایمیل یا پیام‌رسان‌های معرفی‌شده پیگیری می‌شود.</li>
                        <li class="mb-3">به‌روزرسانی قوانین ممکن است در بازه‌های زمانی انجام شود؛ تاریخ آخرین ویرایش در پایین همین صفحه درج می‌گردد. ادامه استفاده به معنای پذیرش نسخه جدید است.</li>
                        <li class="mb-3">مالکیت معنوی محتوا (متن‌ها، چیدمان، نشان‌های گرافیکی) متعلق به سامانه بوده و هرگونه استفاده تجاری بدون مجوز کتبی ممنوع است.</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-lg-4 anim fade-up delay-2">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-1"></i>نکات تکمیلی</h5>
                    <ul class="small text-secondary list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>حریم خصوصی کاربران برای ما اولویت است.</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>اطلاعات پرداخت فقط از درگاه‌های امن عبور می‌کند.</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>گزارش تخلف را از طریق فرم پشتیبانی ارسال کنید.</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>برای دریافت فاکتور رسمی از بخش پشتیبانی درخواست دهید.</li>
                    </ul>
                    <div class="mt-4 small text-muted">آخرین بروزرسانی: آبان ۱۴۰۴</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection