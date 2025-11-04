@extends('layouts.admin')

@section('title', 'لیست اقامت‌گاه‌ها')

@section('content')
<div class="container mt-4">
    <h3>لیست اقامتگاه‌ها</h3>
    <a href="{{ route('stays.create') }}" class="btn btn-success mb-3">+ افزودن اقامتگاه جدید</a>
    <form action="{{ route('stays.togglePeak') }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-warning">
            {{ \App\Models\Stay::where('is_peak', true)->exists() ? 'خروج از حالت پیک' : 'فعال‌سازی حالت پیک' }}
        </button>
    </form>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>عنوان</th>
                <th>شهر</th>
                <th>قیمت (هر شب)</th>
                <th>تخفیف (%)</th>
                <th>فعال؟</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stays as $stay)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $stay->title }}</td>
                    <td>{{ $stay->city }}</td>
                    <td>{{ number_format($stay->price_per_night) }}</td>
                    <td>{{ $stay->discount_percent }}</td>
                    <td>{{ $stay->is_active ? '✅' : '❌' }}</td>
                    <td>
                        <a href="{{ route('stays.show', $stay->id) }}" class="btn btn-info btn-sm">مشاهده</a>
                        <a href="{{ route('stays.edit', $stay->id) }}" class="btn btn-primary btn-sm">ویرایش</a>

                        <form action="{{ route('stays.destroy', $stay->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>

                        <form action="{{ route('stays.toggleStatus', $stay->id) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-warning btn-sm">
                                {{ $stay->is_active ? 'غیرفعال‌سازی' : 'فعال‌سازی' }}
                            </button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $stays->links() }}
</div>
@endsection
