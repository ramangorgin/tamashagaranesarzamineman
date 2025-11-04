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
        $stays = Stay::latest()->paginate(10);
        return view('admin.stays.index', compact('stays'));
    }

    public function show(Stay $stay)
    {
        return view('admin.stays.show', compact('stay'));
    }


    public function create()
    {
        return view('admin.stays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'rooms' => 'nullable|integer',
            'beds' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'price_per_night' => 'required|numeric',
            'normal_discount' => 'nullable|numeric|min:0|max:100',
            'peak_discount' => 'nullable|numeric|min:0|max:100',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $validated['owner_id'] = auth()->id();

        $stay = Stay::create($validated);

        // Saving Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('stays', 'public');
                StayImage::create([
                    'stay_id' => $stay->id,
                    'image_path' => $path,
                    'is_main' => $index === 0, 
                ]);
            }
        }

        return redirect()->route('stays.index')->with('success', 'اقامتگاه با موفقیت ثبت شد');
    }

    public function edit(Stay $stay)
    {
        return view('admin.stays.edit', compact('stay'));
    }

    public function update(Request $request, Stay $stay)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'rooms' => 'nullable|integer',
            'beds' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'price_per_night' => 'required|numeric',
            'normal_discount' => 'nullable|numeric|min:0|max:100',
            'peak_discount' => 'nullable|numeric|min:0|max:100',
            'images.*' => 'nullable|image|max:4096',
        ]);

        $stay->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('stays', 'public');
                StayImage::create([
                    'stay_id' => $stay->id,
                    'image_path' => $path,
                    'is_main' => false,
                ]);
            }
        }

        return redirect()->route('stays.index')->with('success', 'اطلاعات با موفقیت به‌روزرسانی شد');
    }

    public function destroy(Stay $stay)
    {
        // Deleting images from storage
        foreach ($stay->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $stay->delete();

        return redirect()->route('stays.index')->with('success', 'اقامتگاه حذف شد');
    }

    public function toggleStatus(Stay $stay)
    {
        $stay->is_active = !$stay->is_active;
        $stay->save();

        $msg = $stay->is_active ? 'اقامتگاه فعال شد ✅' : 'اقامتگاه غیرفعال شد ❌';
        return back()->with('success', $msg);
    }

}
