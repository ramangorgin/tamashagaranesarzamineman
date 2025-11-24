<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stay;
use App\Models\StayImage;
use Illuminate\Support\Facades\Storage;

class StayImageController extends Controller
{
    public function upload(Request $request, Stay $stay)
    {
        $request->validate([
            'images.*' => 'required|image|max:4096'
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('stays/images', 'public');
            $image = $stay->images()->create(['path' => $path]);
            $uploaded[] = [
                'id' => $image->id,
                'url' => asset('storage/' . $path),
            ];
        }

        return response()->json(['success' => true, 'images' => $uploaded]);
    }

    public function destroy($id)
    {
        $image = StayImage::findOrFail($id);
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();

        return back()->with('success', 'تصویر با موفقیت حذف شد.');
    }
}
