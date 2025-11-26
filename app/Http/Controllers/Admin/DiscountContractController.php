<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountContract;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

class DiscountContractController extends Controller
{
    // List contracts
    public function index()
    {
        $contracts = DiscountContract::withCount('members')
            ->latest()
            ->paginate(15);

        return view('admin.discount_contracts.index', compact('contracts'));
    }

    // Show create form
    public function create()
    {
        return view('admin.discount_contracts.create');
    }

    // Store new contract
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_date'       => 'required|string',
            'end_date'         => 'required|string',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        // Normalize Jalali to Gregorian (YYYY-MM-DD)
        $data['start_date'] = $this->normalizeDate($data['start_date']);
        $data['end_date']   = $this->normalizeDate($data['end_date']);

        // Validate chronology after normalization
        if(Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))){
            return back()->withErrors(['end_date' => 'تاریخ پایان نباید قبل از تاریخ شروع باشد.'])->withInput();
        }

        $contract = DiscountContract::create($data);

        return redirect()
            ->route('admin.discount_contracts.edit', $contract)
            ->with('success', 'قرارداد ایجاد شد.');
    }

    // Show single contract
    public function show(DiscountContract $discountContract)
    {
        $discountContract->load('members');
        return view('admin.discount_contracts.show', ['contract' => $discountContract]);
    }

    // Edit form
    public function edit(DiscountContract $discountContract)
    {
        $discountContract->load('members');
        return view('admin.discount_contracts.edit', ['contract' => $discountContract]);
    }

    // Update
    public function update(Request $request, DiscountContract $discountContract)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_date'       => 'required|string',
            'end_date'         => 'required|string',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);

        // Normalize Jalali to Gregorian (YYYY-MM-DD)
        $data['start_date'] = $this->normalizeDate($data['start_date']);
        $data['end_date']   = $this->normalizeDate($data['end_date']);

        // Validate chronology after normalization
        if(Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))){
            return back()->withErrors(['end_date' => 'تاریخ پایان نباید قبل از تاریخ شروع باشد.'])->withInput();
        }

        $discountContract->update($data);

        return redirect()
            ->route('admin.discount_contracts.edit', $discountContract)
            ->with('success', 'قرارداد به‌روزرسانی شد.');
    }

    // Delete
    public function destroy(DiscountContract $discountContract)
    {
        $discountContract->delete();

        return redirect()
            ->route('admin.discount_contracts.index')
            ->with('success', 'قرارداد حذف شد.');
    }

    /**
     * Convert a Jalali date string (like 1404/09/07 or 1404-09-07) to Gregorian YYYY-MM-DD.
     * If already Gregorian, returns as-is.
     */
    protected function normalizeDate(?string $value): string
    {
        $val = trim($value ?? '');
        if($val==='') return Carbon::today()->format('Y-m-d');
        // Detect Jalali by year >= 1300 and presence of slash or Persian digits
        $digitsFa = '۰۱۲۳۴۵۶۷۸۹'; $digitsAr = '٠١٢٣٤٥٦٧٨٩';
        $hasFaDigits = strpbrk($val, $digitsFa) !== false || strpbrk($val, $digitsAr) !== false;
        // unify separators
        $valUnified = str_replace(['.',','], '-', str_replace('/', '-', $val));
        // Convert Persian/Arabic digits to English
        $map = [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ];
        $valEn = strtr($valUnified, $map);
        // Try parse as Y-m-d (Gregorian). If success and year < 1300, consider it Gregorian.
        try {
            $c = Carbon::createFromFormat('Y-m-d', $valEn);
            if($c && $c->year < 1300 && $c->isValid()) return $c->format('Y-m-d');
        } catch (\Exception $e) {}

        // Otherwise parse as Jalali via Verta
        try {
            // Accept forms like 1404-09-07 or 1404/09/07
            $parts = explode('-', $valEn);
            if(count($parts)===3){
                [$jy,$jm,$jd] = $parts;
                // Verta::createJalali expects Y,M,D,H,i,s
                $v = Verta::createJalali((int)$jy,(int)$jm,(int)$jd,0,0,0);
                $g = $v->datetime(); // Carbon instance (Gregorian)
                return $g->format('Y-m-d');
            }
        } catch (\Exception $e) {}

        // Fallback: Carbon parse
        return Carbon::parse($valEn)->format('Y-m-d');
    }
}