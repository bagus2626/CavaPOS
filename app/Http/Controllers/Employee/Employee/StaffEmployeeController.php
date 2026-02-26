<?php

namespace App\Http\Controllers\Employee\Employee;

use App\Http\Controllers\Controller;
use App\Models\Partner\HumanResource\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class StaffEmployeeController extends Controller
{
    /**
     * Employee yang sedang login.
     */
    private function authEmployee(): Employee
    {
        return Auth::guard('employee')->user();
    }

    /**
     * partner_id dari employee yang login.
     */
    private function partnerScope(): int
    {
        return $this->authEmployee()->partner_id;
    }

    /**
     * Role employee dalam lowercase (manager/supervisor).
     */
    private function empRole(): string
    {
        return strtolower($this->authEmployee()->role ?? 'manager');
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        $partnerId = $this->partnerScope();

        $currentPartnerId = $partnerId; // staff hanya bisa lihat outlet sendiri
        $currentRole      = $request->input('role');
        $currentStatus    = $request->input('status');
        $q                = trim((string) $request->input('q', ''));

        // Base query — hanya partner sendiri, kecualikan diri sendiri
        $employeesQuery = Employee::with('partner')
            ->where('partner_id', $partnerId)
            ->where('id', '!=', $this->authEmployee()->id);

        // Filter by role
        if (!empty($currentRole)) {
            $employeesQuery->where('role', $currentRole);
        }

        // Filter by status
        if (!empty($currentStatus)) {
            if ($currentStatus === 'on_duty') {
                $employeesQuery->where('is_active', 1);
            } elseif ($currentStatus === 'off') {
                $employeesQuery->where('is_active', 0);
            }
        }

        // Search
        if ($q !== '') {
            $employeesQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        $employees = $employeesQuery
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        // Available roles untuk filter pills
        $availableRolesQuery = Employee::where('partner_id', $partnerId)
            ->where('id', '!=', $this->authEmployee()->id);

        if ($q !== '') {
            $availableRolesQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        $availableRoles = $availableRolesQuery
            ->distinct()
            ->pluck('role')
            ->filter()
            ->sort()
            ->values();

        // Hitung inactive
        $inactiveCountQuery = Employee::where('partner_id', $partnerId)
            ->where('id', '!=', $this->authEmployee()->id)
            ->where('is_active', 0);

        if ($q !== '') {
            $inactiveCountQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        $inactiveCount = $inactiveCountQuery->count();

        // Partners untuk filter (hanya outlet sendiri)
        $partners = User::where('id', $partnerId)->get();

        return view('pages.employee.staff.employees.index', compact(
            'employees',
            'partners',
            'availableRoles',
            'inactiveCount',
            'currentPartnerId',
            'currentRole',
            'currentStatus',
            'q'
        ));
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $partners = User::where('id', $this->partnerScope())->get();

        return view('pages.employee.staff.employees.create', compact('partners'));
    }

    // =========================================================
    // STORE
    // =========================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'unique:employees,user_name'],
            'email'    => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email'],
            'partner'  => ['required'],
            'role'     => ['required', 'in:CASHIER,KITCHEN'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        // Pastikan partner yang dipilih adalah milik manager ini
        if ((int) $validated['partner'] !== $this->partnerScope()) {
            return back()->withErrors(['partner' => 'Outlet tidak valid.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $this->uploadImage($request->file('image'));
            }

            Employee::create([
                'partner_id' => $validated['partner'],
                'name'       => $validated['name'],
                'user_name'  => $validated['username'],
                'email'      => $validated['email'],
                'role'       => $validated['role'],
                'password'   => Hash::make($validated['password']),
                'is_active'  => $request->boolean('is_active', true),
                'image'      => $imagePath,
            ]);

            DB::commit();

            return redirect()
                ->route('employee.' . $this->empRole() . '.employees.index')
                ->with('success', 'Employee berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        return view('pages.employee.staff.employees.show', compact('employee'));
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        $partners = User::where('id', $this->partnerScope())->get();

        return view('pages.employee.staff.employees.edit', compact('employee', 'partners'));
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function update(Request $request, Employee $employee)
    {
        $this->authorizeEmployee($employee);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'username'     => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:employees,user_name,' . $employee->id],
            'email'        => ['required', 'email:rfc,dns', 'max:254', 'unique:employees,email,' . $employee->id],
            'role'         => ['required', 'in:CASHIER,KITCHEN'],
            'partner'      => ['required'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'    => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        // Pastikan partner yang dipilih adalah milik manager ini
        if ((int) $validated['partner'] !== $this->partnerScope()) {
            return back()->withErrors(['partner' => 'Outlet tidak valid.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $disk = Storage::disk('public');

            // Hapus gambar jika diminta
            if ($request->boolean('remove_image') && $employee->image) {
                if ($disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }
                $employee->image = null;
            }

            // Upload gambar baru
            if ($request->hasFile('image')) {
                if ($employee->image && $disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }
                $employee->image = $this->uploadImage($request->file('image'));
            }

            $employee->name       = $validated['name'];
            $employee->user_name  = $validated['username'];
            $employee->partner_id = $validated['partner'];
            $employee->email      = $validated['email'];
            $employee->role       = $validated['role'];
            $employee->is_active  = $request->boolean('is_active', true);

            if (!empty($validated['password'])) {
                $employee->password = Hash::make($validated['password']);
            }

            $employee->save();

            DB::commit();

            return redirect()
                ->route('employee.' . $this->empRole() . '.employees.index')
                ->with('success', 'Data employee berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mengupdate: ' . $e->getMessage()]);
        }
    }

    // =========================================================
    // DESTROY
    // =========================================================
    public function destroy(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        try {
            if (!empty($employee->image)) {
                $disk = Storage::disk('public');
                if ($disk->exists($employee->image)) {
                    $disk->delete($employee->image);
                }
            }

            $employee->delete();

            return redirect()
                ->route('employee.' . $this->empRole() . '.employees.index')
                ->with('success', 'Employee berhasil dihapus.');

        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    // =========================================================
    // CHECK USERNAME (AJAX)
    // =========================================================
    public function checkUsername(Request $request)
    {
        $validated = $request->validate([
            'username'   => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._-]+$/'],
            'exclude_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $query = Employee::query()->where('user_name', $validated['username']);

        if (!empty($validated['exclude_id'])) {
            $query->where('id', '!=', $validated['exclude_id']);
        }

        $available = !$query->exists();

        return response()->json([
            'username'  => $validated['username'],
            'available' => $available,
            'message'   => $available ? 'Username tersedia' : 'Username sudah dipakai',
        ]);
    }

    // =========================================================
    // HELPER: Authorize — pastikan employee milik partner sendiri
    // =========================================================
    private function authorizeEmployee(Employee $employee): void
    {
        abort_if($employee->partner_id !== $this->partnerScope(), 403);
    }

    // =========================================================
    // HELPER: Upload & compress image (sama persis dengan Owner)
    // =========================================================
    private function uploadImage($file): string
    {
        $img = Image::make($file->getPathname())->orientate();

        $img->resize(1600, 1600, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        $disk     = Storage::disk('public');
        $dir      = 'employees';
        $basename = Str::uuid()->toString();

        $disk->makeDirectory($dir);

        try {
            $binary = (string) $img->encode('webp', 78);
            $path   = "{$dir}/{$basename}.webp";
        } catch (\Throwable $e) {
            $binary = (string) $img->encode('jpg', 80);
            $path   = "{$dir}/{$basename}.jpg";
        }

        $disk->put($path, $binary);

        return $path;
    }
}