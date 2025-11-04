@extends('layouts.admin')

@section('title', 'ویرایش اقامت‌گاه')

@section('content')
<div class="container mt-4">
    <h3>ویرایش اقامتگاه: {{ $stay->title }}</h3>

    <form action="{{ route('stays.update', $stay->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>عنوان</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $stay->title) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>نوع اقامتگاه</label>
                <select name="type" class="form-select">
                    <option value="hotel" {{ $stay->type == 'hotel' ? 'selected' : '' }}>هتل</option>
                    <option value="villa" {{ $stay->type == 'villa' ? 'selected' : '' }}>ویلا</option>
                    <option value="apartment" {{ $stay->type == 'apartment' ? 'selected' : '' }}>آپارتمان</option>
                    <option value="ecolodge" {{ $stay->type == 'ecolodge' ? 'selected' : '' }}>بوم‌گردی</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>استان</label>
                <input type="text" name="province" class="form-control" value="{{ old('province', $stay->province) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>شهر</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $stay->city) }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>آدرس کامل</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $stay->address) }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>توضیحات</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $stay->description) }}</textarea>
            </div>

            <div class="col-md-3 mb-3">
                <label>ظرفیت</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $stay->capacity) }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>تعداد اتاق</label>
                <input type="number" name="rooms" class="form-control" value="{{ old('rooms', $stay->rooms) }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>تعداد تخت</label>
                <input type="number" name="beds" class="form-control" value="{{ old('beds', $stay->beds) }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>تعداد سرویس</label>
                <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms', $stay->bathrooms) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>قیمت هر شب (تومان)</label>
                <input type="text" name="price_per_night" id="priceInput" class="form-control"  value="{{ old('price_per_night', $stay->price_per_night) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>درصد تخفیف</label>
                <input type="number" name="discount_percent" class="form-control" value="{{ old('discount_percent', $stay->discount_percent) }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>تصاویر فعلی</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($stay->images as $img)
                        <div class="position-relative" style="width:150px">
                            <img src="{{ asset('storage/'.$img->image_path) }}" class="img-thumbnail w-100">
                            <form action="{{ route('stay-images.destroy', $img->id) }}" method="POST" class="position-absolute top-0 end-0" onsubmit="return confirm('آیا از حذف این تصویر مطمئن هستید؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="col-md-12 mb-3">
                <label>افزودن تصاویر جدید</label>
                <input type="file" name="images[]" class="form-control" multiple>
            </div>

            <div class="col-md-12 mt-3">
                <button class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('stays.index') }}" class="btn btn-secondary">بازگشت</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('priceInput');

    function toPersianDigits(num) {
        const map = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return num.replace(/\d/g, d => map[d]);
    }

    function toEnglishDigits(num) {
        return num.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    }

    input.addEventListener('input', function (e) {
        let raw = toEnglishDigits(e.target.value).replace(/[^\d]/g, '');

        if (!raw) {
            e.target.value = '';
            return;
        }

        let withCommas = raw.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        e.target.value = toPersianDigits(withCommas);
    });

    input.form.addEventListener('submit', function () {
        let englishValue = toEnglishDigits(input.value).replace(/[,٬]/g, '');
        input.value = englishValue;
    });
});
</script>
@endsection
