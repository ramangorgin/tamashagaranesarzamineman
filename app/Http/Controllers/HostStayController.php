<?php

namespace App\Http\Controllers;

use App\Models\Stay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HostStayController extends Controller
{
    // فهرست اقامت‌گاه‌ها
    public function index()
    {
        $stays = Stay::where('host_id', Auth::guard('host')->id())->latest()->get();
        return view('host.stays.index', compact('stays'));
    }

    // فرم ایجاد
    public function create()
    {
        return view('host.stays.create');
    }

    // ذخیره در دیتابیس
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:hotel,villa,apartment,ecolodge',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:10000',
            'rooms' => 'required|integer|min:1',
            'beds' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
        ]);

        $data['host_id'] = Auth::guard('host')->id();
        Stay::create($data);

        return redirect()->route('host.stays.index')->with('success', 'اقامت‌گاه با موفقیت ثبت شد!');
    }

    // فرم ویرایش
    public function edit(Stay $stay)
    {
        $this->authorizeStay($stay);
        return view('host.stays.edit', compact('stay'));
    }

    // بروزرسانی
    public function update(Request $request, Stay $stay)
    {
        $this->authorizeStay($stay);

        $stay->update($request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:hotel,villa,apartment,ecolodge',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:10000',
            'rooms' => 'required|integer|min:1',
            'beds' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
        ]));

        return redirect()->route('host.stays.index')->with('success', 'اقامت‌گاه ویرایش شد.');
    }

    // حذف اقامت‌گاه
    public function destroy(Stay $stay)
    {
        $this->authorizeStay($stay);
        $stay->delete();
        return back()->with('success', 'اقامت‌گاه حذف شد.');
    }

    private function authorizeStay($stay)
    {
        if ($stay->host_id !== Auth::guard('host')->id()) {
            abort(403);
        }
    }
}
