@extends('layouts.admin')

@section('title', 'ویرایش عضو قرارداد')

@section('content')
<div class="container mt-4">
    <h3>ویرایش عضو: {{ $member->full_name }}</h3>

    <form action="{{ route('discount-contract-members.update', $member->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>نام کامل</label>
                <input type="text" name="full_name" class="form-control" value="{{ $member->full_name }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>کد ملی</label>
                <input type="text" name="national_id" class="form-control" value="{{ $member->national_id }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>شماره تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ $member->phone }}">
            </div>

            <div class="col-md-12 mt-3">
                <button class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('discount-contracts.show', $member->contract_id) }}" class="btn btn-secondary">بازگشت</a>
            </div>
        </div>
    </form>
</div>
@endsection
