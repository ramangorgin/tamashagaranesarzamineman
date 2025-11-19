<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StayController extends Controller
{
    // Utility: detect current role and guard
    protected function role(): string
    {
        if (Auth::guard('admin')->check()) return 'admin';
        if (Auth::guard('host')->check())  return 'host';
        return 'user';
    }
    protected function userId(string $role): ?int
    {
        return $role === 'admin' ? Auth::guard('admin')->id()
             : ($role === 'host' ? Auth::guard('host')->id() : Auth::id());
    }
    protected function viewPath(string $name): string
    {
        $r = $this->role();
        return $r === 'admin' ? "stays.admin.$name" : "host.stays.$name";
    }

    /**
     * نمایش لیست اقامتگاه‌ها
     */
    public function index()
    {
        $role = $this->role();
        $query = Stay::query();

        if ($role === 'host') {
            $query->where('host_id', $this->userId('host'));
        }

        if ($search = request('q')) {
            $query->where(function($q) use ($search){
                $q->where('title','like',"%{$search}%")
                  ->orWhere('id',$search);
            });
        }

        $stays = $query->latest()->paginate(12);

        return view($this->viewPath('index'), compact('stays','role'));
    }

    /**
     * نمایش فرم ایجاد اقامتگاه
     */
    public function create()
    {
        $categories = ['hotel','villa','apartment','ecolodge','suite','motel','house'];
        return view($this->viewPath('create'), compact('categories'));
    }

    /**
     * ذخیره اقامتگاه جدید
     */
    public function store(Request $request)
    {
        $normalize = fn($v)=> $v!==null ? preg_replace('/[^\d]/','',$v) : null;

        $request->merge([
            'price_per_person'   => $normalize($request->price_per_person),
            'extra_person_price' => $normalize($request->extra_person_price),
        ]);

        $data = $request->validate([
            'title'=>'required|string|max:255',
            'category'=>'required|string|max:40',
            'province_id'=>'nullable|string|max:10',
            'province_name'=>'nullable|string|max:80',
            'city_id'=>'nullable|string|max:10',
            'city_name'=>'nullable|string|max:80',
            'county_id'=>'nullable|string|max:10',
            'county_name'=>'nullable|string|max:80',
            'village_name'=>'nullable|string|max:120',
            'address'=>'nullable|string',
            'latitude'=>'nullable|numeric',
            'longitude'=>'nullable|numeric',
            'capacity'=>'required|integer|min:1',
            'base_capacity'=>'required|integer|min:1',
            'extra_capacity'=>'nullable|integer|min:0',
            'area'=>'nullable|integer|min:0',
            'bedrooms'=>'nullable|integer|min:0',
            'double_beds'=>'nullable|integer|min:0',
            'single_beds'=>'nullable|integer|min:0',
            'floor_beds'=>'nullable|integer|min:0',
            'bathrooms'=>'nullable|integer|min:0',
            'iranian_toilets'=>'nullable|integer|min:0',
            'western_toilets'=>'nullable|integer|min:0',
            'price_per_person'=>'required|numeric|min:0',
            'extra_person_price'=>'nullable|numeric|min:0',
            'site_commission'=>'required|numeric|min:0|max:100',
            'max_discount_normal'=>'required|numeric|min:0|max:100',
            'max_discount_peak'=>'required|numeric|min:0|max:100',
            'checkin_time'=>'nullable',
            'checkout_time'=>'nullable',
            'rules_json'=>'nullable|string',
            'images.*'=>'nullable|image|max:2048',
            'main_image_index'=>'nullable|integer|min:0',
        ]);

        $data['host_id'] = $this->userId('host');
        $stay = Stay::create($data);

        // Rules
        if($request->filled('rules_json')){
            $rules = json_decode($request->rules_json,true) ?: [];
            foreach($rules as $r){
                if(!empty($r['rule_text'])){
                    $stay->rules()->create([
                        'rule_text'=>trim($r['rule_text']),
                        'is_allowed'=>!empty($r['is_allowed'])
                    ]);
                }
            }
        }

        // Images
        if($request->hasFile('images')){
            $mainIdx = (int)$request->input('main_image_index',0);
            foreach($request->file('images') as $i=>$file){
                $path = $file->store('stays','public');
                $stay->images()->create([
                    'path'=>'storage/'.$path,
                    'is_main'=> $i===$mainIdx
                ]);
            }
        }

        return redirect()->route('host.stays.edit',$stay)->with('success','اقامت‌گاه ایجاد شد.');
    }

    /**
     * نمایش فرم ویرایش اقامتگاه
     */
    public function edit(Stay $stay)
    {
        $role = $this->role();
        if($role==='host' && $stay->host_id !== $this->userId('host')) abort(403);
        $stay->load(['images','rules']);
        return view($this->viewPath('edit'), compact('stay','role'));
    }

    /**
     * به‌روزرسانی اقامتگاه
     */
    public function update(Request $request, Stay $stay)
    {
        $normalize = function($val){
            if($val===null || $val==='') return null;
            return preg_replace('/[^\d]/','', $val);
        };
        $request->merge([
            'price_per_person'   => $normalize($request->input('price_per_person')),
            'extra_person_price' => $normalize($request->input('extra_person_price')),
        ]);

        $role = $this->role();
        if ($role === 'host' && $stay->host_id !== $this->userId('host')) {
            abort(403);
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'category'=> 'required|in:hotel,villa,apartment,ecolodge,suite,motel,house',
            'province_id'   => 'required|string|max:4',
            'province_name' => 'required|string|max:100',
            'city_id'       => 'required|string|max:4',
            'city_name'     => 'required|string|max:100',
            'county_id'     => 'required|string|max:4',
            'county_name'   => 'required|string|max:100',
            'village_name'  => 'nullable|string|max:120',
            'address'       => 'required|string|max:400',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'base_capacity'   => 'required|integer|min:1',
            'capacity'        => 'required|integer|min:1',
            'extra_capacity'  => 'nullable|integer|min:0',
            'area'            => 'nullable|integer|min:0',
            'bedrooms'        => 'nullable|integer|min:0',
            'double_beds'     => 'nullable|integer|min:0',
            'single_beds'     => 'nullable|integer|min:0',
            'floor_beds'      => 'nullable|integer|min:0',
            'bathrooms'       => 'nullable|integer|min:0',
            'iranian_toilets' => 'nullable|integer|min:0',
            'western_toilets' => 'nullable|integer|min:0',
            'price_per_person'   => 'required|numeric|min:0',
            'extra_person_price' => 'nullable|numeric|min:0',
            'site_commission'    => 'required|numeric|min:0|max:100',
            'max_discount_normal'=> 'required|numeric|min:0|max:100',
            'max_discount_peak'  => 'required|numeric|min:0|max:100',
            'is_active' => $role==='admin' ? 'sometimes|boolean' : 'prohibited',
        ]);

        $data = $request->only([
            'title','category',
            'province_id','province_name','city_id','city_name',
            'county_id','county_name','village_name','address',
            'latitude','longitude',
            'area','capacity','base_capacity','extra_capacity',
            'bedrooms','double_beds','single_beds','floor_beds',
            'iranian_toilets','western_toilets','bathrooms',
            'price_per_person','extra_person_price',
            'site_commission','max_discount_normal','max_discount_peak',
        ]);

        if ($role === 'admin') {
            $data['is_active'] = (bool)$request->input('is_active', $stay->is_active);
        }

        $stay->update($data);

        // Rules replace
        if($request->filled('rules_json')){
            $rules = json_decode($request->rules_json,true) ?: [];
            $stay->rules()->delete();
            foreach($rules as $r){
                if(!empty($r['rule_text'])){
                    $stay->rules()->create([
                        'rule_text'=>trim($r['rule_text']),
                        'is_allowed'=>!empty($r['is_allowed'])
                    ]);
                }
            }
        }

        // Remove images
        if($request->filled('remove_image_ids')){
            $ids = array_filter(explode(',',$request->remove_image_ids));
            if($ids){
                $stay->images()->whereIn('id',$ids)->delete();
            }
        }

        // Existing main
        $existingMain = $request->input('main_image_existing_id');
        if($existingMain){
            $stay->images()->update(['is_main'=>false]);
            $stay->images()->where('id',$existingMain)->update(['is_main'=>true]);
        }

        // New images
        if($request->hasFile('images')){
            $mainIdx = (int)$request->input('main_image_index',-1);
            if($mainIdx>=0 && !$existingMain){
                $stay->images()->update(['is_main'=>false]);
            }
            foreach($request->file('images') as $i=>$file){
                $path = $file->store('stays','public');
                $stay->images()->create([
                    'path'=>'storage/'.$path,
                    'is_main'=> (!$existingMain && $i===$mainIdx)
                ]);
            }
        }

        return back()->with('success','اقامت‌گاه با موفقیت ویرایش شد.');
    }

    // Admin only
    public function toggleStatus(Stay $stay)
    {
        if ($this->role() !== 'admin') abort(403);
        $stay->is_active = !$stay->is_active;
        $stay->save();
        return back()->with('success', $stay->is_active ? 'اقامتگاه فعال شد ✅' : 'اقامتگاه غیرفعال شد ❌');
    }

    // Admin only — global peak switch (if you keep this behavior)
    public function togglePeak()
    {
        if ($this->role() !== 'admin') abort(403);
        $isCurrentlyPeak = Stay::where('is_peak', true)->exists();
        $newStatus = !$isCurrentlyPeak;
        Stay::query()->update(['is_peak' => $newStatus]);
        return back()->with('success', $newStatus ? 'قیمت‌ها در حالت پیک قرار گرفتند ✅' : 'قیمت‌ها از حالت پیک خارج شدند ❌');
    }

    /**
     * حذف اقامتگاه
     */
    public function destroy(Stay $stay)
    {
        $role = $this->role();
        if ($role === 'host' && $stay->host_id !== $this->userId('host')) {
            abort(403);
        }
        $stay->delete();
        return back()->with('success','اقامت‌گاه حذف شد.');
    }

    /**
     * نمایش جزئیات اقامتگاه
     */
    public function show(Stay $stay)
    {
        $role = $this->role();

        // میزبان فقط اقامتگاه خودش را می‌بیند
        if ($role === 'host' && $stay->host_id !== $this->userId('host')) {
            abort(403);
        }

        // کاربر عادی فقط اقامتگاه فعال
        if ($role === 'user' && !$stay->is_active) {
            abort(404);
        }

        $stay->loadMissing(['images','rules']); // اگر روابط وجود دارند

        return view('stays.show', compact('stay','role'));
    }
}
