<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stay;

class StayFacilityController extends Controller
{
    public function update(Request $request, Stay $stay)
    {
        $stay->facilities()->delete();

        if ($request->filled('facilities')) {
            foreach ($request->facilities as $facility) {
                $stay->facilities()->create([
                    'category' => $facility['category'],
                    'name' => $facility['name'],
                    'icon' => $facility['icon'] ?? null,
                    'is_available' => true,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
