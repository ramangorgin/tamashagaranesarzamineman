<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Shared\StayBaseController;
use App\Models\Stay;
use Illuminate\Support\Facades\Auth;

class StayController extends StayBaseController
{
    public function index()
    {
        $stays = Stay::where('host_id', Auth::guard('host')->id())
                    ->withCount(['images', 'rules', 'facilities'])
                    ->latest()
                    ->paginate(10);

        return view('stays.host.index', compact('stays'));
    }
}
