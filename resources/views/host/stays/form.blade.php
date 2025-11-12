@extends('layouts.host')

@section('title', isset($stay) ? 'ویرایش اقامت‌گاه' : 'افزودن اقامت‌گاه')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-building text-primary me-2"></i>
            {{ isset($stay) ? 'ویرایش اقامت‌گاه' : 'افزودن اقامت‌گاه جدید' }}
        </h4>
        <a href="{{ route('host.stays.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-right-circle me-1"></i> بازگشت
        </a>
    </div>

    @include('admin.partials.messages')

    <div class="card border-0 shadow-sm rounded-4 p-4 fade-in">

        <form method="POST" 
              action="{{ isset($stay) ? route('host.stays.update', $stay->id) : route('host.stays.store') }}">
            @csrf
            @if(isset($stay)) @method('PUT') @endif

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">عنوان اقامت‌گاه</label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $stay->title ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">دسته‌بندی</label>
                    <select name="category" class="form-select">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $stay->category ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">استان</label>
                    <input type="text" name="province" class="form-control"
                        value="{{ old('province', $stay->province ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">شهر</label>
                    <input type="text" name="city" class="form-control"
                        value="{{ old('city', $stay->city ?? '') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">آدرس</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $stay->address ?? '') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ظرفیت</label>
                    <input type="number" name="capacity" min="1" class="form-control"
                        value="{{ old('capacity', $stay->capacity ?? 1) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">قیمت هر نفر (تومان)</label>
                    <input type="number" name="price_per_person" class="form-control"
                        value="{{ old('price_per_person', $stay->price_per_person ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">قیمت نفر اضافه (تومان)</label>
                    <input type="number" name="extra_person_price" class="form-control"
                        value="{{ old('extra_person_price', $stay->extra_person_price ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">حداکثر تخفیف عادی (%)</label>
                    <input type="number" name="max_discount_normal" min="0" max="100" class="form-control"
                        value="{{ old('max_discount_normal', $stay->max_discount_normal ?? 0) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">حداکثر تخفیف پیک (%)</label>
                    <input type="number" name="max_discount_peak" min="0" max="100" class="form-control"
                        value="{{ old('max_discount_peak', $stay->max_discount_peak ?? 0) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">کمیسیون سایت (%)</label>
                    <input type="number" name="site_commission" min="0" max="100" class="form-control"
                        value="{{ old('site_commission', $stay->site_commission ?? 10) }}">
                </div>

                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            {{ old('is_active', $stay->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">فعال باشد</label>
                    </div>
                </div>

            </div>

            <hr class="my-4">

            <h5 class="fw-bold mb-3">
                <i class="bi bi-images text-primary me-2"></i> تصاویر اقامت‌گاه
            </h5>

            <div class="mb-3">
                <input type="file" id="images" multiple class="form-control">
            </div>

            <div id="image-gallery" class="d-flex flex-wrap gap-3">
                @if(isset($stay))
                    @foreach($stay->images as $img)
                        <div id="img-{{ $img->id }}" class="position-relative fade-in">
                            <img src="{{ asset('storage/'.$img->path) }}" class="rounded shadow-sm" width="140" height="100">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="deleteImage({{ $img->id }})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            <hr class="my-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-grid-1x2 text-success me-2"></i> امکانات</h5>

            <div id="facilities-list" class="row g-2">
                @php
                    $defaultFacilities = ['آب', 'برق', 'گاز', 'آشپزخانه', 'تلویزیون', 'مبلمان', 'پارکینگ', 'استخر'];
                @endphp
                @foreach($defaultFacilities as $facility)
                <div class="col-6 col-md-3">
                    <div class="form-check border rounded p-2 bg-light hover-facility">
                        <input type="checkbox" class="form-check-input" name="facilities[]" value="{{ $facility }}"
                            {{ isset($stay) && $stay->facilities->pluck('name')->contains($facility) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $facility }}</label>
                    </div>
                </div>
                @endforeach
            </div>

            <hr class="my-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist text-warning me-2"></i> قوانین اقامت‌گاه</h5>

            @php
                $rules = [
                    'استعمال دخانیات مجاز است.',
                    'پذیرش ۲۴ ساعته مهمان.',
                    'پذیرش گروه‌های مجردی فراهم است.',
                    'ورود حیوانات خانگی مجاز نیست.',
                    'برگزاری مراسم مجاز نیست.',
                ];
            @endphp

            @foreach($rules as $rule)
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="rules[]" value="{{ $rule }}"
                    {{ isset($stay) && $stay->rules->pluck('rule_text')->contains($rule) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $rule }}</label>
            </div>
            @endforeach


            <div class="text-end">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ isset($stay) ? 'به‌روزرسانی اقامت‌گاه' : 'ثبت اقامت‌گاه' }}
                </button>
            </div>

        </form>

    </div>
</div>
@push('styles')
<style>
.fade-in { animation: fadeIn .4s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }

input, textarea, select {
    transition: all 0.2s ease-in-out;
}
input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 5px rgba(13,110,253,0.5);
    border-color: #0d6efd;
}
</style>
@endpush
@push('scripts')
<script>
const stayId = {{ $stay->id ?? 'null' }};

// ✅ آپلود تصاویر
document.getElementById('images').addEventListener('change', function(e) {
    if (!stayId) return alert('ابتدا اقامت‌گاه را ثبت کنید.');

    let formData = new FormData();
    for (const file of e.target.files) formData.append('images[]', file);

    fetch(`/host/stays/${stayId}/upload-image`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            data.images.forEach(img => {
                const div = document.createElement('div');
                div.id = `img-${img.id}`;
                div.className = 'position-relative fade-in';
                div.innerHTML = `
                    <img src="${img.url}" class="rounded shadow-sm" width="140" height="100">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                        onclick="deleteImage(${img.id})"><i class='bi bi-x'></i></button>`;
                document.getElementById('image-gallery').appendChild(div);
            });
        }
    });
});

function deleteImage(id) {
    fetch(`/host/stays/images/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(res => res.json()).then(data => {
        if (data.success) document.getElementById(`img-${id}`).remove();
    });
}
</script>
@endpush
@endsection
