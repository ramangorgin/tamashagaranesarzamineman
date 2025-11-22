<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountContract;
use Illuminate\Http\Request;

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
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

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
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'is_active'        => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);

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
}