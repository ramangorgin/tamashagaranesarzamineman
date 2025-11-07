<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountContract;
use App\Models\DiscountContractMember;
use Illuminate\Http\Request;

class DiscountContractMemberController extends Controller
{
    // Adding new member to the contract
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:discount_contracts,id',
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);

        DiscountContractMember::create($validated);

        return back()->with('success', 'عضو جدید با موفقیت اضافه شد.');
    }

    // Editig the Members
    public function edit($id)
    {
        $member = DiscountContractMember::findOrFail($id);
        return view('admin.discount_contract_members.edit', compact('member'));
    }

    // Updating the changes
    public function update(Request $request, $id)
    {
        $member = DiscountContractMember::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
        ]);

        $member->update($validated);

        return back()->with('success', 'مشخصات عضو با موفقیت ویرایش شد.');
    }

    // Deleting Member
    public function destroy($id)
    {
        $member = DiscountContractMember::findOrFail($id);
        $member->delete();

        return back()->with('success', 'عضو با موفقیت حذف شد.');
    }
}
