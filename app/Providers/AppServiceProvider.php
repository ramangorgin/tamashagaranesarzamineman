<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\SmsIrService::class, function($app) {
            return new \App\Services\SmsIrService(config('smsir'));
        });
    }

    public function boot(): void
    {
        Blade::directive('faNum', function ($expr) {
            return "<?php echo faNum($expr); ?>";
        });
    }
}
