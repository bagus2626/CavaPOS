<?php

namespace App\Http\Controllers\Employee\SettingsProfile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class StaffSettingsController extends Controller
{
    private function authEmployee()
    {
        return Auth::guard('employee')->user();
    }

    private function empRole(): string
    {
        return strtolower($this->authEmployee()->role ?? 'manager');
    }

    public function index()
    {
        $employee = $this->authEmployee();
        return view('pages.employee.staff.settings-profile.index', compact('employee'));
    }

    public function edit()
    {
        $employee = $this->authEmployee();
        return view('pages.employee.staff.settings-profile.edit', compact('employee'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_image'     => 'nullable|boolean',
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:8|confirmed',
        ]);

        $employee = $this->authEmployee();

        try {
            DB::beginTransaction();

            $updateData = [
                'name'       => $validated['name'],
                'updated_at' => now(),
            ];

            // Handle password change
            if ($request->filled('new_password')) {
                $employeeData = DB::table('employees')->where('id', $employee->id)->first();

                if (!Hash::check($request->current_password, $employeeData->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Current password is incorrect'])
                        ->withInput();
                }

                $updateData['password'] = Hash::make($request->new_password);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $file  = $request->file('image');
                $image = Image::make($file)->orientate();
                $image->resize(800, 800, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $disk = Storage::disk('public');
                $dir  = 'employee-profiles';
                $disk->makeDirectory($dir);

                $basename  = Str::uuid()->toString();
                $finalPath = "{$dir}/{$basename}.webp";
                $disk->put($finalPath, (string) $image->encode('webp', 80));

                // Hapus foto lama
                $employeeData = DB::table('employees')->where('id', $employee->id)->first();
                if (!empty($employeeData->image) && $disk->exists($employeeData->image)) {
                    $disk->delete($employeeData->image);
                }

                $updateData['image'] = $finalPath;
            }

            // Handle remove image
            if ($request->input('remove_image') == 1) {
                $employeeData = DB::table('employees')->where('id', $employee->id)->first();
                if (!empty($employeeData->image) && Storage::disk('public')->exists($employeeData->image)) {
                    Storage::disk('public')->delete($employeeData->image);
                }
                $updateData['image'] = null;
            }

            DB::table('employees')->where('id', $employee->id)->update($updateData);

            DB::commit();

            $successMessage = $request->filled('new_password')
                ? 'Profile and password updated successfully!'
                : 'Profile updated successfully!';

            return redirect()
                ->route('employee.' . $this->empRole() . '.settings.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()])
                ->withInput();
        }
    }
}