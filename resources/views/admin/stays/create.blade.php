@extends('layouts.admin')
@section('title', 'ایجاد اقامت‌گاه')
@section('content')
<div class="container mt-4">
    <h3>افزودن اقامتگاه جدید</h3>

    <form action="{{ route('stays.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>عنوان</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>نوع اقامتگاه</label>
                <select name="type" class="form-select">
                    <option value="hotel">هتل</option>
                    <option value="villa">ویلا</option>
                    <option value="apartment">آپارتمان</option>
                    <option value="ecolodge">بوم‌گردی</option>
                    <option value="suite">سوئیت</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>استان</label>
                <input type="text" name="province" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>شهر</label>
                <input type="text" name="city" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
                <label>آدرس کامل</label>
                <input type="text" name="address" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
                <label>توضیحات</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="col-md-4 mb-3">
                <label>ظرفیت</label>
                <input type="number" name="capacity" class="form-control" value="1">
            </div>
            <div class="col-md-4 mb-3">
                <label>تعداد اتاق</label>
                <input type="number" name="rooms" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>تعداد تخت</label>
                <input type="number" name="beds" class="form-control">
            </div>


            <div class="col-md-6 mb-3">
                <label>قیمت هر شب (تومان)</label>
                <input type="text" name="price_per_night" id="priceInput" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>تخفیف عادی (%)</label>
                <input type="number" name="normal_discount" class="form-control" value="0">
            </div>

            <div class="col-md-4 mb-3">
                <label>تخفیف در روزهای پیک (%)</label>
                <input type="number" name="peak_discount" class="form-control" value="0">
            </div>

            <div class="col-md-12 mb-3">
                <label>تصاویر</label>
                <input type="file" name="images[]" class="form-control" multiple>
            </div>

            <div class="col-md-12">
                <button class="btn btn-success">ذخیره</button>
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