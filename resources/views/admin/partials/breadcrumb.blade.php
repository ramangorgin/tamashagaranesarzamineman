@php
    use Illuminate\Support\Facades\Route;
    $current = Route::currentRouteName();
    $backRoute = match(true) {
        str_contains($current, 'discount-contracts.') => route('discount-contracts.index'),
        str_contains($current, 'stays.') => route('admin.stays.index'),
        default => route('admin.dashboard'),
    };
@endphp

<div class="breadcrumb-container mb-4 rounded-3 p-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
    <div class="d-flex align-items-center gap-2">
        {{-- دکمه بازگشت --}}
        <a href="{{ $backRoute }}" class="btn btn-return d-flex align-items-center">
            <i class="bi bi-arrow-right-circle-fill me-2 fs-5"></i>
            بازگشت
        </a>

        {{-- مسیر ناوبری --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">داشبورد</a>
                </li>

                @if(isset($breadcrumbs) && is_array($breadcrumbs))
                    @foreach($breadcrumbs as $title => $url)
                        @if ($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $url }}">{{ $title }}</a></li>
                        @endif
                    @endforeach
                @elseif(isset($pageTitle))
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                @endif
            </ol>
        </nav>
    </div>
</div>
