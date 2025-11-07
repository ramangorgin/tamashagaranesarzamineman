<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountContract;
use App\Models\DiscountContractMember;
use Illuminate\Http\Request;

class DiscountContractController extends Controller
{
    // Indexing the Contracts
    public function index()
    {
        $contracts = DiscountContract::latest()->paginate(10);
        return view('admin.discount_contracts.index', compact('contracts'));
    }

    // Creating new Contracts
    public function create()
    {
        return view('admin.discount_contracts.create');
    }

    // Storing new Contracts
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $contract = DiscountContract::create($validated);
        return redirect()->route('discount-contracts.show', $contract->id)
                         ->with('success', 'قرارداد با موفقیت ایجاد شد.');
    }

    // Showing the Contract
    public function show($id)
    {
        $contract = DiscountContract::with('members')->findOrFail($id);
        return view('admin.discount_contracts.show', compact('contract'));
    }

    // Editing the Contract
    public function edit($id)
    {
        $contract = DiscountContract::findOrFail($id);
        return view('admin.discount_contracts.edit', compact('contract'));
    }

    // Updating the Contract
    public function update(Request $request, $id)
    {
        $contract = DiscountContract::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $contract->update($validated);

        return redirect()->route('discount-contracts.show', $contract->id)
                         ->with('success', 'قرارداد با موفقیت به‌روزرسانی شد.');
    }

    // Deleting the Contract
    public function destroy($id)
    {
        $contract = DiscountContract::findOrFail($id);
        $contract->delete();

        return redirect()->route('discount-contracts.index')
                         ->with('success', 'قرارداد با موفقیت حذف شد.');
    }
}
