<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use App\Models\StayImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StayController extends Controller
{

    public function index()
    {
        $stays = Stay::with(['host'])->withCount(['images', 'rules', 'facilities'])
                    ->latest()
                    ->paginate(15);

        return view('stays.admin.index', compact('stays'));
    }

    public function toggleStatus(Stay $stay)
    {
        $stay->is_active = !$stay->is_active;
        $stay->save();

        $msg = $stay->is_active ? 'اقامتگاه فعال شد ✅' : 'اقامتگاه غیرفعال شد ❌';
        return back()->with('success', $msg);
    }

    public function togglePeak()
    {
        // Checking the status
        $isCurrentlyPeak = Stay::where('is_peak', true)->exists();

        // Swap it
        $newStatus = !$isCurrentlyPeak;

        // Update all stays to the new peak status
        \App\Models\Stay::query()->update(['is_peak' => $newStatus]);

        $msg = $newStatus ? 'قیمت‌ها در حالت پیک قرار گرفتند ✅' : 'قیمت‌ها از حالت پیک خارج شدند ❌';
        return back()->with('success', $msg);
    }
}