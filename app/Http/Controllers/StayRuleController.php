<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stay;

class StayRuleController extends Controller
{
    public function update(Request $request, Stay $stay)
    {
        $stay->rules()->delete();

        if ($request->filled('rules')) {
            foreach ($request->rules as $rule) {
                $stay->rules()->create([
                    'rule_text' => $rule,
                    'is_allowed' => true,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
