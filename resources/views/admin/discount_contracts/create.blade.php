@extends('layouts.admin')

@section('title', 'ایجاد قرارداد جدید')

@section('content')


<div class="container">
    <h3>ایجاد قرارداد جدید</h3>

    <form action="{{ route('discount-contracts.store') }}" method="POST">
        @csrf
        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <label>عنوان قرارداد</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="col-md-3 mb-3">
                <label>درصد تخفیف (%)</label>
                <input type="number" name="discount_percent" class="form-control" min="0" max="100" required>
            </div>

            <div class="col-md-3 mb-3">
                <label>فعال باشد؟</label>
                <select name="is_active" class="form-select">
                    <option value="1">بله</option>
                    <option value="0">خیر</option>
                </select>
            </div>

            <div class="col-md-6 mb-3 position-relative">
                <label>تاریخ شروع</label>
                <div class="input-group">
                    <input type="text" id="start_date_display" class="form-control" placeholder="انتخاب تاریخ شمسی">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="start_date" id="start_date">
            </div>

            <div class="col-md-6 mb-3 position-relative">
                <label>تاریخ پایان</label>
                <div class="input-group">
                    <input type="text" id="end_date_display" class="form-control" placeholder="انتخاب تاریخ شمسی">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                </div>
                <input type="hidden" name="end_date" id="end_date">
            </div>



            <div class="col-md-12 mt-3">
                <button class="btn btn-success">ذخیره قرارداد</button>
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
        initialValue: false,
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
        initialValue: false,
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