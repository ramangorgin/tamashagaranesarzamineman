<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stay;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StayController extends Controller
{
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

    // LIST
    public function index()
    {
        $role = $this->role();
        $query = Stay::query()->with('host');

        if ($role === 'host') {
            $query->where('host_id', $this->userId('host'));
        }

        if ($search = request('q')) {
            $query->where(function($q) use ($search){
                $q->where('title','like',"%{$search}%")
                  ->orWhere('id',$search);
            });
        }

        $stays = $query->latest()->paginate(15);

        return view('stays.index', compact('stays','role'));
    }

    // CREATE FORM
    public function create()
    {
        $role = $this->role();
        $categories = ['hotel','villa','apartment','ecolodge','suite','motel','house'];
        // Admin: allow opening create without host_id; inline host selection will handle
        $host = null;
        if ($role === 'admin' && request('host_id')) {
            $host = \App\Models\Host::findOrFail((int)request('host_id'));
        }
        $beds = \App\Models\Bed::all();
        return view('stays.create', compact('categories','role','host','beds'));
    }

    // STORE
    public function store(Request $request)
    {
        $role = $this->role();

        $normalize = fn($v)=> $v!==null ? preg_replace('/[^\d]/','',$v) : null;
        $request->merge([
            'price_per_person'   => $normalize($request->price_per_person),
            'extra_person_price' => $normalize($request->extra_person_price),
            'price_per_night'    => $normalize($request->price_per_night),
        ]);

        $rules = [
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'category'=>'required|string|in:hotel,villa,apartment,ecolodge,suite,motel,house',
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
            'pricing_mode' => 'required|in:per_person,per_night',
            'price_per_person'=>'required_if:pricing_mode,per_person|nullable|numeric|min:0',
            'price_per_night'=>'required_if:pricing_mode,per_night|nullable|numeric|min:0',
            'extra_person_price'=>'nullable|numeric|min:0',
            'checkin_time'=>'nullable',
            'checkout_time'=>'nullable',
            'rules_json'=>'nullable|string',
            'images.*'=>'nullable|image|max:2048',
            'main_image_index'=>'nullable|integer|min:0',
        ];

        // Hotel-specific validation rules
        $isHotel = $request->input('category') === 'hotel';
        if ($isHotel) {
            $rules += [
                'star_rating' => 'nullable|integer|min:1|max:5',
                'license_number' => 'nullable|string|max:255',
                'has_lobby' => 'nullable|boolean',
                'has_elevator' => 'nullable|boolean',
                'has_restaurant' => 'nullable|boolean',
                'has_parking' => 'nullable|boolean',
                'has_breakfast' => 'nullable|boolean',
                'has_24h_reception' => 'nullable|boolean',
                'room_types' => 'required|array|min:1',
                'room_types.*.title' => 'required|string|max:255',
                'room_types.*.capacity' => 'required|integer|min:1',
                'room_types.*.base_capacity' => 'required|integer|min:1',
                'room_types.*.extra_capacity' => 'nullable|integer|min:0',
                'room_types.*.area' => 'nullable|integer|min:0',
                'room_types.*.price_per_night' => 'required|numeric|min:0',
                'room_types.*.total_rooms' => 'required|integer|min:1',
                'room_types.*.beds' => 'required|array|min:1',
                'room_types.*.beds.*.bed_id' => 'required|integer|exists:beds,id',
                'room_types.*.beds.*.quantity' => 'required|integer|min:1',
            ];
        }
        // host needs commission & discounts; admin auto-zero
        if ($role !== 'admin') {
            $rules += [
                'site_commission'=>'required|numeric|min:0|max:100',
                'min_price_adjustment'=>'required|numeric|min:0|max:100',
                'max_price_adjustment'=>'required|numeric|min:0|max:100',
            ];
        }
        if ($role === 'admin') {
            $rules += [
                'host_id' => 'required|integer|exists:hosts,id',
            ];
        }

        $data = $request->validate($rules);

        // Ensure irrelevant price is nulled for clarity
        if (($data['pricing_mode'] ?? 'per_person') === 'per_person') {
            $data['price_per_night'] = null;
        } else {
            $data['price_per_person'] = null;
        }

        if ($role === 'host') {
            $data['host_id'] = $this->userId('host');
        } else { // admin
            $data['host_id'] = (int)$request->input('host_id');
        }

        // Use database transaction for hotel creation
        DB::beginTransaction();
        try {
            $stay = Stay::create($data);
            
            // Update final prices based on current periods
            $stay->updateFinalPrices();

            // If admin creates a stay, auto-approve and activate immediately
            if ($role === 'admin') {
                $stay->update([
                    'moderation_status'     => 'approved',
                    'approved_by_admin_id'  => $this->userId('admin'),
                    'approved_at'           => now(),
                    'is_active'             => true,
                ]);
            }

            // Hotel-specific creation
            if ($isHotel) {
                // Validate capacity constraints for room types
                $roomTypes = $request->input('room_types', []);
                foreach ($roomTypes as $index => $roomType) {
                    $baseCapacity = (int)($roomType['base_capacity'] ?? 0);
                    $capacity = (int)($roomType['capacity'] ?? 0);
                    $extraCapacity = (int)($roomType['extra_capacity'] ?? 0);
                    
                    if ($baseCapacity > $capacity) {
                        DB::rollBack();
                        return back()->withErrors([
                            "room_types.{$index}.base_capacity" => 'ظرفیت پایه نمی‌تواند بیشتر از ظرفیت کل باشد.'
                        ])->withInput();
                    }
                    
                    if ($extraCapacity < 0) {
                        DB::rollBack();
                        return back()->withErrors([
                            "room_types.{$index}.extra_capacity" => 'ظرفیت اضافی نمی‌تواند منفی باشد.'
                        ])->withInput();
                    }
                    
                    // Validate bed quantities match capacity
                    $beds = $roomType['beds'] ?? [];
                    $totalBedCapacity = 0;
                    foreach ($beds as $bed) {
                        $bedModel = \App\Models\Bed::find($bed['bed_id']);
                        if ($bedModel) {
                            $bedCapacity = $bed['quantity'] ?? 0;
                            // Single bed = 1, double = 2, queen = 2, king = 2, extra = 1
                            $capacityPerBed = in_array($bedModel->code, ['double', 'queen', 'king']) ? 2 : 1;
                            $totalBedCapacity += ($bedCapacity * $capacityPerBed);
                        }
                    }
                    
                    if ($totalBedCapacity !== $capacity) {
                        DB::rollBack();
                        return back()->withErrors([
                            "room_types.{$index}.beds" => "مجموع ظرفیت تخت‌ها ({$totalBedCapacity}) باید با ظرفیت کل ({$capacity}) برابر باشد."
                        ])->withInput();
                    }
                }

                // Create hotel record
                $hotel = Hotel::create([
                    'stay_id' => $stay->id,
                    'star_rating' => $request->input('star_rating'),
                    'license_number' => $request->input('license_number'),
                    'has_lobby' => (bool)$request->input('has_lobby', false),
                    'has_elevator' => (bool)$request->input('has_elevator', false),
                    'has_restaurant' => (bool)$request->input('has_restaurant', false),
                    'has_parking' => (bool)$request->input('has_parking', false),
                    'has_breakfast' => (bool)$request->input('has_breakfast', false),
                    'has_24h_reception' => (bool)$request->input('has_24h_reception', false),
                ]);

                // Create room types
                foreach ($roomTypes as $roomTypeData) {
                    $normalizePrice = fn($v) => $v !== null ? preg_replace('/[^\d]/', '', $v) : null;
                    $pricePerNight = $normalizePrice($roomTypeData['price_per_night'] ?? 0);
                    
                    $roomType = HotelRoomType::create([
                        'hotel_id' => $hotel->id,
                        'title' => $roomTypeData['title'],
                        'capacity' => (int)$roomTypeData['capacity'],
                        'base_capacity' => (int)$roomTypeData['base_capacity'],
                        'extra_capacity' => (int)($roomTypeData['extra_capacity'] ?? 0),
                        'area' => isset($roomTypeData['area']) ? (int)$roomTypeData['area'] : null,
                        'price_per_night' => (int)$pricePerNight,
                        'total_rooms' => (int)$roomTypeData['total_rooms'],
                    ]);

                    // Attach beds
                    $bedsToAttach = [];
                    foreach ($roomTypeData['beds'] ?? [] as $bed) {
                        $bedsToAttach[$bed['bed_id']] = ['quantity' => (int)$bed['quantity']];
                    }
                    $roomType->beds()->attach($bedsToAttach);
                }
            }

            // Rules
            if($request->filled('rules_json')){
                $rulesArr = json_decode($request->rules_json,true) ?: [];
                foreach($rulesArr as $r){
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

            DB::commit();

            $redirectRoute = $role==='admin' ? 'admin.stays.edit' : 'host.stays.edit';
            return redirect()->route($redirectRoute,$stay)->with('success','اقامت‌گاه ایجاد شد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ایجاد اقامت‌گاه: ' . $e->getMessage()])->withInput();
        }
    }

    // EDIT FORM
    public function edit(Stay $stay)
    {
        $role = $this->role();
        if($role==='host' && $stay->host_id !== $this->userId('host')) abort(403);
        $stay->load(['images','rules','host','hotel.roomTypes.beds']);
        $beds = \App\Models\Bed::all();
        return view('stays.edit', compact('stay','role','beds'));
    }

    // UPDATE
    public function update(Request $request, Stay $stay)
    {
        $role = $this->role();
        if ($role === 'host' && $stay->host_id !== $this->userId('host')) abort(403);

        $normalize = fn($v)=> $v!==null && $v!=='' ? preg_replace('/[^\d]/','',$v) : null;
        $request->merge([
            'price_per_person'   => $normalize($request->input('price_per_person')),
            'extra_person_price' => $normalize($request->input('extra_person_price')),
            'price_per_night'    => $normalize($request->input('price_per_night')),
        ]);

        $rules = [
            'title'   => 'required|string|max:255',
            'category'=> 'required|in:hotel,villa,apartment,ecolodge,suite,motel,house',
            'province_id'   => 'required|string|max:10',
            'province_name' => 'required|string|max:80',
            'city_id'       => 'required|string|max:10',
            'city_name'     => 'required|string|max:80',
            'county_id'     => 'required|string|max:10',
            'county_name'   => 'required|string|max:80',
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
            'pricing_mode'       => 'required|in:per_person,per_night',
            'price_per_person'   => 'required_if:pricing_mode,per_person|nullable|numeric|min:0',
            'price_per_night'    => 'required_if:pricing_mode,per_night|nullable|numeric|min:0',
            'extra_person_price' => 'nullable|numeric|min:0',
            'rules_json'         => 'nullable|string',
            'remove_image_ids'   => 'nullable|string',
            'images.*'           => 'nullable|image|max:2048',
            'main_image_index'   => 'nullable|integer|min:0',
            'main_image_existing_id' => 'nullable|integer',
        ];
        if ($role !== 'admin') {
            $rules += [
                'site_commission'    => 'required|numeric|min:0|max:100',
                'min_price_adjustment'=> 'required|numeric|min:0|max:100',
                'max_price_adjustment'  => 'required|numeric|min:0|max:100',
            ];
        }

        $validated = $request->validate($rules);

        // Ensure irrelevant price is nulled for clarity
        if (($validated['pricing_mode'] ?? $stay->pricing_mode ?? 'per_person') === 'per_person') {
            $validated['price_per_night'] = null;
        } else {
            $validated['price_per_person'] = null;
        }

        if ($role === 'admin') {
            $validated['site_commission'] = 0;
            $validated['min_price_adjustment'] = 0;
            $validated['max_price_adjustment'] = 0;
            // Allow admin to change host_id
            if ($request->has('host_id')) {
                $validated['host_id'] = (int)$request->input('host_id');
            }
        }

        $updateFields = [
            'title','category',
            'province_id','province_name','city_id','city_name',
            'county_id','county_name','village_name','address',
            'latitude','longitude',
            'area','capacity','base_capacity','extra_capacity',
            'bedrooms','double_beds','single_beds','floor_beds',
            'iranian_toilets','western_toilets','bathrooms',
            'pricing_mode','price_per_person','price_per_night','extra_person_price',
            'site_commission','min_price_adjustment','max_price_adjustment',
        ];
        
        if ($role === 'admin' && isset($validated['host_id'])) {
            $updateFields[] = 'host_id';
        }

        $stay->update(array_intersect_key($validated, array_flip($updateFields)));
        
        // Update final prices if price fields changed
        if (isset($validated['price_per_person']) || isset($validated['price_per_night']) || isset($validated['extra_person_price'])) {
            $stay->updateFinalPrices();
        }

        // Rules
        if($request->filled('rules_json')){
            $arr = json_decode($request->rules_json,true) ?: [];
            $stay->rules()->delete();
            foreach($arr as $r){
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

        return back()->with('success','اقامت‌گاه به‌روزرسانی شد.');
    }

    // TOGGLE STATUS (admin)
    public function toggleStatus(Stay $stay)
    {
        if ($this->role() !== 'admin') abort(403);
        $stay->is_active = !$stay->is_active;
        $stay->save();
        return back()->with('success', $stay->is_active ? 'فعال شد' : 'غیرفعال شد');
    }

    // TOGGLE PEAK (admin)
    public function togglePeak()
    {
        if ($this->role() !== 'admin') abort(403);
        $isPeak = Stay::where('is_peak', true)->exists();
        Stay::query()->update(['is_peak' => !$isPeak]);
        return back()->with('success', !$isPeak ? 'حالت پیک فعال شد' : 'حالت پیک غیرفعال شد');
    }

    // DESTROY
    public function destroy(Stay $stay)
    {
        $role = $this->role();
        if ($role === 'host' && $stay->host_id !== $this->userId('host')) abort(403);
        $stay->delete();
        return back()->with('success','اقامت‌گاه حذف شد.');
    }

    // APPROVE (admin)
    public function approve(Stay $stay)
    {
        if ($this->role() !== 'admin') abort(403);
        $stay->update([
            'moderation_status'     => 'approved',
            'approved_by_admin_id'  => $this->userId('admin'),
            'approved_at'           => now(),
            'reject_reason'         => null,
            // optionally activate on approve:
            'is_active'             => true,
        ]);
        return back()->with('success','اقامت‌گاه تأیید شد.');
    }

    // REJECT (admin)
    public function reject(Request $request, Stay $stay)
    {
        if ($this->role() !== 'admin') abort(403);
        $data = $request->validate(['reason' => 'nullable|string|max:500']);
        $stay->update([
            'moderation_status'     => 'rejected',
            'approved_by_admin_id'  => null,
            'approved_at'           => null,
            'reject_reason'         => $data['reason'] ?? null,
            // ensure it is not publicly active
            'is_active'             => false,
        ]);
        return back()->with('success','اقامت‌گاه رد شد.');
    }

    // SHOW
    public function show(Stay $stay)
    {
        $role = $this->role();
        // Visibility rules:
        // Admin: can view all.
        // Host: can view own stays always; other hosts' stays only if they are approved & active.
        // Guest/user: can view only approved & active stays.
        if ($role === 'admin') {
            // no restriction
        } elseif ($role === 'host') {
            $isOwner = $stay->host_id === $this->userId('host');
            if (!$isOwner && (!$stay->is_active || $stay->moderation_status !== 'approved')) {
                abort(404); // treat as not found
            }
        } else { // guest/user
            if (!$stay->is_active || $stay->moderation_status !== 'approved') {
                abort(404);
            }
        }
        $stay->loadMissing(['images','rules','host']);
        return view('stays.show', compact('stay','role'));
    }
  

}
