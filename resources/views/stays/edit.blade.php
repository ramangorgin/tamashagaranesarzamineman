@extends(($role ?? null)==='admin' ? 'layouts.admin' : 'layouts.host')
@section('title','ویرایش اقامت‌گاه')

@section('breadcrumb')
    @if(($role ?? null)==='admin')
        <li class="breadcrumb-item"><a href="{{ route('admin.stays.index') }}">اقامت‌گاه‌ها</a></li>
        <li class="breadcrumb-item active">ویرایش</li>
    @endif
@endsection

@section('breadcrumb-actions')
    @if(($role ?? null)==='admin')
        <a href="{{ route('stays.show',$stay) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye"></i> مشاهده
        </a>
    @endif
@endsection

@section('content')
<div class="container py-4">
  <div class="card shadow-lg border-0 rounded-4 mx-auto animate__animated animate__fadeInUp" style="max-width:1000px;">
    <div class="card-body p-4">
      <h4 class="fw-bold text-primary text-center mb-4">
        <i class="bi bi-pencil-square me-2"></i> ویرایش اقامت‌گاه
      </h4>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger small mb-3">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      @if($role==='admin')
        <div class="card border-0 mb-3">
          <div class="card-body p-3 d-flex flex-column gap-2">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold"><i class="bi bi-person-badge me-2"></i> انتخاب میزبان</div>
              <div class="small text-muted">می‌توانید میزبان را تغییر دهید</div>
            </div>
            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label small">نام میزبان (جستجو فقط بر اساس نام)</label>
                <input type="text" id="hostSearch" class="form-control" placeholder="مثال: علی رضایی" autocomplete="off">
                <div id="hostSearchResults" class="list-group mt-1" style="max-height:220px;overflow:auto;display:none"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label small">موبایل (فقط جستجو با شماره)</label>
                <input type="text" id="hostPhone" class="form-control" inputmode="tel" dir="ltr" placeholder="09XXXXXXXXX">
              </div>
              <div class="col-md-2 d-grid">
                <label class="form-label small">&nbsp;</label>
                <button type="button" id="btnQuickCreateHost" class="btn btn-outline-primary">ایجاد سریع</button>
              </div>
            </div>
            <div id="hostSelectedBox" class="alert alert-warning d-flex justify-content-between align-items-center small" style="display:none">
              <div>
                <i class="bi bi-person-badge me-2"></i>
                میزبان: <strong id="hostSelName">—</strong>
                <span class="mx-2">|</span>
                موبایل: <span id="hostSelPhone" dir="ltr"></span>
                <span class="mx-2">|</span>
                وضعیت: <span id="hostSelStatus"></span>
              </div>
              <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnChangeHost">تغییر</button>
              </div>
            </div>
          </div>
        </div>
      @endif

      <form id="stayForm" method="POST" action="{{ ($role ?? null)==='admin' ? route('admin.stays.update',$stay) : route('host.stays.update',$stay) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @php $role = $role ?? (auth('admin')->check() ? 'admin' : 'host'); @endphp
        @if($role==='admin')
          <input type="hidden" name="host_id" id="host_id" value="{{ old('host_id', $stay->host_id) }}">
        @endif

        <div class="d-flex flex-wrap justify-content-center mb-4 gap-2 small fw-semibold">
          <div class="step-dot active" data-step="1">مشخصات</div>
          <div class="step-dot" data-step="2">موقعیت</div>
          <div class="step-dot" data-step="3">ظرفیت</div>
          <div class="step-dot" data-step="4">قیمت</div>
          <div class="step-dot" data-step="5">تصاویر</div>
          <div class="step-dot" data-step="6">امکانات</div>
          <div class="step-dot" data-step="7">قوانین</div>
        </div>

        {{-- Step 1 --}}
        <div id="step1">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">عنوان اقامت‌گاه</label>
              <input type="text" name="title" id="title" class="form-control" value="{{ old('title',$stay->title) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">دسته‌بندی</label>
              @php $cats=['hotel','villa','apartment','ecolodge','suite','motel','house']; @endphp
              <select name="category" id="category" class="form-select" required>
                @foreach($cats as $c)
                  <option value="{{ $c }}" @selected(old('category',$stay->category)===$c)>{{ stayTypeToPersian($c) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">توضیحات</label>
              <textarea name="description" id="description" class="form-control">{{ old('description',$stay->description) }}</textarea>
              <small class="text-muted">توضیحات کامل اقامت‌گاه (اختیاری)</small>
            </div>
          </div>
          {{-- Hotel-specific fields --}}
          <div id="hotelFields" class="d-none mt-4">
            <hr>
            <h5 class="mb-3">اطلاعات هتل</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">ستاره هتل</label>
                <select name="star_rating" class="form-select">
                  <option value="">انتخاب کنید</option>
                  @for($i=1; $i<=5; $i++)
                    <option value="{{ $i }}" @selected(old('star_rating', optional($stay->hotel)->star_rating)==$i)>{{ $i }} ستاره</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">شماره پروانه</label>
                <input type="text" name="license_number" id="license_number" class="form-control" value="{{ old('license_number', optional($stay->hotel)->license_number) }}">
              </div>
            </div>
          </div>
          <div class="mt-4 text-end">
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 2 --}}
        <div id="step2" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">استان</label>
              <select name="province_id" id="province" class="form-select" required></select>
              <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name',$stay->province_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">شهر</label>
              <select name="city_id" id="city" class="form-select" required disabled></select>
              <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name',$stay->city_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">بخش / شهرستان</label>
              <select name="county_id" id="county" class="form-select" required disabled></select>
              <input type="hidden" name="county_name" id="county_name" value="{{ old('county_name',$stay->county_name) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">روستا (اختیاری)</label>
              <select name="village_name" id="village" class="form-select" disabled>
                <option value="">(انتخاب کنید)</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">آدرس دقیق</label>
              <textarea name="address" class="form-control" rows="2" required>{{ old('address',$stay->address) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label d-flex justify-content-between">موقعیت روی نقشه
                <small class="text-muted">برای تغییر کلیک کنید</small>
              </label>
              <div class="input-group mb-2">
                  <input 
                      type="text" 
                      id="coordSearch" 
                      class="form-control"
                      placeholder="مختصات کپی شده را جایگذاری کنید">
                  <button class="btn btn-primary" id="applyCoordsBtn">جایگذاری</button>
              </div>

              <div id="map" style="height:380px;border:1px solid #dee2e6;border-radius:14px;"></div>
              <input type="hidden" name="latitude" id="lat" value="{{ old('latitude',$stay->latitude) }}">
              <input type="hidden" name="longitude" id="lng" value="{{ old('longitude',$stay->longitude) }}">
              <div class="small text-muted mt-2">مختصات فعلی: <span id="coordText">{{ $stay->latitude ? $stay->latitude.', '.$stay->longitude : 'انتخاب نشده' }}</span></div>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 3: Capacity --}}
        <div id="step3" class="d-none">
          {{-- Non-hotel capacity fields --}}
          <div id="nonHotelCapacity" class="row g-3">
            <div class="col-md-3">
              <label class="form-label">ظرفیت پایه</label>
              <input type="number" min="1" name="base_capacity" class="form-control" value="{{ old('base_capacity',$stay->base_capacity) }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">ظرفیت کل</label>
              <input type="number" min="1" name="capacity" class="form-control" value="{{ old('capacity',$stay->capacity) }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">نفرات اضافه</label>
              <input type="number" min="0" name="extra_capacity" class="form-control" value="{{ old('extra_capacity',$stay->extra_capacity ?? 0) }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">متراژ (متر)</label>
              <input type="text" name="area" class="form-control" data-price-format placeholder="" value="{{ old('area',$stay->area ? number_format($stay->area) : '') }}">
            </div>
          </div>
          {{-- Hotel room types builder --}}
          <div id="hotelRoomTypes" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">انواع اتاق‌ها</h5>
              <button type="button" class="btn btn-sm btn-primary" id="addRoomTypeBtn">
                <i class="bi bi-plus-lg"></i> افزودن نوع اتاق
              </button>
            </div>
            <div id="roomTypesContainer"></div>
            <input type="hidden" name="room_types_json" id="room_types_json">
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 4: Pricing --}}
        <div id="step4" class="d-none">
          <div class="row g-3" id="pricingFields">
            <div class="col-md-4" id="pricingModeField">
              <label class="form-label">نوع قیمت‌گذاری</label>
              <select name="pricing_mode" id="pricing_mode" class="form-select" required>
                @php $pm = old('pricing_mode',$stay->pricing_mode ?? 'per_person'); @endphp
                <option value="per_person" @selected($pm==='per_person')>به ازای هر نفر</option>
                <option value="per_night" @selected($pm==='per_night')>به ازای هر شب</option>
              </select>
            </div>
            <div class="col-md-4 pricing-per-person" id="pricePerPersonField">
              <label class="form-label">قیمت هر نفر (ظرفیت پایه) <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="price_per_person" id="price_per_person" class="form-control price-field" data-price-format value="{{ old('price_per_person', $stay->price_per_person !== null ? number_format($stay->price_per_person) : '0') }}">
            </div>
            <div class="col-md-4 pricing-per-night d-none">
              <label class="form-label">قیمت هر شب <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="price_per_night" id="price_per_night" class="form-control price-field" data-price-format value="{{ old('price_per_night', $stay->price_per_night !== null ? number_format($stay->price_per_night) : '0') }}">
            </div>
            <div class="col-md-4 extra-person-group" id="extraPersonPriceField">
              <label class="form-label">قیمت نفر اضافه <small class="text-muted">(ریال)</small></label>
              <input type="text" inputmode="numeric" name="extra_person_price" class="form-control price-field" data-price-format value="{{ old('extra_person_price', $stay->extra_person_price ? number_format($stay->extra_person_price) : '0') }}">
            </div>
            @if($role !== 'admin')
              <div class="col-md-4" id="commissionField">
                <label class="form-label d-flex justify-content-between align-items-center">
                  <span>کمیسیون سایت</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary commission-toggle" data-mode="percent" style="font-size:0.75rem;">
                    <i class="bi bi-arrow-repeat"></i> درصد/مبلغ
                  </button>
                </label>
                <div class="input-group commission-input-group" data-mode="percent">
                  <input type="number" name="site_commission" id="site_commission" min="0" max="100" step="0.5" class="form-control commission-percent" value="{{ old('site_commission',$stay->site_commission) }}" required>
                  <span class="input-group-text">%</span>
                </div>
                <div class="input-group commission-input-group d-none" data-mode="price">
                  <input type="text" id="site_commission_price" class="form-control commission-price" data-price-format placeholder="">
                  <span class="input-group-text">ریال</span>
                </div>
                <small class="text-muted d-block mt-1" id="commissionInfo"></small>
              </div>
              <div class="col-md-4" id="minAdjustmentField">
                <label class="form-label d-flex justify-content-between align-items-center">
                  <span>کف تغییر قیمت</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary min-adjustment-toggle" data-mode="percent" style="font-size:0.75rem;">
                    <i class="bi bi-arrow-repeat"></i> درصد/مبلغ
                  </button>
                </label>
                <div class="input-group min-adjustment-input-group" data-mode="percent">
                  <input type="number" name="min_price_adjustment" id="min_price_adjustment" min="0" max="100" step="0.5" class="form-control min-adjustment-percent" value="{{ old('min_price_adjustment',$stay->min_price_adjustment) }}" required>
                  <span class="input-group-text">%</span>
                </div>
                <div class="input-group min-adjustment-input-group d-none" data-mode="price">
                  <input type="text" id="min_price_adjustment_price" class="form-control min-adjustment-price" data-price-format placeholder="">
                  <span class="input-group-text">ریال</span>
                </div>
                <small class="text-muted d-block mt-1" id="minAdjustmentInfo"></small>
              </div>
              <div class="col-md-4" id="maxAdjustmentField">
                <label class="form-label d-flex justify-content-between align-items-center">
                  <span>سقف تغییر قیمت</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary max-adjustment-toggle" data-mode="percent" style="font-size:0.75rem;">
                    <i class="bi bi-arrow-repeat"></i> درصد/مبلغ
                  </button>
                </label>
                <div class="input-group max-adjustment-input-group" data-mode="percent">
                  <input type="number" name="max_price_adjustment" id="max_price_adjustment" min="0" max="100" step="0.5" class="form-control max-adjustment-percent" value="{{ old('max_price_adjustment',$stay->max_price_adjustment) }}" required>
                  <span class="input-group-text">%</span>
                </div>
                <div class="input-group max-adjustment-input-group d-none" data-mode="price">
                  <input type="text" id="max_price_adjustment_price" class="form-control max-adjustment-price" data-price-format placeholder="">
                  <span class="input-group-text">ریال</span>
                </div>
                <small class="text-muted d-block mt-1" id="maxAdjustmentInfo"></small>
              </div>
            @else
              <input type="hidden" name="site_commission" value="0">
              <input type="hidden" name="min_price_adjustment" value="0">
              <input type="hidden" name="max_price_adjustment" value="0">
            @endif
          </div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 5 --}}
        <div id="step5" class="d-none">
          <div class="mb-3">
            <label class="form-label">افزودن تصاویر جدید (حداکثر 10)</label>
            <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple class="form-control">
          </div>
          <h6 class="fw-bold small mb-2">تصاویر فعلی</h6>
          <div class="row g-3 mb-3">
            @forelse($stay->images as $img)
              <div class="col-6 col-md-3">
                <div class="position-relative">
                  <img src="{{ asset($img->path) }}" class="img-fluid rounded-3" style="height:140px;object-fit:cover;">
                  @if($img->is_main)
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-1">اصلی</span>
                  @endif
                  <button type="button" class="btn btn-sm btn-outline-danger position-absolute bottom-0 end-0 m-1 existing-delete" data-id="{{ $img->id }}"><i class="bi bi-trash"></i></button>
                  <button type="button" class="btn btn-sm btn-outline-info position-absolute bottom-0 start-0 m-1 make-main-existing" data-id="{{ $img->id }}">
                    <i class="bi {{ $img->is_main ? 'bi-star-fill' : 'bi-star' }}"></i>
                  </button>
                </div>
              </div>
            @empty
              <p class="small text-muted">تصویری ندارد.</p>
            @endforelse
          </div>
          <h6 class="fw-bold small mb-2">پیش‌نمایش تصاویر جدید</h6>
          <div id="imagesPreview" class="row g-3"></div>
          <input type="hidden" name="main_image_index" id="main_image_index">
          <input type="hidden" name="main_image_existing_id" id="main_image_existing_id" value="{{ optional($stay->images->firstWhere('is_main',true))->id }}">
          <input type="hidden" name="remove_image_ids" id="remove_image_ids">
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 6: Amenities --}}
        <div id="step6" class="d-none">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="form-label mb-0">امکانات اقامت‌گاه</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addAmenityBtn">
              <i class="bi bi-plus-lg"></i> افزودن امکان
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" id="amenitiesTable">
              <thead class="table-light">
                <tr>
                  <th style="width:25%">امکان</th>
                  <th style="width:40%">توضیحات (اختیاری)</th>
                  <th style="width:16%">وضعیت</th>
                  <th style="width:19%">حذف</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <textarea name="amenities_json" id="amenities_json" class="d-none"></textarea>
          <div class="mt-3 small text-muted">امکانات موجود یا غیرموجود در اقامت‌گاه را مشخص کنید</div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="button" class="btn btn-primary next-btn">مرحله بعد <i class="bi bi-arrow-left-short"></i></button>
          </div>
        </div>

        {{-- Step 7: Rules --}}
        <div id="step7" class="d-none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">ساعت ورود</label>
              <input type="time" name="checkin_time" id="checkin_time" class="form-control" value="{{ old('checkin_time',$stay->checkin_time ?? '14:00') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">ساعت خروج</label>
              <input type="time" name="checkout_time" id="checkout_time" class="form-control" value="{{ old('checkout_time',$stay->checkout_time ?? '12:00') }}">
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
            <label class="form-label mb-0">سایر قوانین</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addRuleBtn">
              <i class="bi bi-plus-lg"></i> افزودن قانون
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" id="rulesTable">
              <thead class="table-light">
                <tr>
                  <th style="width:70%">متن قانون</th>
                  <th style="width:16%">وضعیت</th>
                  <th style="width:14%">حذف</th>
                </tr>
              </thead>
              <tbody>
              @foreach($stay->rules ?? [] as $r)
                <tr>
                  <td><input type="text" class="form-control form-control-sm rule-text" placeholder="متن قانون" value="{{ $r->rule_text }}" required></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm {{ $r->is_allowed?'btn-success':'btn-danger' }} toggle-allowed" data-allowed="{{ $r->is_allowed?1:0 }}">
                      <i class="bi {{ $r->is_allowed?'bi-check-circle':'bi-x-circle' }}"></i>
                    </button>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-rule"><i class="bi bi-trash3"></i></button>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <textarea name="rules_json" id="rules_json" class="d-none"></textarea>
          <div class="mt-3 small text-muted">نمونه‌ها: «برگزاری پارتی: ممنوع»، «پخش آهنگ: مجاز»</div>
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary prev-btn"><i class="bi bi-arrow-right-short"></i> قبلی</button>
            <button type="submit" class="btn btn-success">ذخیره تغییرات <i class="bi bi-check2-circle"></i></button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
