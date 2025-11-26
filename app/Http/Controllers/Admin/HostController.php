<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    public function quickStore(Request $request)
    {
        $phone = normalize_digits($request->input('phone'));
        $existing = $phone ? Host::where('phone',$phone)->first() : null;
        if($existing){
            if ($request->wantsJson()) {
                return response()->json($existing->only(['id','name','phone','national_id','status']));
            }
            return redirect()->route('admin.stays.create', ['host_id' => $existing->id])
                ->with('success', 'میزبان موجود انتخاب شد. اکنون اقامت‌گاه را ثبت کنید.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:20',
        ],[
            'name.required' => 'نام الزامی است.',
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.max' => 'شماره موبایل نباید بیش از ۲۰ کاراکتر باشد.',
            'national_id.max' => 'کد ملی نباید بیش از ۲۰ کاراکتر باشد.',
        ]);

        $host = Host::create([
            'name' => $data['name'],
            'phone' => $phone ?: $data['phone'],
            'national_id' => $data['national_id'] ?? null,
            'status' => 'approved',
        ]);

        if ($request->wantsJson()) {
            return response()->json($host->only(['id','name','phone','national_id','status']));
        }

        return redirect()->route('admin.stays.create', ['host_id' => $host->id])
            ->with('success', 'میزبان ایجاد شد. حالا اقامت‌گاه را ثبت کنید.');
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $hosts = Host::query()
            ->when($q, function($query) use ($q){
                $query->where('name','like',"%{$q}%")
                      ->orWhere('phone','like',"%{$q}%")
                      ->orWhere('national_id','like',"%{$q}%");
            })
            ->latest()->paginate(20)->withQueryString();

        return view('admin.hosts.index', compact('hosts','q'));
    }

    public function create()
    {
        return view('admin.hosts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'nullable|string|max:255',
            'national_id'=>'nullable|string|max:20|unique:hosts,national_id',
            'phone'=>'required|string|max:20|unique:hosts,phone',
            'email'=>'nullable|email|max:255',
            'postal_code'=>'nullable|string|max:10',
            'province_id'=>'nullable|string|max:4',
            'province_name'=>'nullable|string|max:255',
            'city_id'=>'nullable|string|max:4',
            'city_name'=>'nullable|string|max:255',
            'county_id'=>'nullable|string|max:4',
            'county_name'=>'nullable|string|max:255',
            'village_name'=>'nullable|string|max:255',
            'address'=>'nullable|string|max:500',
            'iban'=>'nullable|string|max:34',
            'bank_name'=>'nullable|string|max:100',
            'account_holder'=>'nullable|string|max:255',
            'status'=>'nullable|in:pending,approved,rejected',
            'rejection_reason'=>'nullable|string|max:500',
            'id_card_image'=>'nullable|image|max:2048',
            'selfie_image'=>'nullable|image|max:2048',
            'business_license'=>'nullable|image|max:2048',
        ]);
        foreach (['id_card_image','selfie_image','business_license'] as $f) {
            if($request->hasFile($f)){
                $path = $request->file($f)->store('hosts','public');
                $data[$f] = 'storage/'.$path;
            }
        }
        $host = Host::create($data);
        return redirect()->route('admin.hosts.show',$host)->with('success','میزبان ایجاد شد.');
    }

    public function show(Host $host)
    {
        return view('admin.hosts.show', compact('host'));
    }

    public function edit(Host $host)
    {
        return view('admin.hosts.edit', compact('host'));
    }

    public function update(Request $request, Host $host)
    {
        $data = $request->validate([
            'name'=>'nullable|string|max:255',
            'national_id'=>'nullable|string|max:20|unique:hosts,national_id,'.$host->id,
            'phone'=>'required|string|max:20|unique:hosts,phone,'.$host->id,
            'email'=>'nullable|email|max:255',
            'postal_code'=>'nullable|string|max:10',
            'province_id'=>'nullable|string|max:4',
            'province_name'=>'nullable|string|max:255',
            'city_id'=>'nullable|string|max:4',
            'city_name'=>'nullable|string|max:255',
            'county_id'=>'nullable|string|max:4',
            'county_name'=>'nullable|string|max:255',
            'village_name'=>'nullable|string|max:255',
            'address'=>'nullable|string|max:500',
            'iban'=>'nullable|string|max:34',
            'bank_name'=>'nullable|string|max:100',
            'account_holder'=>'nullable|string|max:255',
            'status'=>'nullable|in:pending,approved,rejected',
            'rejection_reason'=>'nullable|string|max:500',
            'id_card_image'=>'nullable|image|max:2048',
            'selfie_image'=>'nullable|image|max:2048',
            'business_license'=>'nullable|image|max:2048',
        ]);
        foreach (['id_card_image','selfie_image','business_license'] as $f) {
            if($request->hasFile($f)){
                $path = $request->file($f)->store('hosts','public');
                $data[$f] = 'storage/'.$path;
            }
        }
        $host->update($data);
        return redirect()->route('admin.hosts.show',$host)->with('success','میزبان به‌روزرسانی شد.');
    }

    public function destroy(Host $host)
    {
        $host->delete();
        return redirect()->route('admin.hosts.index')->with('success','میزبان حذف شد.');
    }

    public function approve(Host $host)
    {
        $host->update(['status' => 'approved', 'rejection_reason' => null]);
        return back()->with('success','میزبان تأیید شد.');
    }

    public function reject(Request $request, Host $host)
    {
        $data = $request->validate(['reason' => 'nullable|string|max:500']);
        $host->update(['status' => 'rejected', 'rejection_reason' => $data['reason'] ?? null]);
        return back()->with('success','میزبان رد شد.');
    }
    /**
     * AJAX: search hosts by name, phone, or national_id.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q',''));
        if($q==='') return response()->json([]);
        $hosts = Host::query()
            ->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhere('phone','like',"%{$q}%")
                  ->orWhere('national_id','like',"%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','phone','national_id','status']);
        return response()->json($hosts);
    }

    /**
     * AJAX: lookup host by phone; 404 if not found.
     */
    public function lookupByPhone(Request $request)
    {
        $phone = normalize_digits($request->get('phone',''));
        if($phone==='') return response()->json(['message'=>'phone required'],422);
        $host = Host::where('phone',$phone)->first(['id','name','phone','national_id','status']);
        if(!$host) return response()->json(['message'=>'not found'],404);
        return response()->json($host);
    }
}