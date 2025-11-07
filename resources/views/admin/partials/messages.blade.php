{{-- پیام‌های خطا --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- پیام موفقیت --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- پیام هشدار --}}
@if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif
