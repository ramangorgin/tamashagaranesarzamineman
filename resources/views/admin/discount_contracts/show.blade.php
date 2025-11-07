@extends('layouts.admin')

@section('title', 'جزئیات قرارداد')

@section('content')
<div class="container mt-4">
    <h3>قرارداد: {{ $contract->title }}</h3>
    <p>درصد تخفیف: <strong>{{ $contract->discount_percent }}%</strong></p>
    <p>از تاریخ: {{ verta($contract->start_date)->format('Y/m/d') }}
    تا {{ verta($contract->end_date)->format('Y/m/d') }}</p>

    <hr>
    <h5>افزودن عضو جدید</h5>

    <form action="{{ route('discount-contract-members.store') }}" method="POST" class="row">
        @csrf
        <input type="hidden" name="contract_id" value="{{ $contract->id }}">
        <div class="col-md-4 mb-2">
            <input type="text" name="full_name" class="form-control" placeholder="نام کامل" required>
        </div>
        <div class="col-md-3 mb-2">
            <input type="text" name="national_id" class="form-control" placeholder="کد ملی">
        </div>
        <div class="col-md-3 mb-2">
            <input type="text" name="phone" class="form-control" placeholder="شماره تلفن">
        </div>
        <div class="col-md-2 mb-2">
            <button class="btn btn-success w-100">افزودن</button>
        </div>
    </form>

    <hr>
    <h5>اعضای این قرارداد ({{ $contract->members->count() }})</h5>

    <table class="table table-striped text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>نام کامل</th>
                <th>کد ملی</th>
                <th>شماره تلفن</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contract->members as $member)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $member->full_name }}</td>
                <td>{{ $member->national_id ?? '-' }}</td>
                <td>{{ $member->phone ?? '-' }}</td>
                <td>
                    <a href="{{ route('discount-contract-members.edit', $member->id) }}" class="btn btn-sm btn-warning">ویرایش</a>
                    <form action="{{ route('discount-contract-members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف این عضو؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
