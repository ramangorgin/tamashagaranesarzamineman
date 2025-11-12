<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stay;
use App\Models\StayImage;
use App\Models\StayFacility;
use App\Models\StayRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StayBaseController extends Controller
{
    /**
     * نمایش فرم ایجاد اقامتگاه
     */
    public function create()
    {
        $categories = [
            'hotel' => 'هتل',
            'villa' => 'ویلا',
            'apartment' => 'آپارتمان',
            'ecolodge' => 'بوم‌گردی',
            'suite' => 'سوئیت',
            'motel' => 'مسافرخانه',
            'house' => 'خانه',
        ];

        return view('stays.create', compact('categories'));
    }

    /**
     * ذخیره اقامتگاه جدید
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $stay = Stay::create([
                'title' => $request->title,
                'category' => $request->category,
                'province' => $request->province,
                'city' => $request->city,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'area' => $request->area,
                'capacity' => $request->capacity,
                'base_capacity' => $request->base_capacity,
                'extra_capacity' => $request->extra_capacity,
                'bedrooms' => $request->bedrooms,
                'double_beds' => $request->double_beds,
                'single_beds' => $request->single_beds,
                'floor_beds' => $request->floor_beds,
                'iranian_toilets' => $request->iranian_toilets,
                'western_toilets' => $request->western_toilets,
                'bathrooms' => $request->bathrooms,
                'price_per_person' => $request->price_per_person,
                'extra_person_price' => $request->extra_person_price,
                'site_commission' => $request->site_commission,
                'max_discount_normal' => $request->max_discount_normal,
                'max_discount_peak' => $request->max_discount_peak,
                'is_peak' => $request->boolean('is_peak'),
                'is_active' => true,
                'host_id' => Auth::guard('host')->id(),
                'admin_id' => Auth::guard('admin')->id(),
            ]);

            // ذخیره امکانات (Facilities)
            if ($request->has('facilities')) {
                foreach ($request->facilities as $facility) {
                    $stay->facilities()->create([
                        'category' => $facility['category'],
                        'name' => $facility['name'],
                        'icon' => $facility['icon'] ?? null,
                        'is_available' => $facility['is_available'] ?? true,
                    ]);
                }
            }

            // ذخیره قوانین
            if ($request->has('rules')) {
                foreach ($request->rules as $rule) {
                    $stay->rules()->create([
                        'rule_text' => $rule['text'],
                        'is_allowed' => $rule['is_allowed'] ?? true,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route(Auth::guard('admin')->check() ? 'admin.stays.index' : 'host.stays.index')
                ->with('success', 'اقامتگاه با موفقیت ثبت شد.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'خطا در ثبت اقامتگاه: ' . $e->getMessage());
        }
    }

    /**
     * نمایش فرم ویرایش اقامتگاه
     */
    public function edit($id)
    {
        $stay = Stay::with(['facilities', 'rules', 'images'])->findOrFail($id);
        $categories = [
            'hotel' => 'هتل',
            'villa' => 'ویلا',
            'apartment' => 'آپارتمان',
            'ecolodge' => 'بوم‌گردی',
            'suite' => 'سوئیت',
            'motel' => 'مسافرخانه',
            'house' => 'خانه',
        ];

        return view('stays.edit', compact('stay', 'categories'));
    }

    /**
     * به‌روزرسانی اقامتگاه
     */
    public function update(Request $request, $id)
    {
        $stay = Stay::findOrFail($id);
        $stay->update($request->only([
            'title', 'category', 'province', 'city', 'address', 'latitude', 'longitude', 'area',
            'capacity', 'base_capacity', 'extra_capacity', 'bedrooms', 'double_beds',
            'single_beds', 'floor_beds', 'iranian_toilets', 'western_toilets', 'bathrooms',
            'price_per_person', 'extra_person_price', 'site_commission',
            'max_discount_normal', 'max_discount_peak', 'is_peak', 'is_active'
        ]));

        return back()->with('success', 'اقامتگاه با موفقیت ویرایش شد.');
    }

    /**
     * حذف اقامتگاه
     */
    public function destroy($id)
    {
        $stay = Stay::findOrFail($id);
        $stay->delete();
        return back()->with('success', 'اقامتگاه حذف شد.');
    }
}
