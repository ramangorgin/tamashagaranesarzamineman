@extends('layouts.host')

@section('title', 'اقامت‌گاه‌های من')

@section('content')
<div class="container-fluid">

    {{-- عنوان و دکمه افزودن --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-house-door-fill text-primary me-2"></i> اقامت‌گاه‌های من
        </h4>
        <a href="{{ route('host.stays.create') }}" class="btn btn-primary rounded-pill shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> افزودن اقامت‌گاه
        </a>
    </div>

    {{-- پیام‌ها --}}
    @include('admin.partials.messages')

    {{-- جدول اقامتگاه‌ها --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>دسته‌بندی</th>
                            <th>شهر</th>
                            <th>ظرفیت</th>
                            <th>قیمت</th>
                            <th>وضعیت</th>
                            <th class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stays as $stay)
                        <tr class="fade-in">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $stay->title }}</td>
                            <td>{{ $stay->category }}</td>
                            <td>{{ $stay->city }}</td>
                            <td>{{ $stay->capacity }}</td>
                            <td>{{ number_format($stay->price_per_person) }} تومان</td>
                            <td>
                                <span class="badge {{ $stay->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $stay->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('host.stays.edit', $stay->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('host.stays.destroy', $stay->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف این اقامتگاه مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">هنوز هیچ اقامت‌گاهی ثبت نکرده‌اید.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
.fade-in { animation: fadeIn .4s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(5px);} to {opacity:1; transform:translateY(0);} }
</style>

@endsection
