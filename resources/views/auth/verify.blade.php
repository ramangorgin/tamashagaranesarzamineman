@extends('layouts.app')

@section('title', 'تأیید کد پیامکی')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="text-center mb-4">تأیید شماره موبایل</h5>

                {{-- پیام موفقیت یا خطا --}}
                @if (session('message'))
                    <div class="alert alert-success text-center">{{ session('message') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('verify-otp') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">شماره موبایل</label>
                        <input type="text" name="phone" class="form-control text-center"
                            value="{{ session('phone') ?? old('phone') }}"
                            placeholder="09123456789" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">کد تأیید (۶ رقمی)</label>
                        <input type="text" name="code" class="form-control text-center"
                            placeholder="******" required>
                    </div>

                    {{-- تایمر شمارش معکوس --}}
                    <div class="text-center mb-3">
                        <span id="timer" class="fw-bold text-danger"></span>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-3">
                        ورود به حساب
                    </button>
                </form>

                {{-- دکمه ارسال مجدد --}}
                <form method="POST" action="{{ route('send-otp') }}" id="resendForm">
                    @csrf
                    <input type="hidden" name="phone" value="{{ session('phone') ?? old('phone') }}">
                    <button type="submit" id="resendBtn" class="btn btn-outline-primary w-100" disabled>
                        ارسال مجدد کد
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- اسکریپت تایمر --}}
<script>
    let totalSeconds = 300; // مدت زمان (۵ دقیقه)
    const timerDisplay = document.getElementById('timer');
    const resendBtn = document.getElementById('resendBtn');

    function updateTimer() {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        timerDisplay.textContent = `زمان باقی‌مانده: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;

        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = "زمان به پایان رسید.";
            resendBtn.disabled = false; // فعال شدن دکمه
            resendBtn.classList.remove('btn-outline-primary');
            resendBtn.classList.add('btn-primary');
        }

        totalSeconds--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
</script>
@endsection
