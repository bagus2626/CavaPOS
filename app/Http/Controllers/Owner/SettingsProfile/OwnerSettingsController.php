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
     * Display the settings page (Detail View - Read Only)
     */
    public function index()
    {
        $owner = Auth::guard('owner')->user();
        return view('pages.owner.settings-profile.index', compact('owner'));
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $owner = Auth::guard('owner')->user();
        return view('pages.owner.settings-profile.edit', compact('owner'));
    }

    /**
     * Update personal information (name, phone, image, password - all optional)
     */
    public function updatePersonalInfo(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $owner = Auth::guard('owner')->user();

        try {
            DB::beginTransaction();

            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'updated_at' => now()
            ];

            // Handle password change (optional)
            if ($request->filled('new_password')) {
                $ownerData = DB::table('owners')->where('id', $owner->id)->first();

                if (!Hash::check($request->current_password, $ownerData->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Current password is incorrect'])
                        ->withInput();
                }

                $updateData['password'] = Hash::make($request->new_password);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $image = Image::make($file);
                $image->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $disk = Storage::disk('public');
                $dir = 'owner-profiles';
                $disk->makeDirectory($dir);

                $basename = Str::uuid()->toString();
                $finalPath = "{$dir}/{$basename}.webp";

                $disk->put($finalPath, (string) $image->encode('webp', 80));

                $ownerData = DB::table('owners')->where('id', $owner->id)->first();

                // Delete old photo
                if ($ownerData->image && $disk->exists($ownerData->image)) {
                    $disk->delete($ownerData->image);
                }

                $updateData['image'] = $finalPath;
            }

            // Handle image removal
            if ($request->input('remove_image') == 1) {
                $ownerData = DB::table('owners')->where('id', $owner->id)->first();

                if ($ownerData->image && Storage::disk('public')->exists($ownerData->image)) {
                    Storage::disk('public')->delete($ownerData->image);
                }

                $updateData['image'] = null;
            }

            // Update database
            DB::table('owners')
                ->where('id', $owner->id)
                ->update($updateData);

            DB::commit();

            $successMessage = 'Profile updated successfully!';
            if ($request->filled('new_password')) {
                $successMessage = 'Profile and password updated successfully!';
            }

            return redirect()->route('owner.user-owner.settings.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
