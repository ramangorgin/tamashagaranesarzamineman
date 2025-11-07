@extends('layouts.admin')

@section('title', 'ویرایش قرارداد')

@section('content')
<div class="container mt-4">
    <h3>ویرایش قرارداد: {{ $contract->title }}</h3>

    <form action="{{ route('discount-contracts.update', $contract->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <label>عنوان</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $contract->title) }}" required>
            </div>

            <div class="col-md-3 mb-3">
                <label>درصد تخفیف (%)</label>
                <input type="number" name="discount_percent" class="form-control" min="0" max="100" value="{{ old('discount_percent', $contract->discount_percent) }}" required>
            </div>

            <div class="col-md-3 mb-3">
                <label>فعال باشد؟</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $contract->is_active ? 'selected' : '' }}>بله</option>
                    <option value="0" {{ !$contract->is_active ? 'selected' : '' }}>خیر</option>
                </select>
            </div>

            <div class="col-md-6 mb-3 position-relative">
                <label>تاریخ شروع</label>
                <div class="input-group">
                    <input type="text" id="start_date_display" class="form-control"
                        value="{{ verta($contract->start_date)->format('Y/m/d') }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="start_date" id="start_date" value="{{ $contract->start_date->format('Y-m-d') }}">
            </div>

            <div class="col-md-6 mb-3 position-relative">
                <label>تاریخ پایان</label>
                <div class="input-group">
                    <input type="text" id="end_date_display" class="form-control"
                        value="{{ verta($contract->end_date)->format('Y/m/d') }}">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="end_date" id="end_date" value="{{ $contract->end_date->format('Y-m-d') }}">
            </div>


            <div class="col-md-12 mt-3">
                <button class="btn btn-primary">ذخیره تغییرات</button>
                <a href="{{ route('discount-contracts.index') }}" class="btn btn-secondary">بازگشت</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $("#start_date_display").persianDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        toolbox: { calendarSwitch: { enabled: false } },
        onSelect: function(unix) {
            let gregorian = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
            gregorian = toEnglishDigits(gregorian);
            $('#start_date').val(gregorian);
        }
    });

    $("#end_date_display").persianDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        toolbox: { calendarSwitch: { enabled: false } },
        onSelect: function(unix) {
            let gregorian = new persianDate(unix).toCalendar('gregorian').format('YYYY-MM-DD');
            gregorian = toEnglishDigits(gregorian);
            $('#end_date').val(gregorian);
        }
    });

    $('form').on('submit', function(e) {
        const start = $('#start_date').val();
        const end = $('#end_date').val();

        if (!start || !end) {
            e.preventDefault();
            alert('لطفاً تاریخ شروع و پایان را انتخاب کنید.');
        }
    });
});
</script>
@endpush
@endsection
