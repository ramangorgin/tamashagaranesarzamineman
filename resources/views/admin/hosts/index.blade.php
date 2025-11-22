@extends('layouts.admin')
@section('title','مدیریت میزبانان')

@section('breadcrumb')
    <li class="breadcrumb-item active">میزبانان</li>
@endsection

@section('breadcrumb-actions')
    <a href="{{ route('admin.hosts.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> ایجاد میزبان
    </a>
@endsection

@section('content')
@if(session('success')) <div class="alert alert-success mt-2">{{ session('success') }}</div> @endif

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <form method="GET" class="row g-2 mb-3">
      <div class="col-md-6">
        <input name="q" value="{{ $q }}" class="form-control" placeholder="جستجو نام، موبایل یا کد ملی">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100"><i class="bi bi-search"></i> جستجو</button>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr class="text-nowrap small">
            <th>#</th>
            <th>نام</th>
            <th>موبایل</th>
            <th>استان / شهر</th>
            <th>وضعیت</th>
            <th>شناسه ملی</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($hosts as $h)
            <tr>
              <td class="text-muted small">{{ $h->id }}</td>
              <td>{{ $h->name ?? '—' }}</td>
              <td dir="ltr">{{ $h->phone }}</td>
              <td class="small">{{ $h->province_name ?? '—' }} / {{ $h->city_name ?? '—' }}</td>
              <td>
                @php $st=$h->status; @endphp
                <span class="badge {{ $st==='approved'?'bg-success':($st==='rejected'?'bg-danger':'bg-secondary') }}">
                  {{ $st==='approved'?'تأیید شده':($st==='rejected'?'رد شده':'در انتظار') }}
                </span>
                @if($h->rejection_reason)
                  <div class="small text-muted" title="{{ $h->rejection_reason }}">علت: {{ \Illuminate\Support\Str::limit($h->rejection_reason,30) }}</div>
                @endif
              </td>
              <td class="small">{{ $h->national_id ?? '—' }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('admin.hosts.show',$h) }}" class="btn btn-outline-secondary" title="مشاهده"><i class="bi bi-eye"></i></a>
                  <a href="{{ route('admin.hosts.edit',$h) }}" class="btn btn-outline-primary" title="ویرایش"><i class="bi bi-pencil-square"></i></a>
                  @if($h->status!=='approved')
                    <button type="button" class="btn btn-outline-success btn-approve" data-url="{{ route('admin.hosts.approve',$h) }}" title="تأیید"><i class="bi bi-check2"></i></button>
                  @endif
                  @if($h->status!=='rejected')
                    <button type="button" class="btn btn-outline-warning btn-reject" data-url="{{ route('admin.hosts.reject',$h) }}" title="رد"><i class="bi bi-x-lg"></i></button>
                  @endif
                  <button type="button" class="btn btn-outline-danger btn-delete" data-url="{{ route('admin.hosts.destroy',$h) }}" title="حذف"><i class="bi bi-trash3"></i></button>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">موردی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $hosts->links() }}</div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function ajaxConfirm(opts){ Swal.fire(opts.swal).then(r=>{ if(!r.isConfirmed) return; submitPatch(opts.url, opts.method||'PATCH', opts.extra||{}); }); }
function submitPatch(url, method, data){
  const f=document.createElement('form'); f.method='POST'; f.action=url;
  f.innerHTML=`@csrf @method('${method}')`;
  Object.entries(data).forEach(([k,v])=>{
    const i=document.createElement('input'); i.type='hidden'; i.name=k; i.value=v; f.appendChild(i);
  });
  document.body.appendChild(f); f.submit();
}
document.querySelectorAll('.btn-approve').forEach(b=>b.onclick=()=>ajaxConfirm({
  url:b.dataset.url,
  swal:{title:'تأیید میزبان؟',icon:'question',showCancelButton:true,confirmButtonText:'تأیید'}
}));
document.querySelectorAll('.btn-reject').forEach(b=>b.onclick=async()=>{
  const {value:reason,isConfirmed}=await Swal.fire({title:'علت رد (اختیاری)',input:'text',showCancelButton:true,confirmButtonText:'رد'});
  if(isConfirmed) submitPatch(b.dataset.url,'PATCH',{reason});
});
document.querySelectorAll('.btn-delete').forEach(b=>b.onclick=()=>ajaxConfirm({
  url:b.dataset.url, method:'DELETE',
  swal:{title:'حذف میزبان؟',icon:'warning',showCancelButton:true,confirmButtonText:'حذف',confirmButtonColor:'#dc3545'}
}));
</script>
@endpush