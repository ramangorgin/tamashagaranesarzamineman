@extends('layouts.admin')
@section('title', 'جزئیات قرارداد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.discount_contracts.index') }}">تخفیفات سازمانی</a></li>
    <li class="breadcrumb-item active">جزئیات</li>
@endsection

@section('breadcrumb-actions')
    <a href="{{ route('admin.discount_contracts.edit',$contract) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil-square"></i> ویرایش
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body d-md-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $contract->title }}</h5>
                <div class="text-muted small">
                    درصد: <strong>{{ rtrim(rtrim(number_format($contract->discount_percent,2), '0'),'.') }}%</strong> |
                    از {{ verta($contract->start_date)->format('Y/m/d') }} تا {{ verta($contract->end_date)->format('Y/m/d') }}
                </div>
            </div>
            <span class="badge {{ $contract->is_currently_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $contract->is_currently_active ? 'فعال' : 'غیرفعال' }}
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">افزودن عضو جدید</h6>
            <form action="{{ route('admin.discount_contract_members.store',$contract) }}" method="POST" class="row g-2 mb-3">
                @csrf
                <div class="col-md-4"><input type="text" name="full_name" class="form-control" placeholder="نام کامل" required></div>
                <div class="col-md-3"><input type="text" name="national_id" class="form-control" placeholder="کد ملی"></div>
                <div class="col-md-3"><input type="text" name="phone" class="form-control" placeholder="شماره تلفن"></div>
                <div class="col-md-2"><button class="btn btn-success w-100">افزودن</button></div>
            </form>

            <h6 class="fw-bold mb-3">اعضا ({{ $contract->members->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>نام کامل</th>
                            <th>کد ملی</th>
                            <th>شماره تلفن</th>
                            <th style="width:120px">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->members as $member)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $member->full_name }}</td>
                            <td>{{ $member->national_id ?? '-' }}</td>
                            <td>{{ $member->phone ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.discount_contract_members.destroy',[$contract,$member]) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف این عضو؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted">عضوی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
