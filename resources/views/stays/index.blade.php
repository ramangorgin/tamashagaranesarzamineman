@extends($role==='admin' ? 'layouts.admin' : 'layouts.host')
@section('title', $role==='admin' ? 'مدیریت اقامت‌گاه‌ها' : 'اقامت‌گاه‌های من')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container {{ $role==='admin' ? 'fluid' : 'py-4' }}">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-2 mb-md-0">
      <i class="bi {{ $role==='admin' ? 'bi-buildings text-primary' : 'bi-house-door text-primary' }} me-2"></i>
      {{ $role==='admin' ? 'مدیریت اقامت‌گاه‌ها' : 'اقامت‌گاه‌های من' }}
    </h4>
    <div class="d-flex gap-2">
      <form id="searchForm" method="GET" class="d-flex">
        <div class="input-group">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="جستجو عنوان یا شناسه...">
          <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>
      <a href="{{ $role==='admin' ? route('admin.stays.create') : route('host.stays.create') }}" class="btn btn-primary rounded-pill">
        <i class="bi bi-plus-circle me-1"></i> جدید
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm border-0 rounded-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr class="text-nowrap small">
            <th>#</th>
            <th>عنوان</th>
            <th>دسته‌بندی</th>
            <th>استان / شهر</th>
            @if($role==='admin')
              <th>میزبان</th>
            @else
              <th>ظرفیت (پایه/کل)</th>
              <th>قیمت پایه</th>
              <th>کمیسیون%</th>
            @endif
            <th>وضعیت</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
        @forelse($stays as $stay)
          <tr class="fade-in">
            <td class="text-muted small">{{ $stay->id }}</td>
            <td class="fw-semibold">{{ Str::limit($stay->title,40) }}</td>
            <td><span class="badge bg-info-subtle text-info">{{ stayTypeToPersian($stay->category) }}</span></td>
            <td class="small">{{ $stay->province_name }} / {{ $stay->city_name }}</td>

            @if($role==='admin')
              <td class="small">{{ $stay->host->name ?? $stay->host->full_name ?? '—' }}</td>
            @else
              <td class="small">{{ $stay->base_capacity }} / {{ $stay->capacity }}</td>
              <td class="small">{{ number_format($stay->price_per_person) }}</td>
              <td class="small">{{ rtrim(rtrim(number_format($stay->site_commission,2),'0'),'.') }}</td>
            @endif

            <td>
              @if($stay->is_active)
                <span class="badge bg-success"><i class="bi bi-check-circle"></i> فعال</span>
              @else
                <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> در انتظار</span>
              @endif
              @if($stay->is_peak)
                <span class="badge bg-warning text-dark">پیک</span>
              @endif
            </td>
            <td class="text-center">
              <div class="btn-group btn-group-sm">
                <a href="{{ route('stays.show',$stay) }}" class="btn btn-outline-secondary" title="نمایش">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ $role==='admin' ? route('admin.stays.edit',$stay) : route('host.stays.edit',$stay) }}" class="btn btn-outline-primary" title="ویرایش">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <button type="button"
                        class="btn btn-outline-danger delete-btn"
                        data-id="{{ $stay->id }}"
                        data-title="{{ Str::limit($stay->title,30) }}"
                        data-url="{{ $role==='admin' ? route('admin.stays.destroy',$stay) : route('host.stays.destroy',$stay) }}"
                        title="حذف">
                  <i class="bi bi-trash3"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $role==='admin' ? 8 : 9 }}" class="text-center py-4 text-muted">
              <i class="bi bi-inbox-fill fs-3 d-block mb-2"></i>
              هیچ اقامت‌گاهی ثبت نشده است.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($stays instanceof \Illuminate\Pagination\AbstractPaginator && $stays->hasPages())
      <div class="card-footer bg-transparent">
        {{ $stays->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>

<style>
.fade-in { animation: fadeIn .35s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(4px);} to {opacity:1; transform:translateY(0);} }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    Swal.fire({
      title:'حذف اقامت‌گاه؟',
      html:'<div class="small text-muted">«'+btn.dataset.title+'»</div>',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'حذف',
      cancelButtonText:'انصراف',
      confirmButtonColor:'#dc3545'
    }).then(r=>{
      if(r.isConfirmed){
        const f=document.createElement('form');
        f.method='POST'; f.action=btn.dataset.url;
        f.innerHTML='@csrf @method("DELETE")';
        document.body.appendChild(f); f.submit();
      }
    });
  });
});
</script>
@endpush