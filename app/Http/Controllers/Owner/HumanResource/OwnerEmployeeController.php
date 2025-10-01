<?php

namespace App\Http\Controllers\Owner\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Partner\HumanResource\Employee;
use App\Models\User;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OwnerEmployeeController extends Controller
{
    public function index()
    {
        $owner_id = Auth::id();
        $partners = User::where('owner_id', $owner_id)->get();
        $partners_ids = $partners->pluck('id');
        $employees = Employee::with('partner')->whereIn('partner_id', $partners_ids)->get();
        $roles = $employees->pluck('role')->unique()->sort()->values();
        return view('pages.owner.human-resource.employee.index', compact('partners', 'employees', 'roles'));
    }

    public function create()
    {
        $owner_id = Auth::id();
        $partners = User::where('owner_id', $owner_id)->get();
        return view('pages.owner.human-resource.employee.create', compact('partners'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // 1) Validasi input
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'username'              => ['required', 'string', 'unique:employees,user_name'],
            'email'                 => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email'],
            'partner'               => ['required'],
            'role'                  => ['required', 'in:CASHIER,KITCHEN,WAITER'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'image'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // max 2MB
            'is_active'             => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            // 2) Kompres & simpan gambar (opsional)
            $imagePath = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Baca & perbaiki orientasi EXIF
                $img = Image::make($file->getPathname())->orientate();

                // Batasi dimensi supaya aman (maks 1600x1600, tetap proporsional)
                $img->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $disk = Storage::disk('public');
                $dir  = 'employees';
                $disk->makeDirectory($dir);

                $basename = Str::uuid()->toString();
                $path     = null;
                $binary   = null;

                // Coba simpan sebagai WebP (ukuran kecil, modern)
                try {
                    $binary = (string) $img->encode('webp', 78); // kualitas 0-100
                    $path   = "{$dir}/{$basename}.webp";
                } catch (\Throwable $e) {
                    // Fallback ke JPEG kalau WebP tidak didukung di server
                    $binary = (string) $img->encode('jpg', 80);
                    $path   = "{$dir}/{$basename}.jpg";
                }

                // Tulis ke disk public
                $disk->put($path, $binary);

                // Simpan path relatif untuk database
                $imagePath = $path;
            }

            // 3) Buat record employee
            $employee = Employee::create([
                'partner_id' => $validated['partner'], // partner yang sedang login
                'name'       => $validated['name'],
                'user_name'  => $validated['username'],
                'email'      => $validated['email'],
                'role'       => $validated['role'],
                'password'   => Hash::make($validated['password']),
                'is_active'  => $request->boolean('is_active', true),
                'image'      => $imagePath, // contoh: employees/xxxx.webp
            ]);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.employees.index')
                ->with('success', 'Employee created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Jika sempat nulis file lalu gagal, bersihkan
            if (!empty($path ?? null) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()]);
        }
    }


    public function show(Employee $employee)
    {
        $data = Employee::where('id', $employee->id)
            ->first();
        $partner = User::where('id', $data->partner_id)->first();
        if ($partner->owner_id != Auth::id()) {
            abort(403);
        }
        return view('pages.owner.human-resource.employee.show', compact('data'));
    }

    public function edit(Employee $employee)
    {
        $owner_id = Auth::id();
        $partners = User::where('owner_id', $owner_id)->get();

        return view('pages.owner.human-resource.employee.edit', compact('employee', 'partners'));
    }

    public function update(Request $request, Employee $employee)
    {
        // dd($request->all());
        // Pastikan milik partner yang login
        $ownerId = Auth::id();
        $partners = User::where('owner_id', $ownerId)->get();
        abort_if(!$partners->contains($employee->partner_id), 403);

        // Validasi
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'username'              => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:employees,user_name,' . $employee->id],
            'email'                 => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email,' . $employee->id],
            'role'                  => ['required', 'in:CASHIER,KITCHEN,WAITER'],
            'partner'               => ['required'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'image'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'             => ['nullable', 'boolean'],
            'remove_image'          => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();
        try {
            $disk = Storage::disk('public');

            // Hapus gambar lama jika diminta
            if ($request->boolean('remove_image') && $employee->image) {
                if ($disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }
                $employee->image = null;
            }

            // Upload & kompres gambar baru (opsional)
            if ($request->hasFile('image')) {
                // Hapus lama dulu jika ada
                if ($employee->image && $disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }

                $file = $request->file('image');
                $img  = Image::make($file->getPathname())->orientate();
                $img->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $dir      = 'employees';
                $basename = (string) Str::uuid();
                $path     = null;

                try {
                    $binary = (string) $img->encode('webp', 78);
                    $path   = "{$dir}/{$basename}.webp";
                } catch (\Throwable $e) {
                    $binary = (string) $img->encode('jpg', 80);
                    $path   = "{$dir}/{$basename}.jpg";
                }

                $disk->put($path, $binary);
                $employee->image = $path; // simpan path relatif
            }

            // Update field lainnya
            $employee->name      = $validated['name'];
            $employee->user_name  = $validated['username'];
            $employee->partner_id = $validated['partner'];
            $employee->email     = $validated['email'];
            $employee->role      = $validated['role'];
            $employee->is_active = $request->boolean('is_active', true);

            // Password hanya jika diisi
            if (!empty($validated['password'])) {
                $employee->password = Hash::make($validated['password']);
            }

            $employee->save();

            DB::commit();
            return redirect()
                ->route('owner.user-owner.employees.index')
                ->with('success', 'Employee updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mengupdate: ' . $e->getMessage()]);
        }
    }


    public function destroy(Employee $employee)
    {
        // (opsional tapi bagus) pastikan employee milik partner yang login
        $ownerId = Auth::id();
        $partners = User::where('owner_id', $ownerId)->get();
        abort_if(!$partners->contains($employee->partner_id), 403);

        try {
            // Hapus file gambar jika ada
            if (!empty($employee->image)) {
                $disk = Storage::disk('public');       // path DB: "employees/xxxx.webp"
                if ($disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }
            }

            // Hapus record
            $employee->delete();

            return redirect()
                ->route('owner.user-owner.employees.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function checkUsername(Request $request)
    {
        $validated = $request->validate([
            'username'   => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._-]+$/'],
            // opsional: saat edit, kirimkan exclude_id agar username miliknya sendiri dianggap valid
            'exclude_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $query = Employee::query()->where('user_name', $validated['username']);

        if (!empty($validated['exclude_id'])) {
            $query->where('id', '!=', $validated['exclude_id']);
        }

        $available = !$query->exists();

        return response()->json([
            'username' => $validated['username'],
            'available' => $available,
            'message'  => $available ? 'Username tersedia' : 'Username sudah dipakai',
        ]);
    }
}
