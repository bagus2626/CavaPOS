<?php

namespace App\Http\Controllers\Owner\SettingsProfile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class OwnerSettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $owner = Auth::guard('owner')->user();

        return view('pages.owner.settings-profile.index', compact('owner'));
    }

    /**
     * Update personal information
     */
    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email,' . Auth::guard('owner')->id(),
            'phone_number' => 'required|string|max:20',
        ]);

        $owner = Auth::guard('owner')->user();

        // Update owner data menggunakan DB Query Builder
        DB::table('owners')
            ->where('id', $owner->id)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Personal information updated successfully'
        ]);
    }

    /**
     * Update profile photo (direct upload)
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB max
        ]);

        try {
            $owner = Auth::guard('owner')->user();
            $file = $request->file('image');

            // Process image
            $image = Image::make($file);

            // Resize to optimal size
            $image->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Save image
            $disk = Storage::disk('public');
            $dir = 'owner-profiles';
            $disk->makeDirectory($dir);

            $basename = Str::uuid()->toString();
            $finalPath = "{$dir}/{$basename}.webp";

            // Encode to WebP
            $disk->put($finalPath, (string) $image->encode('webp', 80));

            // Get owner data
            $ownerData = DB::table('owners')
                ->where('id', $owner->id)
                ->first();

            // Delete old photo if exists
            if ($ownerData->image && $disk->exists($ownerData->image)) {
                $disk->delete($ownerData->image);
            }

            // Update database
            DB::table('owners')
                ->where('id', $owner->id)
                ->update([
                    'image' => $finalPath,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'image_url' => asset('storage/' . $finalPath) . '?v=' . time()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $owner = Auth::guard('owner')->user();

        // Ambil data owner dari database
        $ownerData = DB::table('owners')
            ->where('id', $owner->id)
            ->first();

        // Hapus foto jika ada
        if ($ownerData->image && Storage::disk('public')->exists($ownerData->image)) {
            Storage::disk('public')->delete($ownerData->image);
        }

        // Update database (set image to null)
        DB::table('owners')
            ->where('id', $owner->id)
            ->update([
                'image' => null,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo deleted successfully'
        ]);
    }

    /**
     * Update Logo
     */
    public function updateLogo(Request $request)
    {
        // Fitur ini ditunda dulu
        return response()->json([
            'success' => false,
            'message' => 'Feature not available yet'
        ], 501);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $owner = Auth::guard('owner')->user();

        // Ambil fresh data dari database untuk memastikan password terbaru
        $ownerData = DB::table('owners')
            ->where('id', $owner->id)
            ->first();

        // Verify current password 
        if (!Hash::check($request->current_password, $ownerData->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update Password 
        DB::table('owners')
            ->where('id', $owner->id)
            ->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
