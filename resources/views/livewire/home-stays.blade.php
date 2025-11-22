<div class="home-stays-wrapper">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-stars text-primary me-2"></i> اقامت‌گاه‌های پیشنهادی</h2>
            <p class="text-muted small mb-0">مرور سریع چند اقامت‌گاه فعال و قابل رزرو</p>
        </div>
        <div class="filters row g-2 w-100 w-lg-auto">
            <div class="col-6 col-md-3">
                <input wire:model.debounce.500ms="search" type="text" class="form-control form-control-sm" placeholder="جستجو عنوان / شهر">
            </div>
            <div class="col-6 col-md-3">
                <input wire:model.debounce.500ms="province" type="text" class="form-control form-control-sm" placeholder="استان">
            </div>
            <div class="col-6 col-md-3">
                <input wire:model.debounce.500ms="city" type="text" class="form-control form-control-sm" placeholder="شهر">
            </div>
            <div class="col-6 col-md-3">
                <select wire:model="sort" class="form-select form-select-sm">
                    <option value="latest">جدیدترین</option>
                    <option value="price_low">کمترین قیمت</option>
                    <option value="price_high">بیشترین قیمت</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($stays as $stay)
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ route('stays.show', $stay) }}" class="text-decoration-none stay-card h-100 d-block">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                        @php $img = optional($stay->images->first())->url; @endphp
                        <div class="ratio ratio-4x3 bg-light">
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $stay->title }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async">
                            @else
                                <div class="d-flex align-items-center justify-content-center text-muted fw-semibold">بدون تصویر</div>
                            @endif
                            @if($stay->is_peak)
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 shadow-sm">پیک</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold mb-1 text-dark text-truncate">{{ $stay->title }}</h6>
                            <div class="small text-muted mb-2 text-truncate">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $stay->city_name }}، {{ $stay->province_name }}
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-2 small text-muted">
                                <span><i class="bi bi-people-fill text-primary me-1"></i>{{ $stay->base_capacity }}/{{ $stay->capacity }}</span>
                                @if($stay->area)
                                    <span><i class="bi bi-aspect-ratio text-primary me-1"></i>{{ $stay->area }} متر</span>
                                @endif
                                @if($stay->bedrooms)
                                    <span><i class="bi bi-door-open text-primary me-1"></i>{{ $stay->bedrooms }} خواب</span>
                                @endif
                            </div>
                            <div class="mt-auto">
                                <div class="price-box d-flex align-items-baseline gap-1">
                                    <span class="fw-bold text-primary">{{ number_format($stay->price_per_person ?? 0) }}</span>
                                    <small class="text-muted">ریال / نفر</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 small mb-0">اقامت‌گاهی یافت نشد.</div>
            </div>
        @endforelse
    </div>

    <div class="text-center mt-4" wire:key="home-stays-load-more">
        @if($stays->count() >= $perPage)
            <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> بیشتر
            </button>
        @endif
    </div>

    <div wire:loading.class.remove="opacity-0" wire:loading.delay class="loading-overlay opacity-0">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
</div>
