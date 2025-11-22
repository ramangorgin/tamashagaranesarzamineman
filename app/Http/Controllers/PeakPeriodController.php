<?php

namespace App\Http\Controllers;

use App\Models\PeakPeriod;
use Illuminate\Http\Request;

class PeakPeriodController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        PeakPeriod::create($data);
        return back()->with('success','بازه پیک ثبت شد.');
    }

    public function destroy(PeakPeriod $peakPeriod)
    {
        $peakPeriod->delete();
        return back()->with('success','بازه حذف شد.');
    }
}