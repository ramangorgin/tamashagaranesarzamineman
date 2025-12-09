<div class="price-with-discount d-inline-flex flex-column align-items-start">
  <div class="d-flex align-items-center gap-2 flex-wrap">
    <span class="fw-bold text-success fs-5">{{ number_format($adjusted) }}</span>
    <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.75rem; font-weight: 600;">
      <i class="bi bi-tag-fill"></i> {{ rtrim(rtrim(number_format($percent, 1), '0'), '.') }}% تخفیف
    </span>
  </div>
  <div class="text-decoration-line-through text-muted small" style="opacity: 0.7;">
    {{ number_format($original) }} ریال
  </div>
</div>