.step-dot{background:#e9ecef;color:#6c757d;padding:6px 10px;border-radius:18px;min-width:70px;text-align:center;transition:.25s;cursor:pointer;font-size:.75rem;user-select:none;}
.step-dot:hover{background:#dee2e6;transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,.1);}
.step-dot.active{background:#0d6efd;color:#fff;box-shadow:0 0 0 3px rgba(13,110,253,.15);}
.step-dot.active:hover{background:#0b5ed7;transform:translateY(-1px);}
#map{position:relative;overflow:hidden;height:380px;border:1px solid #dee2e6;border-radius:14px;}
/* Prevent global img rules from breaking Leaflet tiles */
.leaflet-container img{max-width:none!important;}
.leaflet-container{z-index:0;}
.image-box{position:relative}
.image-box img{width:100%;height:140px;object-fit:cover;border-radius:10px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.15)}
.main-badge{position:absolute;top:6px;left:6px;background:#ffc107;color:#212529;padding:4px 8px;border-radius:20px;font-size:.7rem;cursor:pointer;display:flex;align-items:center;gap:4px;box-shadow:0 0 0 2px rgba(255,193,7,.4)}
.remove-img{position:absolute;bottom:6px;right:6px;background:#dc3545;color:#fff;border:none;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer}
.rule-row-removed{opacity:.4;text-decoration:line-through}
.price-field{text-align:left;direction:ltr}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
(function(){
  const role='{{ $role }}';
  const totalSteps = 7; // Edit mode always has 7 steps (no review step)
  let current=1;
  let map, marker=null;
  
  // Define roomTypesJsonField at top level so it's accessible everywhere
  const roomTypesJsonField = document.getElementById('room_types_json');
  function initMap(){
    if(map) return;
    const latField=document.getElementById('lat');
    const lngField=document.getElementById('lng');
    const startLat = latField.value ? parseFloat(latField.value) : 32;
    const startLng = lngField.value ? parseFloat(lngField.value) : 53;
    const zoom = latField.value ? 12 : 5;
    map = L.map('map').setView([startLat,startLng],zoom);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
    if(latField.value && lngField.value){ marker=L.marker([startLat,startLng]).addTo(map); }
    map.on('click',e=>{
      const {lat,lng}=e.latlng;
      if(marker) marker.setLatLng(e.latlng); else marker=L.marker(e.latlng).addTo(map);
      latField.value=lat.toFixed(6); lngField.value=lng.toFixed(6);
      document.getElementById('coordText').textContent=latField.value+', '+lngField.value;
      Swal.fire({toast:true,icon:'success',title:'مختصات ثبت شد',position:'top',showConfirmButton:false,timer:1300});
    });
  }
  function setLocationFromSearch() {
    const input = document.getElementById("coordSearch").value.trim();

    if (!input) {
        Swal.fire({icon:'warning', title:'لطفا موقعیت را وارد کنید'});
        return;
    }

    // Extract numbers (supports formats like "35.7,51.3" or "lat=35.7 lng=51.3")
    const nums = input.match(/[-]?\d+(\.\d+)?/g);

    if (!nums || nums.length < 2) {
        Swal.fire({icon:'error', title:'موقعیت معتبر نیست'});
        return;
    }

    const lat = parseFloat(nums[0]);
    const lng = parseFloat(nums[1]);

    if (isNaN(lat) || isNaN(lng)) {
        Swal.fire({icon:'error', title:'موقعیت معتبر نیست'});
        return;
    }

    // Update map
    const latlng = [lat, lng];
    map.setView(latlng, 14);

    if (marker) marker.setLatLng(latlng);
    else marker = L.marker(latlng).addTo(map);

    // Update hidden fields
    document.getElementById('lat').value = lat.toFixed(6);
    document.getElementById('lng').value = lng.toFixed(6);

    Swal.fire({
        toast: true,
        position: 'top',
        icon: 'success',
        title: 'موقعیت با موفقیت ثبت شد',
        showConfirmButton: false,
        timer: 1300
    });
  }
  // Add event listeners for coordinate input
  document.getElementById("coordSearch").addEventListener("keypress", e => {
    if (e.key === "Enter") setLocationFromSearch();
  });

  // Add click event listener for the apply button
  document.getElementById("applyCoordsBtn").addEventListener("click", e => {
    e.preventDefault();
    setLocationFromSearch();
  });


  function show(step){
    for(let i=1;i<=totalSteps;i++){
      const el=document.getElementById('step'+i);
      if(!el) continue;
      if(i===step){ el.classList.remove('d-none'); el.style.display='block'; }
      else { el.style.display='none'; }
      const dot=document.querySelector('.step-dot[data-step="'+i+'"]');
      dot && dot.classList.toggle('active', i===step);
    }
    current=step;
    if(step===2){ initMap(); setTimeout(()=> map.invalidateSize(), 150); }
    if(step===4){ 
      setTimeout(()=>{ 
        if(typeof togglePricingFields === 'function') togglePricingFields();
        if(typeof updateCommissionValidation === 'function') updateCommissionValidation();
      }, 100); 
    }
    // Remove auto-scroll - commented out
    // window.scrollTo({top:0,behavior:'smooth'});
  }
  document.querySelectorAll('.next-btn').forEach(b=>b.addEventListener('click',()=>{
    if(!validateStep(current)) return;
    if(current<totalSteps) show(current+1);
    if(current===6) syncAmenitiesJson();
    if(current===7) syncRulesJson();
  }));
  document.querySelectorAll('.prev-btn').forEach(b=>b.addEventListener('click',()=>{ if(current>1) show(current-1); }));
  
  // Make step dots clickable - allow navigation to any step
  document.querySelectorAll('.step-dot').forEach(d=> {
    d.style.cursor = 'pointer';
    d.addEventListener('click',()=>{
      const targetStep = parseInt(d.dataset.step);
      if(targetStep === current) return;
      
      // If going forward, validate all previous steps
      if(targetStep > current) {
        let allValid = true;
        for(let i = 1; i < targetStep; i++) {
          if(!validateStep(i, true)) {
            allValid = false;
            break;
          }
        }
        if(!allValid) {
          Swal.fire({
            icon: 'warning',
            title: 'لطفاً مراحل قبلی را تکمیل کنید',
            text: 'برای رفتن به این مرحله، ابتدا باید مراحل قبلی را به درستی تکمیل کنید.'
          });
          return;
        }
      }
      
      // Navigate to the step
      show(targetStep);
      if(targetStep === 6) syncAmenitiesJson();
      if(targetStep === 7) syncRulesJson();
    });
  });

  function validateStep(step, silent = false){
    if(step===2 && !document.getElementById('lat').value){
      if(!silent) Swal.fire({icon:'warning',title:'مختصات انتخاب نشده'}); 
      return false;
    }
    if(step===4 && role !== 'admin') {
      // Validate commission and price adjustments
      function getBasePrice() {
        const isHotel = categorySelect && categorySelect.value === 'hotel';
        if (isHotel) {
          const roomTypes = document.querySelectorAll('.room-type-card');
          if (roomTypes.length === 0) return 0;
          let totalPrice = 0;
          let count = 0;
          roomTypes.forEach(card => {
            const priceInput = card.querySelector('.room-type-price');
            if (priceInput && priceInput.value) {
              const price = parseInt(priceInput.value.replace(/,/g, '')) || 0;
              if (price > 0) {
                totalPrice += price;
                count++;
              }
            }
          });
          return count > 0 ? Math.round(totalPrice / count) : 0;
        } else {
          const modeSel = document.getElementById('pricing_mode');
          const isPerNight = modeSel && modeSel.value === 'per_night';
          const priceInput = isPerNight ? document.getElementById('price_per_night') : document.getElementById('price_per_person');
          if (priceInput && priceInput.value) {
            return parseInt(priceInput.value.replace(/,/g, '')) || 0;
          }
        }
        return 0;
      }
      
      const basePrice = getBasePrice();
      if (basePrice > 0) {
        const commissionPercent = parseFloat(document.getElementById('site_commission').value) || 0;
        const minAdjustmentPercent = parseFloat(document.getElementById('min_price_adjustment').value) || 0;
        
        if (minAdjustmentPercent > commissionPercent) {
          if (!silent) {
            Swal.fire({
              icon: 'error',
              title: 'خطا در کف تغییر قیمت',
              text: `کف تغییر قیمت (${minAdjustmentPercent}%) نمی‌تواند بیشتر از درصد کمیسیون (${commissionPercent}%) باشد.`
            });
          }
          return false;
        }
      }
    }
    if(step===5){ // images limit
      const inputEl=document.getElementById('imagesInput');
      const filesLen = inputEl?.files?.length || 0;
      if(filesLen>10){ 
        if(!silent) Swal.fire({icon:'error',title:'حداکثر 10 تصویر'}); 
        return false; 
      }
    }
    const container=document.getElementById('step'+step);
    let invalid=false;
    container.querySelectorAll('[required]').forEach(inp=>{
      if(!inp.value){ 
        inp.classList.add('is-invalid'); 
        invalid=true; 
      } else { 
        inp.classList.remove('is-invalid'); 
      }
    });
    if(invalid){ 
      if(!silent) Swal.fire({icon:'error',title:'فیلدهای ضروری خالی است'}); 
      return false; 
    }
    return true;
  }

  // Price formatting (ریال) with commas
  function formatPrice(val){
    val = val.replace(/[^\d]/g,'');
    if(!val) return '0';
    return val.replace(/\B(?=(\d{3})+(?!\d))/g,',');
  }
  document.querySelectorAll('[data-price-format]').forEach(inp=>{
    inp.addEventListener('input',()=> {
      const caret=inp.selectionStart;
      inp.value=formatPrice(inp.value);
      inp.setSelectionRange(caret,caret);
    });
    inp.addEventListener('blur',()=>{ inp.value=formatPrice(inp.value); });
  });

  // Pricing mode toggle
  (function(){
    const modeSel=document.getElementById('pricing_mode');
    const perPerson=document.querySelectorAll('.pricing-per-person');
    const perNight=document.querySelectorAll('.pricing-per-night');
    const pricePerPerson=document.getElementById('price_per_person');
    const pricePerNight=document.getElementById('price_per_night');
    const extraGroup=document.querySelectorAll('.extra-person-group');
    const extraInput=document.querySelector('input[name="extra_person_price"]');
    function syncUI(){
      const byNight = modeSel.value==='per_night';
      perPerson.forEach(el=> el.classList.toggle('d-none', byNight));
      perNight.forEach(el=> el.classList.toggle('d-none', !byNight));
      // required swap
      if(pricePerPerson) pricePerPerson.toggleAttribute('required', !byNight);
      if(pricePerNight) pricePerNight.toggleAttribute('required', byNight);
      // extra person visibility (hidden for per-night)
      extraGroup.forEach(el=> el.classList.toggle('d-none', byNight));
      if(extraInput){
        extraInput.disabled = byNight;
        if(byNight) extraInput.value='';
      }
      // Update commission/adjustment calculations when price mode changes
      if(typeof updateCommissionValidation === 'function') updateCommissionValidation();
    }
    modeSel?.addEventListener('change', syncUI);
    syncUI();
  })();

  // Capacity constraints: extra_capacity <= capacity - base_capacity
  (function(){
    const capacityInp = document.querySelector('input[name="capacity"]');
    const baseInp = document.querySelector('input[name="base_capacity"]');
    const extraInp = document.querySelector('input[name="extra_capacity"]');
    if(!capacityInp || !baseInp || !extraInp) return;
    function toInt(v){ const n=parseInt(String(v||'').replace(/[^\d]/g,''),10); return isNaN(n)?0:n; }
    function updateMax(){
      const cap = toInt(capacityInp.value);
      const base = toInt(baseInp.value);
      const maxExtra = Math.max(0, cap - base);
      extraInp.setAttribute('max', String(maxExtra));
      const cur = toInt(extraInp.value);
      if(cur > maxExtra){ extraInp.value = String(maxExtra); }
    }
    ['input','change','blur'].forEach(ev=>{
      capacityInp.addEventListener(ev, updateMax);
      baseInp.addEventListener(ev, updateMax);
      extraInp.addEventListener(ev, updateMax);
    });
    updateMax();
  })();

  // Geo cascading (static JSON like create.blade.php)
  const provinceSel=document.getElementById('province'),
        citySel=document.getElementById('city'),
        countySel=document.getElementById('county'),
        villageSel=document.getElementById('village'),
        hProvince=document.getElementById('province_name'),
        hCity=document.getElementById('city_name'),
        hCounty=document.getElementById('county_name');
  function opt(v,t){return `<option value="${v}">${t}</option>`;}
  function reset(sel,ph,disable=true){ sel.innerHTML=opt('',ph); if(disable) sel.setAttribute('disabled','disabled'); else sel.removeAttribute('disabled'); }
  let DATA_PROVINCES=null, DATA_CITIES=null, DATA_COUNTIES=null, DATA_VILLAGES=null;
  const URLS={
    provinces: "{{ asset('data/provinces.json') }}",
    cities: "{{ asset('data/provinces_cities.json') }}",
    counties: "{{ asset('data/provinces_cities_counties.json') }}",
    villages: "{{ asset('data/provinces_cities_counties_villages.json') }}",
  };
  const FALLBACK={
    provinces: "{{ route('geo.provinces') }}",
    cities: (pid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities",
    counties: (pid,cid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities/"+cid+"/counties",
    villages: (pid,cid,coid)=> "{{ url('/geo/provinces') }}/"+pid+"/cities/"+cid+"/counties/"+coid+"/villages",
  };
  const oldProvince="{{ old('province_id',$stay->province_id) }}",
        oldCity="{{ old('city_id',$stay->city_id) }}",
        oldCounty="{{ old('county_id',$stay->county_id) }}",
        oldVillage="{{ old('village_name',$stay->village_name) }}";

  async function loadProvinces(){
    try{
      if(!DATA_PROVINCES){ DATA_PROVINCES = await fetch(URLS.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
      provinceSel.innerHTML=opt('','انتخاب استان');
      DATA_PROVINCES.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.provinceId,p.provinceName)) );
      provinceSel.removeAttribute('disabled');
      if(oldProvince){ provinceSel.value=oldProvince; hProvince.value="{{ old('province_name',$stay->province_name) }}"; provinceSel.dispatchEvent(new Event('change')); }
    }catch(e){
      try{
        const rows = await fetch(FALLBACK.provinces,{headers:{'Accept':'application/json'}}).then(r=>r.json());
        provinceSel.innerHTML=opt('','انتخاب استان'); rows.forEach(p=> provinceSel.insertAdjacentHTML('beforeend', opt(p.id||p.provinceId,p.name||p.provinceName)) );
        provinceSel.removeAttribute('disabled');
        if(oldProvince){ provinceSel.value=oldProvince; hProvince.value="{{ old('province_name',$stay->province_name) }}"; provinceSel.dispatchEvent(new Event('change')); }
      }catch{ provinceSel.innerHTML=opt('', 'خطا در بارگذاری استان‌ها'); }
    }
  }

  provinceSel.addEventListener('change',()=>{
    const pid=provinceSel.value; hProvince.value = provinceSel.options[provinceSel.selectedIndex]?.text || '';
    reset(citySel,'در حال بارگذاری...',false); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!pid){ reset(citySel,'انتخاب شهر'); return; }
    (async ()=>{
      try{
        if(!DATA_CITIES){ DATA_CITIES = await fetch(URLS.cities,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const cities = DATA_CITIES.filter(row=>row.provinceId===pid);
        citySel.innerHTML=opt('','انتخاب شهر');
        cities.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.cityId,c.cityName)) );
        citySel.removeAttribute('disabled');
        if(oldCity){ citySel.value=oldCity; hCity.value="{{ old('city_name',$stay->city_name) }}"; citySel.dispatchEvent(new Event('change')); }
      }catch{
        try{
          const rows = await fetch(FALLBACK.cities(pid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          citySel.innerHTML=opt('','انتخاب شهر'); rows.forEach(c=> citySel.insertAdjacentHTML('beforeend', opt(c.id||c.cityId,c.name||c.cityName)) );
          citySel.removeAttribute('disabled');
          if(oldCity){ citySel.value=oldCity; hCity.value="{{ old('city_name',$stay->city_name) }}"; citySel.dispatchEvent(new Event('change')); }
        }catch{ citySel.innerHTML=opt('', 'خطا در بارگذاری شهرها'); }
      }
    })();
  });

  citySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value; hCity.value = citySel.options[citySel.selectedIndex]?.text || '';
    reset(countySel,'در حال بارگذاری...',false); reset(villageSel,'ابتدا بخش را انتخاب کنید');
    if(!cid){ reset(countySel,'انتخاب بخش'); return; }
    (async ()=>{
      try{
        if(!DATA_COUNTIES){ DATA_COUNTIES = await fetch(URLS.counties,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const counties = DATA_COUNTIES.filter(row=>row.provinceId===pid && row.cityId===cid);
        countySel.innerHTML=opt('','انتخاب بخش/شهرستان');
        counties.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.countyId,c.countyName)) );
        countySel.removeAttribute('disabled');
        if(oldCounty){ countySel.value=oldCounty; hCounty.value="{{ old('county_name',$stay->county_name) }}"; countySel.dispatchEvent(new Event('change')); }
      }catch{
        try{
          const rows = await fetch(FALLBACK.counties(pid,cid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          countySel.innerHTML=opt('','انتخاب بخش/شهرستان'); rows.forEach(c=> countySel.insertAdjacentHTML('beforeend', opt(c.id||c.countyId,c.name||c.countyName)) );
          countySel.removeAttribute('disabled');
          if(oldCounty){ countySel.value=oldCounty; hCounty.value="{{ old('county_name',$stay->county_name) }}"; countySel.dispatchEvent(new Event('change')); }
        }catch{ countySel.innerHTML=opt('', 'خطا در بارگذاری بخش'); }
      }
    })();
  });

  countySel.addEventListener('change',()=>{
    const pid=provinceSel.value, cid=citySel.value, coid=countySel.value; hCounty.value = countySel.options[countySel.selectedIndex]?.text || '';
    reset(villageSel,'در حال بارگذاری...',false);
    if(!coid){ reset(villageSel,'(اختیاری) انتخاب روستا'); return; }
    (async ()=>{
      try{
        if(!DATA_VILLAGES){ DATA_VILLAGES = await fetch(URLS.villages,{headers:{'Accept':'application/json'}}).then(r=>r.json()); }
        const villages = DATA_VILLAGES.filter(row=>row.provinceId===pid && row.cityId===cid && row.countyId===coid);
        villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
        if(!villages.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
        else villages.forEach(v=> { if(v.villageName && v.villageName.trim()) villageSel.insertAdjacentHTML('beforeend', opt(v.villageName,v.villageName)); });
        villageSel.removeAttribute('disabled');
        if(oldVillage){ villageSel.value=oldVillage; }
      }catch{
        try{
          const rows = await fetch(FALLBACK.villages(pid,cid,coid),{headers:{'Accept':'application/json'}}).then(r=>r.json());
          villageSel.innerHTML=opt('', '(اختیاری) انتخاب روستا');
          if(!rows.length) villageSel.insertAdjacentHTML('beforeend', opt('', 'روستایی ثبت نشده'));
          else rows.forEach(v=> { const name=v.name||v.villageName; if(name && name.trim()) villageSel.insertAdjacentHTML('beforeend', opt(name,name)); });
          villageSel.removeAttribute('disabled');
          if(oldVillage){ villageSel.value=oldVillage; }
        }catch{ villageSel.innerHTML=opt('', 'خطا در بارگذاری روستاها'); }
      }
    })();
  });

  reset(provinceSel,'در حال بارگذاری...',false); reset(citySel,'ابتدا استان را انتخاب کنید'); reset(countySel,'ابتدا شهر را انتخاب کنید'); reset(villageSel,'ابتدا بخش را انتخاب کنید');
  loadProvinces();

  // Images
  const imagesInput=document.getElementById('imagesInput');
  const preview=document.getElementById('imagesPreview');
  const mainIndex=document.getElementById('main_image_index');
  const mainExisting=document.getElementById('main_image_existing_id');
  const removeIds=document.getElementById('remove_image_ids');
  let removed=[];
  imagesInput.addEventListener('change',()=>{
    preview.innerHTML=''; const files=[...imagesInput.files].slice(0,10);
    files.forEach((f,i)=>{
      const reader=new FileReader();
      reader.onload=e=>{
        const col=document.createElement('div');
        col.className='col-6 col-md-3';
        col.innerHTML=`<div class="image-box">
          <img src="${e.target.result}" alt="">
          <div class="position-absolute top-0 start-0 m-1 p-1 px-2 rounded-3 bg-light small make-main-new" data-index="${i}" style="cursor:pointer"><i class="bi bi-star${i===0?' -fill':''}"></i></div>
        </div>`;
        preview.appendChild(col);
      };
      reader.readAsDataURL(f);
    });
    mainIndex.value='0';
  });
  preview.addEventListener('click',e=>{
    const btn=e.target.closest('.make-main-new');
    if(btn){
      const idx=btn.dataset.index;
      mainIndex.value=idx; mainExisting.value='';
      preview.querySelectorAll('.make-main-new i').forEach(i=>i.className='bi bi-star');
      btn.querySelector('i').className='bi bi-star-fill';
    }
  });
  document.querySelectorAll('.existing-delete').forEach(b=>{
    b.addEventListener('click',()=>{
      const id=b.dataset.id;
      if(!removed.includes(id)) removed.push(id);
      b.closest('.col-6,.col-md-3').style.opacity=.4;
      removeIds.value=removed.join(',');
    });
  });
  document.querySelectorAll('.make-main-existing').forEach(b=>{
    b.addEventListener('click',()=>{
      mainExisting.value=b.dataset.id; mainIndex.value='';
      document.querySelectorAll('.make-main-existing i').forEach(i=>i.className='bi bi-star');
      b.querySelector('i').className='bi bi-star-fill';
    });
  });


  // Rules dynamic
  const rulesTableBody=document.querySelector('#rulesTable tbody');
  const addRuleBtn=document.getElementById('addRuleBtn');
  const rulesJsonField=document.getElementById('rules_json');
  let ruleCounter=rulesTableBody.querySelectorAll('tr').length;
  addRuleBtn.addEventListener('click',()=>{
    if(ruleCounter>=20){ Swal.fire({icon:'warning',title:'حداکثر 20 قانون'}); return; }
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td><input type="text" class="form-control form-control-sm rule-text" placeholder="متن قانون" required></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-success toggle-allowed" data-allowed="1"><i class="bi bi-check-circle"></i></button>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-rule"><i class="bi bi-trash3"></i></button>
      </td>`;
    rulesTableBody.appendChild(tr);
    ruleCounter++;
  });
  rulesTableBody.addEventListener('click',e=>{
    const btn=e.target.closest('.toggle-allowed');
    if(btn){
      const allowed=btn.dataset.allowed==='1';
      btn.dataset.allowed=allowed?'0':'1';
      btn.classList.toggle('btn-success', !allowed);
      btn.classList.toggle('btn-danger', allowed);
      btn.innerHTML=allowed?'<i class="bi bi-x-circle"></i>':'<i class="bi bi-check-circle"></i>';
    }
    const remove=e.target.closest('.remove-rule');
    if(remove){
      const tr=remove.closest('tr');
      tr.classList.add('rule-row-removed');
      setTimeout(()=>{ tr.remove(); ruleCounter--; },250);
    }
  });
  function syncRulesJson(){
    const rules=[];
    rulesTableBody.querySelectorAll('tr').forEach(tr=>{
      const text=tr.querySelector('.rule-text')?.value?.trim();
      if(!text) return;
      rules.push({
        rule_text:text,
        is_allowed: tr.querySelector('.toggle-allowed').dataset.allowed==='1'
      });
    });
    rulesJsonField.value=JSON.stringify(rules);
  }

  // Amenities dynamic
  const amenitiesTableBody=document.querySelector('#amenitiesTable tbody');
  const addAmenityBtn=document.getElementById('addAmenityBtn');
  const amenitiesJsonField=document.getElementById('amenities_json');
  let amenityCounter=0;
  
  // Predefined amenities list
  const predefinedAmenities = [
    'پارکینگ', 'سیستم گرمایشی', 'سیستم سرمایش', 'تلویزیون', 'مبلمان', 'آسانسور', 
    'اینترنت', 'سرایدار/نگهبان', 'سرو غذا', 'یخچال', 'اجاق گاز', 'وسایل آشپزخانه',
    'میز غذاخوری', 'حمام', 'توالت ایرانی', 'توالت فرنگی', 'اقلام بهداشتی',
    'تلفن ثابت', 'ماشین لباسشویی', 'مایکروفر', 'اتو', 'سشوار', 'جارو برقی',
    'کباب پز', 'صبحانه رایگان', 'استخر', 'جکوزی', 'سونا', 'لابی', 'رستوران',
    'صبحانه', 'پذیرش 24 ساعته'
  ];
  
  addAmenityBtn.addEventListener('click',()=>{
    if(amenityCounter>=50){ Swal.fire({icon:'warning',title:'حداکثر 50 امکان'}); return; }
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td>
        <select class="form-select form-select-sm amenity-name" required>
          <option value="">انتخاب امکان</option>
          ${predefinedAmenities.map(a => `<option value="${a}">${a}</option>`).join('')}
        </select>
      </td>
      <td><input type="text" class="form-control form-control-sm amenity-description" placeholder="توضیحات اختیاری"></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-success toggle-amenity" data-has="1"><i class="bi bi-check-circle"></i></button>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-amenity"><i class="bi bi-trash3"></i></button>
      </td>`;
    amenitiesTableBody.appendChild(tr);
    amenityCounter++;
  });
  amenitiesTableBody.addEventListener('click',e=>{
    const btn=e.target.closest('.toggle-amenity');
    if(btn){
      const has=btn.dataset.has==='1';
      btn.dataset.has=has?'0':'1';
      btn.classList.toggle('btn-success', !has);
      btn.classList.toggle('btn-danger', has);
      btn.innerHTML=has?'<i class="bi bi-x-circle"></i>':'<i class="bi bi-check-circle"></i>';
    }
    const remove=e.target.closest('.remove-amenity');
    if(remove){
      const tr=remove.closest('tr');
      tr.classList.add('rule-row-removed');
      setTimeout(()=>{ tr.remove(); amenityCounter--; },250);
    }
  });
  function syncAmenitiesJson(){
    const amenities=[];
    amenitiesTableBody.querySelectorAll('tr').forEach(tr=>{
      const name=tr.querySelector('.amenity-name')?.value?.trim();
      if(!name) return;
      amenities.push({
        name:name,
        description: tr.querySelector('.amenity-description')?.value?.trim() || '',
        has: tr.querySelector('.toggle-amenity').dataset.has==='1'
      });
    });
    amenitiesJsonField.value=JSON.stringify(amenities);
  }

  // Submit handler: convert price strings (remove commas) and handle hotel room types
  document.getElementById('stayForm').addEventListener('submit',function(e){
    try {
      if(!validateStep(current)){ e.preventDefault(); return; }
      syncAmenitiesJson();
      syncRulesJson();
      
      // Update description from CKEditor
      if (typeof descriptionEditor !== 'undefined' && descriptionEditor && descriptionEditor.getData) {
        document.getElementById('description').value = descriptionEditor.getData();
      }
      
      // Convert commission/adjustment price inputs to percentages if in price mode
      function getBasePrice() {
        const isHotel = categorySelect && categorySelect.value === 'hotel';
        if (isHotel) {
          const roomTypes = document.querySelectorAll('.room-type-card');
          if (roomTypes.length === 0) return 0;
          let totalPrice = 0;
          let count = 0;
          roomTypes.forEach(card => {
            const priceInput = card.querySelector('.room-type-price');
            if (priceInput && priceInput.value) {
              const price = parseInt(priceInput.value.replace(/,/g, '')) || 0;
              if (price > 0) {
                totalPrice += price;
                count++;
              }
            }
          });
          return count > 0 ? Math.round(totalPrice / count) : 0;
        } else {
          const modeSel = document.getElementById('pricing_mode');
          const isPerNight = modeSel && modeSel.value === 'per_night';
          const priceInput = isPerNight ? document.getElementById('price_per_night') : document.getElementById('price_per_person');
          if (priceInput && priceInput.value) {
            return parseInt(priceInput.value.replace(/,/g, '')) || 0;
          }
        }
        return 0;
      }
      
      function priceToPercent(price, basePrice) {
        if (!basePrice || !price) return 0;
        return parseFloat(((price / basePrice) * 100).toFixed(2));
      }
      
      const basePrice = getBasePrice();
      if (basePrice > 0) {
        // Commission
        const commissionToggle = document.querySelector('.commission-toggle');
        if (commissionToggle && commissionToggle.dataset.mode === 'price') {
          const priceInput = document.getElementById('site_commission_price');
          if (priceInput && priceInput.value) {
            const price = parseInt(priceInput.value.replace(/,/g, '')) || 0;
            document.getElementById('site_commission').value = priceToPercent(price, basePrice);
          }
        }
        
        // Min adjustment
        const minToggle = document.querySelector('.min-adjustment-toggle');
        if (minToggle && minToggle.dataset.mode === 'price') {
          const priceInput = document.getElementById('min_price_adjustment_price');
          if (priceInput && priceInput.value) {
            const price = parseInt(priceInput.value.replace(/,/g, '')) || 0;
            document.getElementById('min_price_adjustment').value = priceToPercent(price, basePrice);
          }
        }
        
        // Max adjustment
        const maxToggle = document.querySelector('.max-adjustment-toggle');
        if (maxToggle && maxToggle.dataset.mode === 'price') {
          const priceInput = document.getElementById('max_price_adjustment_price');
          if (priceInput && priceInput.value) {
            const price = parseInt(priceInput.value.replace(/,/g, '')) || 0;
            document.getElementById('max_price_adjustment').value = priceToPercent(price, basePrice);
          }
        }
      }
      
      // Convert price strings (remove commas)
      this.querySelectorAll('[data-price-format]').forEach(inp=>{
        inp.value=inp.value.replace(/,/g,'');
      });
      
      // Handle hotel room types
      if (categorySelect && categorySelect.value === 'hotel') {
        if (typeof syncRoomTypesJson === 'function') {
          syncRoomTypesJson();
        }
        if (!roomTypesJsonField) {
          e.preventDefault();
          Swal.fire({icon: 'error', title: 'خطا در فرم', text: 'فیلد room_types_json یافت نشد'});
          return false;
        }
        const roomTypesJson = roomTypesJsonField.value;
        if (!roomTypesJson || roomTypesJson === '[]') {
          e.preventDefault();
          Swal.fire({icon: 'error', title: 'حداقل یک نوع اتاق باید تعریف شود'});
          return false;
        }
        
        // Remove any existing room type hidden inputs
        this.querySelectorAll('input[name^="room_types"]').forEach(el => el.remove());
        
        // Convert room_types_json to room_types array for backend
        const roomTypes = JSON.parse(roomTypesJson);
        roomTypes.forEach((rt, index) => {
          // Add regular fields
          ['title', 'capacity', 'base_capacity', 'extra_capacity', 'area', 'price_per_night', 'total_rooms'].forEach(key => {
            if (rt[key] !== undefined && rt[key] !== null) {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = `room_types[${index}][${key}]`;
              input.value = rt[key];
              this.appendChild(input);
            }
          });
          
          // Add beds array
          if (rt.beds && Array.isArray(rt.beds)) {
            rt.beds.forEach((bed, bedIndex) => {
              const bedIdInput = document.createElement('input');
              bedIdInput.type = 'hidden';
              bedIdInput.name = `room_types[${index}][beds][${bedIndex}][bed_id]`;
              bedIdInput.value = bed.bed_id;
              this.appendChild(bedIdInput);
              
              const bedQtyInput = document.createElement('input');
              bedQtyInput.type = 'hidden';
              bedQtyInput.name = `room_types[${index}][beds][${bedIndex}][quantity]`;
              bedQtyInput.value = bed.quantity;
              this.appendChild(bedQtyInput);
            });
          }
        });
      }
    } catch (error) {
      console.error('Form submission error:', error);
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'خطا در ارسال فرم',
        text: 'لطفاً خطاهای فرم را بررسی کنید: ' + (error.message || 'خطای نامشخص')
      });
      return false;
    }
  });

  // Admin host selection logic
  (function(){
    const role='{{ $role }}';
    if(role!=='admin') return;
    const hostId=document.getElementById('host_id');
    const box=document.getElementById('hostSelectedBox');
    const selName=document.getElementById('hostSelName');
    const selPhone=document.getElementById('hostSelPhone');
    const selStatus=document.getElementById('hostSelStatus');
    function statusToFa(s){
      switch(String(s||'').toLowerCase()){
        case 'approved': return 'تأیید شده';
        case 'pending': return 'در انتظار بررسی';
        case 'rejected': return 'رد شده';
        default: return s||'—';
      }
    }
    function bindHost(h){
      hostId.value=h.id;
      selName.textContent=h.name||'—';
      selPhone.textContent=h.phone||'';
      selStatus.textContent=statusToFa(h.status);
      box.style.display='flex';
    }
    function clearHost(){
      hostId.value='';
      box.style.display='none';
      hostSearch.value='';
      results.innerHTML='';
      results.style.display='none';
      hostPhone.value='';
      Swal.fire({toast:true,icon:'info',title:'میزبان پاک شد. دوباره انتخاب کنید',position:'top',timer:1400,showConfirmButton:false});
      setTimeout(()=> hostSearch.focus(), 50);
    }

    const hostSearch=document.getElementById('hostSearch');
    const results=document.getElementById('hostSearchResults');
    let t=null;
    hostSearch.addEventListener('input',()=>{
      const cleaned = hostSearch.value.replace(/[0-9۰-۹٠-٩+\-_,.]/g,'');
      if(cleaned !== hostSearch.value){ hostSearch.value = cleaned; }
      const q=hostSearch.value.trim();
      clearTimeout(t);
      if(q.length<2){ results.style.display='none'; results.innerHTML=''; return; }
      t=setTimeout(async ()=>{
        try{
          const res=await fetch("{{ route('admin.hosts.search') }}?q="+encodeURIComponent(q),{headers:{'Accept':'application/json'}});
          const rows=await res.json();
          results.innerHTML='';
          rows.forEach(h=>{
            const a=document.createElement('a');
            a.href='#'; a.className='list-group-item list-group-item-action';
            a.innerHTML=`<div class="d-flex justify-content-between"><div>${h.name||'—'}</div><div class="small" dir="ltr">${h.phone||''}</div></div><div class="small text-muted">${h.national_id||''} · ${h.status||''}</div>`;
            a.addEventListener('click',(e)=>{ e.preventDefault(); bindHost(h); results.style.display='none'; });
            results.appendChild(a);
          });
          results.style.display = rows.length ? 'block':'none';
        }catch{ results.style.display='none'; }
      },300);
    });

    const hostPhone=document.getElementById('hostPhone');
    function toEnDigits(s){const fa='۰۱۲۳۴۵۶۷۸۹', ar='٠١٢٣٤٥٦٧٨٩'; let out=''; for(const ch of String(s)){const iFa=fa.indexOf(ch); if(iFa>-1){out+=String(iFa); continue;} const iAr=ar.indexOf(ch); if(iAr>-1){out+=String(iAr); continue;} out+=ch;} return out; }
    ['input','blur','change'].forEach(ev=> hostPhone.addEventListener(ev,()=>{ hostPhone.value=toEnDigits(hostPhone.value).replace(/[^0-9]/g,''); }));
    hostPhone.addEventListener('blur', async ()=>{
      const p=hostPhone.value.trim(); if(!p) return;
      try{
        const res=await fetch("{{ route('admin.hosts.lookup') }}?phone="+encodeURIComponent(p),{headers:{'Accept':'application/json'}});
        if(res.ok){ const h=await res.json(); bindHost(h); }
      }catch{}
    });

    const quickHostUrl = "{{ Route::has('admin.hosts.quick_store') ? route('admin.hosts.quick_store') : (Route::has('admin.hosts.store') ? route('admin.hosts.store') : '') }}";
    const quickBtn = document.getElementById('btnQuickCreateHost');
    if(!quickHostUrl){
      quickBtn.disabled = true;
      quickBtn.title = 'مسیر ایجاد میزبان در سرور یافت نشد';
    }

    quickBtn.addEventListener('click', async ()=>{
      const phone = hostPhone.value.trim();
      if(!phone){ Swal.fire({icon:'warning',title:'ابتدا شماره موبایل را وارد کنید'}); return; }
      try{
        const chk=await fetch("{{ route('admin.hosts.lookup') }}?phone="+encodeURIComponent(phone),{headers:{'Accept':'application/json'}});
        if(chk.ok){ const h=await chk.json(); bindHost(h); return; }
      }catch{}
      const name = (hostSearch.value||'').trim() || phone;
      const form = new FormData();
      form.append('_token','{{ csrf_token() }}');
      form.append('name', name);
      form.append('phone', phone);
      try{
        const resp = await fetch(quickHostUrl,{method:'POST', body: form, headers:{'Accept':'application/json'}});
        if(resp.ok){ const h=await resp.json(); bindHost(h); Swal.fire({toast:true,icon:'success',title:'میزبان ایجاد شد',position:'top',timer:1200,showConfirmButton:false}); }
        else{
          const d = await resp.json().catch(()=>({message:'خطا'}));
          Swal.fire({icon:'error',title:'خطا در ایجاد', text: d.message||'لطفاً ورودی‌ها را بررسی کنید'});
        }
      }catch{ Swal.fire({icon:'error',title:'خطا در ارتباط با سرور'}); }
    });
    document.getElementById('btnChangeHost').addEventListener('click',()=> clearHost());
    
    // Pre-populate with current host if exists
    @if($stay->host_id && $stay->host)
      bindHost({
        id: {{ $stay->host_id }},
        name: '{{ $stay->host->name ?? $stay->host->full_name ?? '—' }}',
        phone: '{{ $stay->host->phone ?? '' }}',
        status: '{{ $stay->host->status ?? '' }}'
      });
    @endif
  })();

  // Category change handler
  const categorySelect = document.getElementById('category');
  const hotelFields = document.getElementById('hotelFields');
  const nonHotelCapacity = document.getElementById('nonHotelCapacity');
  const hotelRoomTypes = document.getElementById('hotelRoomTypes');
  
  function toggleHotelFields() {
    const isHotel = categorySelect.value === 'hotel';
    if (isHotel) {
      hotelFields.classList.remove('d-none');
      nonHotelCapacity.classList.add('d-none');
      hotelRoomTypes.classList.remove('d-none');
      // Make non-hotel capacity fields not required
      nonHotelCapacity.querySelectorAll('[required]').forEach(el => {
        el.removeAttribute('required');
      });
    } else {
      hotelFields.classList.add('d-none');
      nonHotelCapacity.classList.remove('d-none');
      hotelRoomTypes.classList.add('d-none');
      // Make non-hotel capacity fields required again
      nonHotelCapacity.querySelectorAll('input[type="number"]').forEach(el => {
        if (el.name === 'base_capacity' || el.name === 'capacity') {
          el.setAttribute('required', 'required');
        }
      });
    }
  }
  
  categorySelect.addEventListener('change', toggleHotelFields);
  toggleHotelFields(); // Initial check

  // Hide pricing fields for hotels
  function togglePricingFields() {
    const isHotel = categorySelect.value === 'hotel';
    const pricingModeField = document.getElementById('pricingModeField');
    const pricePerPersonField = document.getElementById('pricePerPersonField');
    const extraPersonPriceField = document.getElementById('extraPersonPriceField');
    const pricingModeSelect = document.getElementById('pricing_mode');
    
    if (isHotel) {
      // Hide pricing fields for hotels
      if (pricingModeField) pricingModeField.style.display = 'none';
      if (pricePerPersonField) pricePerPersonField.style.display = 'none';
      if (extraPersonPriceField) extraPersonPriceField.style.display = 'none';
      if (pricingModeSelect) pricingModeSelect.removeAttribute('required');
    } else {
      // Show pricing fields for non-hotels
      if (pricingModeField) pricingModeField.style.display = '';
      if (pricePerPersonField) pricePerPersonField.style.display = '';
      if (extraPersonPriceField) extraPersonPriceField.style.display = '';
      if (pricingModeSelect) pricingModeSelect.setAttribute('required', 'required');
    }
  }
  
  categorySelect.addEventListener('change', togglePricingFields);

  // Room type builder
  let roomTypeCounter = 0;
  const roomTypesContainer = document.getElementById('roomTypesContainer');
  // roomTypesJsonField is already defined at top level
  const bedsData = @json($beds ?? []);

  function getBedCapacity(bedCode) {
    return ['double', 'queen', 'king'].includes(bedCode) ? 2 : 1;
  }

  function calculateTotalBedCapacity(beds) {
    let total = 0;
    beds.forEach(bed => {
      const bedModel = bedsData.find(b => b.id == bed.bed_id);
      if (bedModel) {
        total += bed.quantity * getBedCapacity(bedModel.code);
      }
    });
    return total;
  }

  function addRoomType(roomTypeData = null) {
    const roomTypeId = roomTypeCounter++;
    const roomTypeDiv = document.createElement('div');
    roomTypeDiv.className = 'card mb-3 room-type-card';
    roomTypeDiv.dataset.roomTypeId = roomTypeId;
    
    // Use provided data or defaults
    const title = roomTypeData?.title || '';
    const capacity = roomTypeData?.capacity || '';
    const baseCapacity = roomTypeData?.base_capacity || '';
    const extraCapacity = roomTypeData?.extra_capacity || 0;
    const area = roomTypeData?.area ? roomTypeData.area.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '';
    const pricePerNight = roomTypeData?.price_per_night ? roomTypeData.price_per_night.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '';
    const totalRooms = roomTypeData?.total_rooms || 1;
    const beds = roomTypeData?.beds || [];
    
    roomTypeDiv.innerHTML = `
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">نوع اتاق #${roomTypeId + 1}</h6>
        <button type="button" class="btn btn-sm btn-outline-danger remove-room-type">
          <i class="bi bi-trash3"></i> حذف
        </button>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">عنوان نوع اتاق <span class="text-danger">*</span></label>
            <input type="text" class="form-control room-type-title" value="${title}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">ظرفیت کل <span class="text-danger">*</span></label>
            <input type="number" min="1" class="form-control room-type-capacity" value="${capacity}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">ظرفیت پایه <span class="text-danger">*</span></label>
            <input type="number" min="1" class="form-control room-type-base-capacity" value="${baseCapacity}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">ظرفیت اضافه</label>
            <input type="number" min="0" class="form-control room-type-extra-capacity" value="${extraCapacity}">
          </div>
          <div class="col-md-3">
            <label class="form-label">متراژ (متر)</label>
            <input type="text" class="form-control room-type-area" data-price-format placeholder="" value="${area}">
          </div>
          <div class="col-md-3">
            <label class="form-label">قیمت هر شب (ریال) <span class="text-danger">*</span></label>
            <input type="text" class="form-control room-type-price price-field" data-price-format placeholder="" value="${pricePerNight}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">تعداد اتاق‌ها <span class="text-danger">*</span></label>
            <input type="number" min="1" class="form-control room-type-total-rooms" value="${totalRooms}" required>
          </div>
          <div class="col-12">
            <label class="form-label">ترکیب تخت‌ها <span class="text-danger">*</span></label>
            <div class="beds-container">
              <div class="d-flex gap-2 mb-2">
                <select class="form-select bed-select" style="max-width:200px;">
                  <option value="">انتخاب تخت</option>
                  ${bedsData.map(bed => `<option value="${bed.id}" data-code="${bed.code}">${bed.title_fa}</option>`).join('')}
                </select>
                <input type="number" min="1" class="form-control bed-quantity" placeholder="" style="max-width:100px;">
                <button type="button" class="btn btn-sm btn-outline-primary add-bed-btn">افزودن</button>
              </div>
              <div class="beds-list"></div>
              <small class="text-muted d-block mt-2">مجموع ظرفیت تخت‌ها: <span class="bed-total-capacity">0</span></small>
            </div>
          </div>
        </div>
      </div>
    `;
    roomTypesContainer.appendChild(roomTypeDiv);
    
    // Apply price formatting to area and price fields
    const areaInput = roomTypeDiv.querySelector('.room-type-area');
    const priceInput = roomTypeDiv.querySelector('.room-type-price');
    
    if (areaInput && typeof formatPrice === 'function') {
      areaInput.addEventListener('input', function() {
        const caret = this.selectionStart;
        this.value = formatPrice(this.value);
        this.setSelectionRange(caret, caret);
      });
      areaInput.addEventListener('blur', function() {
        this.value = formatPrice(this.value);
      });
    }
    
    if (priceInput && typeof formatPrice === 'function') {
      priceInput.addEventListener('input', function() {
        const caret = this.selectionStart;
        this.value = formatPrice(this.value);
        this.setSelectionRange(caret, caret);
      });
      priceInput.addEventListener('blur', function() {
        this.value = formatPrice(this.value);
      });
    }
    
    // Add bed functionality
    const addBedBtn = roomTypeDiv.querySelector('.add-bed-btn');
    const bedSelect = roomTypeDiv.querySelector('.bed-select');
    const bedQuantity = roomTypeDiv.querySelector('.bed-quantity');
    const bedsList = roomTypeDiv.querySelector('.beds-list');
    const bedTotalCapacity = roomTypeDiv.querySelector('.bed-total-capacity');
    const capacityInput = roomTypeDiv.querySelector('.room-type-capacity');
    
    let roomBeds = beds.map(b => ({bed_id: b.bed_id || b.id, quantity: b.quantity || 1}));
    
    function updateBedTotalCapacity() {
      const total = calculateTotalBedCapacity(roomBeds);
      bedTotalCapacity.textContent = total;
    }
    
    function canAddBed(bedId, quantity) {
      const bedModel = bedsData.find(b => b.id == bedId);
      if (!bedModel) return false;
      
      const currentTotal = calculateTotalBedCapacity(roomBeds);
      const bedCapacity = getBedCapacity(bedModel.code);
      const additionalCapacity = bedCapacity * quantity;
      const roomCapacity = parseInt(capacityInput.value) || 0;
      
      return (currentTotal + additionalCapacity) <= roomCapacity;
    }
    
    addBedBtn.addEventListener('click', () => {
      const bedId = bedSelect.value;
      const quantity = parseInt(bedQuantity.value) || 1;
      if (!bedId) {
        Swal.fire({icon: 'warning', title: 'لطفاً نوع تخت را انتخاب کنید'});
        return;
      }
      
      // Check if adding this bed would exceed capacity
      if (!canAddBed(bedId, quantity)) {
        const bedModel = bedsData.find(b => b.id == bedId);
        if (bedModel) {
          const currentTotal = calculateTotalBedCapacity(roomBeds);
          const bedCapacity = getBedCapacity(bedModel.code);
          const roomCapacity = parseInt(capacityInput.value) || 0;
          Swal.fire({
            icon: 'warning',
            title: 'ظرفیت کافی نیست',
            text: `افزودن این تخت باعث می‌شود مجموع ظرفیت تخت‌ها (${currentTotal + (bedCapacity * quantity)}) از ظرفیت کل اتاق (${roomCapacity}) بیشتر شود.`
          });
        }
        return;
      }
      
      const bedModel = bedsData.find(b => b.id == bedId);
      if (!bedModel) return;
      
      // Check if bed already exists
      const existing = roomBeds.find(b => b.bed_id == bedId);
      if (existing) {
        // Check if increasing quantity would exceed capacity
        const currentTotal = calculateTotalBedCapacity(roomBeds);
        const bedCapacity = getBedCapacity(bedModel.code);
        const newTotal = currentTotal - (bedCapacity * existing.quantity) + (bedCapacity * (existing.quantity + quantity));
        const roomCapacity = parseInt(capacityInput.value) || 0;
        
        if (newTotal <= roomCapacity) {
          existing.quantity += quantity;
        } else {
          // Show alert if would exceed capacity
          Swal.fire({
            icon: 'warning',
            title: 'ظرفیت کافی نیست',
            text: `افزودن این تعداد تخت باعث می‌شود مجموع ظرفیت تخت‌ها (${newTotal}) از ظرفیت کل اتاق (${roomCapacity}) بیشتر شود.`
          });
          return;
        }
      } else {
        roomBeds.push({bed_id: parseInt(bedId), quantity: quantity});
      }
      
      renderBedsList();
      bedSelect.value = '';
      bedQuantity.value = '';
      updateCapacityConstraints();
    });
    
    function renderBedsList() {
      bedsList.innerHTML = '';
      roomBeds.forEach((bed, index) => {
        const bedModel = bedsData.find(b => b.id == bed.bed_id);
        if (!bedModel) return;
        const bedDiv = document.createElement('div');
        bedDiv.className = 'badge bg-primary me-2 mb-2 p-2';
        bedDiv.innerHTML = `
          ${bedModel.title_fa} × ${bed.quantity}
          <button type="button" class="btn-close btn-close-white ms-2" data-bed-index="${index}"></button>
        `;
        bedsList.appendChild(bedDiv);
      });
      updateBedTotalCapacity();
    }
    
    bedsList.addEventListener('click', (e) => {
      const btn = e.target.closest('button');
      if (btn) {
        const index = parseInt(btn.dataset.bedIndex);
        roomBeds.splice(index, 1);
        renderBedsList();
      }
    });
    
    // Capacity validation - input-level constraints only, no alerts
    const baseCapacityInput = roomTypeDiv.querySelector('.room-type-base-capacity');
    const extraCapacityInput = roomTypeDiv.querySelector('.room-type-extra-capacity');
    
    function updateCapacityConstraints() {
      const capacity = parseInt(capacityInput.value) || 1;
      const baseCapacity = parseInt(baseCapacityInput.value) || 1;
      
      // Set max for base capacity (cannot exceed total capacity)
      baseCapacityInput.setAttribute('max', capacity);
      if (baseCapacity > capacity) {
        baseCapacityInput.value = capacity;
      }
      
      // Set max for extra capacity (cannot exceed total - base)
      const maxExtra = Math.max(0, capacity - (parseInt(baseCapacityInput.value) || 1));
      extraCapacityInput.setAttribute('max', maxExtra);
      const extraCapacity = parseInt(extraCapacityInput.value) || 0;
      if (extraCapacity > maxExtra) {
        extraCapacityInput.value = maxExtra;
      }
    }
    
    capacityInput.addEventListener('input', updateCapacityConstraints);
    capacityInput.addEventListener('change', updateCapacityConstraints);
    baseCapacityInput.addEventListener('input', updateCapacityConstraints);
    baseCapacityInput.addEventListener('change', updateCapacityConstraints);
    extraCapacityInput.addEventListener('input', updateCapacityConstraints);
    extraCapacityInput.addEventListener('change', updateCapacityConstraints);
    
    // Initialize constraints
    updateCapacityConstraints();
    
    // Remove room type
    roomTypeDiv.querySelector('.remove-room-type').addEventListener('click', () => {
      roomTypeDiv.remove();
      syncRoomTypesJson();
    });
    
    // Store beds array in data attribute
    roomTypeDiv.dataset.beds = JSON.stringify(roomBeds);
    
    // Update beds when changed
    const observer = new MutationObserver(() => {
      roomTypeDiv.dataset.beds = JSON.stringify(roomBeds);
    });
    observer.observe(bedsList, {childList: true});
    
    // Initial render of beds
    renderBedsList();
  }

  function syncRoomTypesJson() {
    const roomTypes = [];
    document.querySelectorAll('.room-type-card').forEach(card => {
      const title = card.querySelector('.room-type-title').value;
      const capacity = parseInt(card.querySelector('.room-type-capacity').value) || 0;
      const baseCapacity = parseInt(card.querySelector('.room-type-base-capacity').value) || 0;
      const extraCapacity = parseInt(card.querySelector('.room-type-extra-capacity').value) || 0;
      const area = card.querySelector('.room-type-area').value ? parseInt(card.querySelector('.room-type-area').value.replace(/,/g, '')) : null;
      const pricePerNight = card.querySelector('.room-type-price').value.replace(/,/g, '');
      const totalRooms = parseInt(card.querySelector('.room-type-total-rooms').value) || 0;
      const beds = JSON.parse(card.dataset.beds || '[]');
      
      if (title && capacity > 0 && baseCapacity > 0 && totalRooms > 0 && beds.length > 0) {
        roomTypes.push({
          title: title,
          capacity: capacity,
          base_capacity: baseCapacity,
          extra_capacity: extraCapacity,
          area: area,
          price_per_night: pricePerNight,
          total_rooms: totalRooms,
          beds: beds
        });
      }
    });
    roomTypesJsonField.value = JSON.stringify(roomTypes);
  }

  document.getElementById('addRoomTypeBtn').addEventListener('click', () => addRoomType());

  // Load existing hotel room types if editing a hotel
  @if($stay->category === 'hotel' && $stay->hotel && $stay->hotel->roomTypes)
    @foreach($stay->hotel->roomTypes as $rt)
      addRoomType({
        title: {!! json_encode($rt->title) !!},
        capacity: {{ $rt->capacity }},
        base_capacity: {{ $rt->base_capacity }},
        extra_capacity: {{ $rt->extra_capacity ?? 0 }},
        area: {{ $rt->area ?? 'null' }},
        price_per_night: {{ $rt->price_per_night }},
        total_rooms: {{ $rt->total_rooms }},
        beds: @json($rt->beds->map(function($bed) {
          return [
            'bed_id' => $bed->id,
            'quantity' => $bed->pivot->quantity ?? 1
          ];
        }))
      });
    @endforeach
  @endif

  // Validate step 3 for hotels
  const originalValidateStep = validateStep;
  validateStep = function(step, silent = false) {
    if (step === 3 && categorySelect.value === 'hotel') {
      const roomTypes = document.querySelectorAll('.room-type-card');
      if (roomTypes.length === 0) {
        if (!silent) Swal.fire({icon: 'warning', title: 'حداقل یک نوع اتاق باید تعریف شود'});
        return false;
      }
      let allValid = true;
      roomTypes.forEach(card => {
        const title = card.querySelector('.room-type-title').value;
        const capacity = parseInt(card.querySelector('.room-type-capacity').value) || 0;
        const baseCapacity = parseInt(card.querySelector('.room-type-base-capacity').value) || 0;
        const totalRooms = parseInt(card.querySelector('.room-type-total-rooms').value) || 0;
        const pricePerNight = card.querySelector('.room-type-price').value.replace(/,/g, '');
        const beds = JSON.parse(card.dataset.beds || '[]');
        
        if (!title || capacity < 1 || baseCapacity < 1 || totalRooms < 1 || !pricePerNight || beds.length === 0) {
          allValid = false;
        }
        
        // Validate bed capacity does not exceed room capacity
        const bedTotal = calculateTotalBedCapacity(beds);
        if (bedTotal > capacity) {
          allValid = false;
          if (!silent) {
            Swal.fire({
              icon: 'error',
              title: 'خطا در نوع اتاق',
              text: `مجموع ظرفیت تخت‌ها (${bedTotal}) نمی‌تواند بیشتر از ظرفیت کل (${capacity}) باشد.`
            });
          }
        }
      });
      if (!allValid) {
        if (!silent) Swal.fire({icon: 'error', title: 'لطفاً تمام فیلدهای نوع اتاق را به درستی تکمیل کنید'});
        return false;
      }
      syncRoomTypesJson();
    }
    return originalValidateStep(step, silent);
  };

  show(1);
})();
</script>
@endpush