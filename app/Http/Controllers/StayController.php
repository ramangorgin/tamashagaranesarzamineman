<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StayController extends Controller
{
    /**
     * نمایش لیست اقامتگاه‌ها
     */
    public function index()
    {
        $guard = Auth::getDefaultDriver();

        if ($guard === 'admin') {
            $stays = Stay::with(['host'])
                ->withCount(['images', 'rules', 'facilities'])
                ->latest()
                ->paginate(15);
            $view = 'stays.admin.index';
        } else {
            $stays = Stay::where('host_id', Auth::id())
                ->withCount(['images', 'rules', 'facilities'])
                ->latest()
                ->paginate(10);
            $view = 'stays.host.index';
        }

        return view($view, compact('stays', 'guard'));
    }

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
            $guard = Auth::getDefaultDriver();

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
                'host_id' => ($guard === 'host') ? Auth::id() : null,
                'admin_id' => ($guard === 'admin') ? Auth::id() : null,
            ]);

            // امکانات (Facilities)
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

            // قوانین (Rules)
            if ($request->has('rules')) {
                foreach ($request->rules as $rule) {
                    $stay->rules()->create([
                        'rule_text' => $rule['text'],
                        'is_allowed' => $rule['is_allowed'] ?? true,
                    ]);
                }
            }

            DB::commit();

            $route = ($guard === 'admin') ? 'admin.stays.index' : 'host.stays.index';
            return redirect()->route($route)->with('success', 'اقامتگاه با موفقیت ثبت شد.');

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

        return view('stays.admin.form', compact('stay', 'categories'));
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
     * فعال / غیرفعال کردن اقامتگاه
     */
    public function toggleStatus(Stay $stay)
    {
        $stay->is_active = !$stay->is_active;
        $stay->save();

        $msg = $stay->is_active ? 'اقامتگاه فعال شد ✅' : 'اقامتگاه غیرفعال شد ❌';
        return back()->with('success', $msg);
    }

    /**
     * تغییر وضعیت پیک (Peak)
     */
    public function togglePeak()
    {
        $isCurrentlyPeak = Stay::where('is_peak', true)->exists();
        $newStatus = !$isCurrentlyPeak;

        Stay::query()->update(['is_peak' => $newStatus]);

        $msg = $newStatus ? 'قیمت‌ها در حالت پیک قرار گرفتند ✅' : 'قیمت‌ها از حالت پیک خارج شدند ❌';
        return back()->with('success', $msg);
    }

    /**
     * حذف اقامتگاه
     */
    public function destroy($id)
    {
        $stay = Stay::findOrFail($id);
        $stay->delete();

        return back()->with('success', 'اقامتگاه با موفقیت حذف شد.');
    }
}
