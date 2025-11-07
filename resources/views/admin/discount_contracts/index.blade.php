@extends('layouts.admin')

@section('title', 'لیست قراردادهای تخفیف')

@section('content')
<div  class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>لیست قراردادهای تخفیف</h3>
        <a href="{{ route('discount-contracts.create') }}" class="btn btn-primary">➕ ایجاد قرارداد جدید</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>عنوان</th>
                <th>درصد تخفیف</th>
                <th>دوره اعتبار</th>
                <th>وضعیت</th>
                <th>اعضا</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $contract->title }}</td>
                <td>{{ $contract->discount_percent }}%</td>
                <td>{{ verta($contract->start_date)->format('Y/m/d') }} تا {{ verta($contract->end_date)->format('Y/m/d') }}</td>
                <td>
                    @if($contract->isCurrentlyActive)
                        <span class="badge bg-success">فعال</span>
                    @else
                        <span class="badge bg-secondary">غیرفعال</span>
                    @endif
                </td>
                <td>{{ $contract->members->count() }}</td>
                <td>
                    <a href="{{ route('discount-contracts.show', $contract->id) }}" class="btn btn-sm btn-info">مشاهده</a>
                    <a href="{{ route('discount-contracts.edit', $contract->id) }}" class="btn btn-sm btn-warning">ویرایش</a>
                    <form action="{{ route('discount-contracts.destroy', $contract->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف قرارداد؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $contracts->links() }}
</div>
@endsection
