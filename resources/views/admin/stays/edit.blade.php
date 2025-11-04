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
                    <option value="house" {{ $stay->type == 'house' ? 'selected' : '' }}>خانه</option>
                    <option value="suite" {{ $stay->type == 'suite' ? 'selected' : '' }}>سوئیت</option>
                    <option value="ecolodge" {{ $stay->type == 'ecolodge' ? 'selected' : '' }}>>بوم‌گردی</option>
                    <option value="motel" {{ $stay->type == 'motel' ? 'selected' : '' }}>مسافرخانه</option>
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
           
            <div class="col-md-4 mb-3">
                <label>تخفیف عادی (%)</label>
                <input type="number" name="normal_discount" class="form-control" value="{{ old('normal_discount', $stay->normal_discount) }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>تخفیف در روزهای پیک (%)</label>
                <input type="number" name="peak_discount" class="form-control"  value="{{ old('peak_discount', $stay->peak_discount) }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>تصاویر فعلی</label>
                <div id="image-gallery" class="d-flex flex-wrap gap-3">
                    @foreach($stay->images as $img)
                        <div class="position-relative image-box" id="image-{{ $img->id }}" style="width:150px">
                            <img src="{{ asset('storage/'.$img->image_path) }}" 
                                class="img-thumbnail w-100 {{ $img->is_main ? 'border border-3 border-primary' : '' }}">
                            <div class="position-absolute top-0 end-0 d-flex flex-column">
                                <button type="button"
                                        class="btn btn-sm btn-danger mb-1"
                                        onclick="deleteImage({{ $img->id }})">×</button>
                                <button type="button"
                                        class="btn btn-sm {{ $img->is_main ? 'btn-primary' : 'btn-secondary' }}"
                                        onclick="setMainImage({{ $img->id }})">
                                    {{ $img->is_main ? '⭐ اصلی' : 'اصلی کن' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label>افزودن تصاویر جدید</label>
                <input type="file" id="newImages" name="images[]" class="form-control" multiple>
            </div>

            <div class="col-md-12 mt-3">
                <button class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('stays.index') }}" class="btn btn-secondary">بازگشت</a>
            </div>
        </div>
    </form>
</div>


@endsection
@push('scripts')

<script>
function deleteImage(id) {
if (!confirm('آیا از حذف این تصویر مطمئن هستید؟')) return;

fetch(`/admin/stay-images/${id}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
    }
})
.then(async res => {
    if (res.ok) {
        // حذف از DOM بدون reload
        const element = document.getElementById(`image-${id}`);
        if (element) element.remove();

        // پیام موفقیت
        const msg = document.createElement('div');
        msg.className = 'alert alert-success mt-2';
        msg.textContent = '✅ تصویر با موفقیت حذف شد';
        document.querySelector('#image-gallery').prepend(msg);
        setTimeout(() => msg.remove(), 2000);
    } else {
        const errorText = await res.text();
        alert('❌ خطا در حذف تصویر: ' + errorText);
    }
})
.catch(err => {
    alert('❌ خطا در ارتباط با سرور');
    console.error(err);
});
}
</script>

<script>
function setMainImage(id) {
    fetch(`/admin/stay-images/${id}/set-main`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // همه تصاویر رو بدون استایل اصلی کن
            document.querySelectorAll('.image-box img').forEach(img => {
                img.classList.remove('border', 'border-3', 'border-primary');
            });

            // همه دکمه‌ها رو برگردون به حالت عادی
            document.querySelectorAll('.image-box .btn-primary').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
                btn.innerText = 'اصلی کن';
            });

            // فقط تصویر انتخابی رو هایلایت کن
            const box = document.getElementById(`image-${data.image_id}`);
            if (box) {
                const img = box.querySelector('img');
                img.classList.add('border', 'border-3', 'border-primary');

                const btn = box.querySelector('.btn-secondary, .btn-primary');
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-primary');
                btn.innerText = '⭐ اصلی';
            }

            // پیام موفقیت
            const msg = document.createElement('div');
            msg.className = 'alert alert-success mt-2';
            msg.textContent = data.message;
            document.querySelector('#image-gallery').prepend(msg);
            setTimeout(() => msg.remove(), 2000);
        } else {
            alert('❌ خطا در تغییر تصویر اصلی');
        }
    })
    .catch(err => {
        console.error(err);
        alert('❌ خطا در ارتباط با سرور هنگام تغییر تصویر اصلی');
    });
}
</script>
<script>
    const newImagesInput = document.getElementById('newImages');
    const gallery = document.getElementById('image-gallery');
    const stayId = {{ $stay->id }};

    newImagesInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        if (!files.length) return;

        const formData = new FormData();
        files.forEach(file => formData.append('images[]', file));

        fetch(`/admin/stays/${stayId}/upload-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                data.images.forEach(img => {
                    // ساختن المان جدید در گالری
                    const div = document.createElement('div');
                    div.className = 'position-relative image-box';
                    div.id = `image-${img.id}`;
                    div.style.width = '150px';
                    div.innerHTML = `
                        <img src="${img.url}" class="img-thumbnail w-100">
                        <div class="position-absolute top-0 end-0 d-flex flex-column">
                            <button type="button" class="btn btn-sm btn-danger mb-1" onclick="deleteImage(${img.id})">×</button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="setMainImage(${img.id})">اصلی کن</button>
                        </div>
                    `;

                    gallery.appendChild(div);
                });

                // پاک کردن فایل‌ها از input
                newImagesInput.value = '';

                // پیام موفقیت
                const msg = document.createElement('div');
                msg.className = 'alert alert-success mt-2';
                msg.textContent = '✅ تصویر(ها) با موفقیت افزوده شد';
                gallery.prepend(msg);
                setTimeout(() => msg.remove(), 2000);
            } else {
                alert('❌ خطا در آپلود تصویر');
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ خطا در ارتباط با سرور هنگام آپلود');
        });
    });
    </script>
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
@endpush
