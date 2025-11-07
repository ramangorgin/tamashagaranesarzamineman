<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BreadcrumbServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('admin.partials.breadcrumb', function ($view) {
            $routeName = Route::currentRouteName();

            $map = [
                'admin.dashboard' => 'داشبورد ادمین',
                'stays.index' => 'اقامتگاه‌ها',
                'stays.create' => 'ایجاد اقامتگاه جدید',
                'stays.edit' => 'ویرایش اقامتگاه',
                'stays.show' => 'نمایش اقامتگاه',
                'discount-contracts.index' => 'قراردادهای تخفیف',
                'discount-contracts.create' => 'ایجاد قرارداد جدید',
                'discount-contracts.edit' => 'ویرایش قرارداد',
                'discount-contracts.show' => 'نمایش قرارداد',
            ];

            $pageTitle = $map[$routeName] ?? 'صفحه جاری';
            $view->with('pageTitle', $pageTitle);
        });
    }
}
