@php
    // تعیین نقش
    $isAdmin = $isAdmin ?? Auth::guard('admin')->check();
@endphp

<div class="row g-3">

    {{-- عنوان و دسته‌بندی --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">عنوان اقامت‌گاه</label>
        <input type="text" name="title" class="form-control"
            value="{{ old('title', $stay->title ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">دسته‌بندی</label>
        <select name="category" class="form-select">
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ old('category', $stay->category ?? '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- موقعیت جغرافیایی --}}
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

    <div class="col-md-6">
        <label class="form-label">عرض جغرافیایی (Latitude)</label>
        <input type="number" step="0.0000001" name="latitude" class="form-control"
            value="{{ old('latitude', $stay->latitude ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">طول جغرافیایی (Longitude)</label>
        <input type="number" step="0.0000001" name="longitude" class="form-control"
            value="{{ old('longitude', $stay->longitude ?? '') }}">
    </div>

    {{-- مشخصات فیزیکی --}}
    <div class="col-md-4">
        <label class="form-label">متراژ (متر مربع)</label>
        <input type="number" name="area" min="0" class="form-control"
            value="{{ old('area', $stay->area ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">ظرفیت کل</label>
        <input type="number" name="capacity" min="1" class="form-control"
            value="{{ old('capacity', $stay->capacity ?? 1) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">ظرفیت پایه</label>
        <input type="number" name="base_capacity" min="1" class="form-control"
            value="{{ old('base_capacity', $stay->base_capacity ?? 1) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">نفر اضافه</label>
        <input type="number" name="extra_capacity" min="0" class="form-control"
            value="{{ old('extra_capacity', $stay->extra_capacity ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">تعداد اتاق خواب</label>
        <input type="number" name="bedrooms" min="0" class="form-control"
            value="{{ old('bedrooms', $stay->bedrooms ?? 1) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">تخت دو نفره</label>
        <input type="number" name="double_beds" min="0" class="form-control"
            value="{{ old('double_beds', $stay->double_beds ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">تخت تک نفره</label>
        <input type="number" name="single_beds" min="0" class="form-control"
            value="{{ old('single_beds', $stay->single_beds ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">رخت‌خواب زمینی</label>
        <input type="number" name="floor_beds" min="0" class="form-control"
            value="{{ old('floor_beds', $stay->floor_beds ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">توالت ایرانی</label>
        <input type="number" name="iranian_toilets" min="0" class="form-control"
            value="{{ old('iranian_toilets', $stay->iranian_toilets ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">توالت فرنگی</label>
        <input type="number" name="western_toilets" min="0" class="form-control"
            value="{{ old('western_toilets', $stay->western_toilets ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">تعداد حمام</label>
        <input type="number" name="bathrooms" min="0" class="form-control"
            value="{{ old('bathrooms', $stay->bathrooms ?? 0) }}">
    </div>

    {{-- قیمت‌ها و درصدها --}}
    <div class="col-md-4">
        <label class="form-label">قیمت هر نفر (تومان)</label>
        <input type="number" name="price_per_person" min="0" class="form-control"
            value="{{ old('price_per_person', $stay->price_per_person ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">قیمت نفر اضافه (تومان)</label>
        <input type="number" name="extra_person_price" min="0" class="form-control"
            value="{{ old('extra_person_price', $stay->extra_person_price ?? 0) }}">
    </div>

    @if(!$isAdmin)
    <div class="col-md-4">
        <label class="form-label">کمیسیون سایت (%)</label>
        <input type="number" name="site_commission" min="0" max="100" class="form-control"
            value="{{ old('site_commission', $stay->site_commission ?? 10) }}">
    </div>
    @endif

    <div class="col-md-4">
        <label class="form-label">حداکثر تخفیف عادی (%)</label>
        <input type="number" name="max_discount_normal" min="0" max="100" class="form-control"
            value="{{ old('max_discount_normal', $stay->max_discount_normal ?? 20) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">حداکثر تخفیف پیک (%)</label>
        <input type="number" name="max_discount_peak" min="0" max="100" class="form-control"
            value="{{ old('max_discount_peak', $stay->max_discount_peak ?? 10) }}">
    </div>

    {{-- وضعیت --}}
    <div class="col-md-4 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                {{ old('is_active', $stay->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">فعال باشد</label>
        </div>
    </div>

</div>

<hr class="my-4">

{{-- تصاویر اقامتگاه --}}
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

{{-- امکانات --}}
<h5 class="fw-bold mb-3"><i class="bi bi-grid-1x2 text-success me-2"></i> امکانات اقامت‌گاه</h5>

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

{{-- قوانین --}}
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
