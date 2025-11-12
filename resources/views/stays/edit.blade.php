@php
    $isAdmin = Auth::guard('admin')->check();
    $isHost = Auth::guard('host')->check();
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.host';
    $updateRoute = $isAdmin ? route('admin.stays.update', $stay->id) : route('host.stays.update', $stay->id);
@endphp

@extends($layout)

@section('title', 'ویرایش اقامت‌گاه')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-pencil-square text-primary me-2"></i>
            {{ $isAdmin ? 'ویرایش اقامت‌گاه (ادمین)' : 'ویرایش اقامت‌گاه (میزبان)' }}
        </h4>
        <a href="{{ $isAdmin ? route('admin.stays.index') : route('host.stays.index') }}" 
           class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-right-circle me-1"></i> بازگشت
        </a>
    </div>

    @include('admin.partials.messages')

    <div class="card border-0 shadow-sm rounded-4 p-4 fade-in">
        <form method="POST" action="{{ $updateRoute }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- فرم مشترک --}}
            @include('stays.partials.form-fields', [
                'stay' => $stay ?? null,
                'isAdmin' => $isAdmin,
                'categories' => $categories
            ])


            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-save2 me-1"></i> به‌روزرسانی اقامت‌گاه
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.fade-in { animation: fadeIn .4s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
input, textarea, select { transition: all 0.2s ease-in-out; }
input:focus, textarea:focus, select:focus { box-shadow: 0 0 5px rgba(13,110,253,0.5); border-color: #0d6efd; }
.hover-facility:hover { background-color: #e9f5ff; transform: scale(1.02); }
</style>
@endpush

@push('scripts')
<script>
const stayId = {{ $stay->id ?? 'null' }};

document.getElementById('images').addEventListener('change', function(e) {
    if (!stayId) return alert('ابتدا اقامت‌گاه را ثبت کنید.');

    let formData = new FormData();
    for (const file of e.target.files) formData.append('images[]', file);

    fetch(`/{{ $isAdmin ? 'admin' : 'host' }}/stays/${stayId}/upload-image`, {
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
    fetch(`/{{ $isAdmin ? 'admin' : 'host' }}/stays/images/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(res => res.json()).then(data => {
        if (data.success) document.getElementById(`img-${id}`).remove();
    });
}
</script>
@endpush
@endsection
