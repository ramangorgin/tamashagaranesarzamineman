<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    /**
     * Loging-out
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('message', 'با موفقیت خارج شدید.');
    }
}
