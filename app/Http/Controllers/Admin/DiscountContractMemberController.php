<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountContract;
use App\Models\DiscountContractMember;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DiscountContractMemberController extends Controller
{
    // Store member (route: admin.discount_contract_members.store with contract param)
    public function store(Request $request, DiscountContract $discountContract)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'phone'       => 'nullable|string|max:50',
        ]);

        // Normalize phone (basic)
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
        }

        $data['contract_id'] = $discountContract->id;

        try {
            DiscountContractMember::create($data);
        } catch (QueryException $e) {
            return back()->with('error', 'عضو تکراری یا خطا در ذخیره.');
        }

        return back()->with('success', 'عضو اضافه شد.');
    }

    // Edit member (optional view)
    public function edit(DiscountContract $discountContract, DiscountContractMember $discountContractMember)
    {
        return view('admin.discount_contract_members.edit', [
            'contract' => $discountContract,
            'member'   => $discountContractMember
        ]);
    }

    // Update member
    public function update(Request $request, DiscountContract $discountContract, DiscountContractMember $discountContractMember)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'phone'       => 'nullable|string|max:50',
        ]);
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
        }

        try {
            $discountContractMember->update($data);
        } catch (QueryException $e) {
            return back()->with('error', 'خطا در ویرایش (احتمال تکراری).');
        }

        return redirect()
            ->route('admin.discount_contracts.edit', $discountContract)
            ->with('success', 'عضو ویرایش شد.');
    }

    // Delete member
    public function destroy(DiscountContract $discountContract, DiscountContractMember $discountContractMember)
    {
        $discountContractMember->delete();

        return back()->with('success', 'عضو حذف شد.');
    }
}