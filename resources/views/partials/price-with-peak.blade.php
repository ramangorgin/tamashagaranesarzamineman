<div class="price-with-peak d-inline-flex align-items-center gap-2">
  <span class="fw-bold text-warning fs-5">{{ number_format($adjusted) }}</span>
  <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.75rem; font-weight: 600;">
    <i class="bi bi-arrow-up-circle-fill"></i> +{{ rtrim(rtrim(number_format($percent, 1), '0'), '.') }}%
  </span>
</div>

