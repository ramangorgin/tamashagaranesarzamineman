@extends('layouts.admin')
@section('title', 'نمایش اقامت‌گاه')

@section('content')
<div class="container mt-4">
    <h3>جزئیات اقامتگاه: {{ $stay->title }}</h3>

    <div class="card mb-4">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>نوع:</strong> {{ stayTypeToPersian($stay->type) }}
                </div>
                <div class="col-md-6">
                    <strong>شهر:</strong> {{ $stay->province }} - {{ $stay->city }}
                </div>
            </div>

            <div class="mb-3">
                <strong>آدرس:</strong> {{ $stay->address }}
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>ظرفیت:</strong> {{ $stay->capacity }}</div>
                <div class="col-md-3"><strong>اتاق:</strong> {{ $stay->rooms }}</div>
                <div class="col-md-3"><strong>تخت:</strong> {{ $stay->beds }}</div>
                <div class="col-md-3"><strong>سرویس:</strong> {{ $stay->bathrooms }}</div>
            </div>

            <div class="mb-3">
                <strong>توضیحات:</strong>
                <p>{{ $stay->description }}</p>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>قیمت هر شب:</strong> {{ number_format($stay->price_per_night) }} تومان</div>
                <div class="col-md-4"><strong>تخفیف:</strong> {{ $stay->discount_percent }}%</div>
                <div class="col-md-4"><strong>قیمت نهایی:</strong> {{ number_format($stay->final_price) }} تومان</div>
            </div>

            <div class="mb-3">
                <strong>وضعیت:</strong>
                {{ $stay->is_active ? 'فعال ✅' : 'غیرفعال ❌' }}
            </div>

        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">تصاویر اقامتگاه</div>
        <div class="card-body d-flex flex-wrap gap-3">
            @forelse($stay->images as $img)
                <img src="{{ asset('storage/'.$img->image_path) }}" class="img-thumbnail" style="width:200px; height:150px; object-fit:cover;">
            @empty
                <p>هیچ تصویری ثبت نشده است.</p>
            @endforelse
        </div>
    </div>

    <a href="{{ route('stays.index') }}" class="btn btn-secondary">بازگشت</a>
    <a href="{{ route('stays.edit', $stay->id) }}" class="btn btn-primary">ویرایش</a>
    <form action="{{ route('stays.toggleStatus', $stay->id) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')
        <button class="btn btn-warning">
            {{ $stay->is_active ? 'غیرفعال‌سازی' : 'فعال‌سازی' }}
        </button>
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="bi bi-calendar-check"></i> رزرو / اجاره
    </button>
</div>

@endsection
