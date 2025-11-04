@extends('layouts.app')

@section('title', 'ورود با شماره تلفن')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="text-center mb-4">ورود به سامانه</h5>

                <form method="POST" action="{{ route('send-otp') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">شماره موبایل</label>
                        <input type="text" name="phone" class="form-control text-center" placeholder="مثلاً 09123456789" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">ارسال کد تأیید</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
