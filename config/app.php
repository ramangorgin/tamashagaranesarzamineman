<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'Tamashagaranesarzamineman'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Asia/Tehran',

    'locale' => 'fa',

    'fallback_locale' => 'fa',

    'faker_locale' => 'fa_IR',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',


    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Melipayamak\Laravel\ServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        Melipayamak\Laravel\Facade::class,
    ])->toArray(),

];
