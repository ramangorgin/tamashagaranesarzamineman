<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StayImage;
use Illuminate\Support\Facades\Storage;

class StayImageController extends Controller
{
    public function destroy($id)
    {
        $image = StayImage::findOrFail($id);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'تصویر با موفقیت حذف شد.');
    }
}
