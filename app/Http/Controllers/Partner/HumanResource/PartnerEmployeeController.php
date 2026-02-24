<?php

namespace App\Http\Controllers\Partner\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Partner\HumanResource\Employee;
use App\Http\Controllers\Owner\Store\OwnerTableController;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PartnerEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $partner = Auth::user();

        // Ambil filter role dari query string
        $roleFilter = $request->query('role');

        // Query untuk semua data
        $employeesQuery = Employee::where('partner_id', $partner->id);

        if ($roleFilter && $roleFilter !== 'all') {
            $employeesQuery->where('role', $roleFilter);
        }

        // Get semua data untuk JavaScript filter
        $allEmployees = $employeesQuery->orderBy('name')->get();

        // Format data untuk JavaScript
        $allEmployeesFormatted = $allEmployees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'user_name' => $employee->user_name,
                'email' => $employee->email,
                'role' => $employee->role,
                'is_active' => $employee->is_active,
                'image' => $employee->image,
            ];
        });

        // Simulasi pagination object untuk compatibility dengan view
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $employees = new \Illuminate\Pagination\LengthAwarePaginator(
            $allEmployees->slice($offset, $perPage)->values(),
            $allEmployees->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get unique roles untuk filter dropdown
        $roles = Employee::where('partner_id', $partner->id)
            ->pluck('role')
            ->unique()
            ->sort()
            ->values();

        return view('pages.partner.human-resource.employee.index', compact(
            'employees',
            'roles',
            'roleFilter',
            'allEmployeesFormatted'
        ));
    }

    public function create()
    {
        abort(404); // disable for now
        $categories = Category::where('partner_id', Auth::id())->get();
        return view('pages.partner.human-resource.employee.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1) Validasi input
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'username'              => ['required', 'string'],
            'email'                 => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email'],
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

            $auth = Auth::user();
            // 3) Buat record employee
            $employee = Employee::create([
                'partner_id' => $auth->id, // partner yang sedang login
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
                ->route('partner.user-management.employees.index')
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
        $data = Employee::where('partner_id', Auth::id())
            ->where('id', $employee->id)
            ->first();
        return view('pages.partner.human-resource.employee.show', compact('data'));
    }

    public function edit(Employee $employee)
    {
        // Pastikan milik partner yang login
        $partnerId = Auth::id();
        abort_if($employee->partner_id !== $partnerId, 403);

        return view('pages.partner.human-resource.employee.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Pastikan milik partner yang login
        $partnerId = Auth::id();
        abort_if($employee->partner_id !== $partnerId, 403);

        // Validasi
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'username'              => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:employees,user_name,' . $employee->id],
            'email'                 => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email,' . $employee->id],
            'role'                  => ['required', 'in:CASHIER,KITCHEN,WAITER'],
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
                ->route('partner.user-management.employees.index')
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
        abort(404);
        // (opsional tapi bagus) pastikan employee milik partner yang login
        $partnerId = Auth::id();
        if ($employee->partner_id !== $partnerId) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

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
                ->route('partner.user-management.employees.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}
