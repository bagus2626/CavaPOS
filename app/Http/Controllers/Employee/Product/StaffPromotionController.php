<?php

namespace App\Http\Controllers\Employee\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPromotionController extends Controller
{
    /**
     * Ambil owner_id dari employee yang sedang login.
     * Chain: employees.partner_id -> users.id -> users.owner_id (= owners.id)
     */
    private function getOwnerId(): int
    {
        $employee = auth('employee')->user();
        $partner  = User::find($employee->partner_id);

        if (!$partner || !$partner->owner_id) {
            abort(403, 'Owner tidak ditemukan untuk employee ini.');
        }

        return (int) $partner->owner_id;
    }

    public function index()
    {
        $ownerId = $this->getOwnerId();
        $q       = request('q');
        $type    = request('type');

        $promotions = Promotion::where('owner_id', $ownerId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('promotion_code', 'like', "%{$q}%")
                        ->orWhere('promotion_name', 'like', "%{$q}%");
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('promotion_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.employee.staff.products.promotion.index', compact('promotions'));
    }

    public function create()
    {
        return view('pages.employee.staff.products.promotion.create');
    }

    public function store(Request $request)
    {
        $ownerId  = $this->getOwnerId();
        $employee = auth('employee')->user();
        $empRole  = strtolower($employee->role ?? 'manager');

        DB::beginTransaction();
        try {
            $request->validate([
                'promotion_name'  => ['required', 'string', 'max:150'],
                'promotion_type'  => ['required', 'in:percentage,amount'],
                'promotion_value' => [
                    'required',
                    'numeric',
                    function ($attr, $val, $fail) use ($request) {
                        if ($request->promotion_type === 'percentage' && ($val < 1 || $val > 100)) {
                            $fail('Persentase harus 1–100.');
                        }
                        if ($request->promotion_type === 'amount' && $val < 0) {
                            $fail('Nominal tidak boleh negatif.');
                        }
                    }
                ],
                'uses_expiry'   => ['required', 'boolean'],
                'start_date'    => ['required_if:uses_expiry,1', 'nullable', 'date'],
                'end_date'      => ['required_if:uses_expiry,1', 'nullable', 'date', 'after:start_date'],
                'is_active'     => ['nullable', 'boolean'],
                'description'   => ['nullable', 'string'],
                'active_days'   => ['nullable', 'array'],
                'active_days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            ]);

            Promotion::create([
                'promotion_code'  => 'PROMO-' . strtoupper(uniqid()),
                'owner_id'        => $ownerId,
                'promotion_name'  => $request->promotion_name,
                'promotion_type'  => $request->promotion_type,
                'promotion_value' => $request->promotion_value,
                'start_date'      => $request->uses_expiry ? $request->start_date : null,
                'end_date'        => $request->uses_expiry ? $request->end_date : null,
                'active_days'     => $request->active_days ?: null,
                'is_active'       => $request->has('is_active') ? (bool) $request->is_active : true,
                'uses_expiry'     => $request->has('uses_expiry') ? (bool) $request->uses_expiry : false,
                'description'     => $request->description,
            ]);

            DB::commit();

            return redirect()
                ->route("employee.{$empRole}.promotions.index")
                ->with('success', 'Promotion added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(Promotion $promotion)
    {
        $this->authorizePromotion($promotion);
        $data = $promotion;
        return view('pages.employee.staff.products.promotion.show', compact('data'));
    }

    public function edit(Promotion $promotion)
    {
        $this->authorizePromotion($promotion);
        $data = $promotion;
        return view('pages.employee.staff.products.promotion.edit', compact('data'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $this->authorizePromotion($promotion);
        $employee = auth('employee')->user();
        $empRole  = strtolower($employee->role ?? 'manager');

        DB::beginTransaction();
        try {
            $request->validate([
                'promotion_name'  => ['required', 'string', 'max:150'],
                'promotion_type'  => ['required', 'in:percentage,amount'],
                'promotion_value' => [
                    'required',
                    'numeric',
                    function ($attr, $val, $fail) use ($request) {
                        if ($request->promotion_type === 'percentage' && ($val < 1 || $val > 100)) {
                            $fail('Persentase harus 1–100.');
                        }
                        if ($request->promotion_type === 'amount' && $val < 0) {
                            $fail('Nominal tidak boleh negatif.');
                        }
                    }
                ],
                'uses_expiry'   => ['nullable', 'boolean'],
                'start_date'    => ['required_if:uses_expiry,1', 'nullable', 'date'],
                'end_date'      => ['required_if:uses_expiry,1', 'nullable', 'date', 'after:start_date'],
                'is_active'     => ['nullable', 'boolean'],
                'description'   => ['nullable', 'string'],
                'active_days'   => ['nullable', 'array'],
                'active_days.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            ]);

            $uses_expiry = $request->has('uses_expiry') ? (bool) $request->uses_expiry : false;

            $promotion->update([
                'promotion_name'  => $request->promotion_name,
                'promotion_type'  => $request->promotion_type,
                'promotion_value' => $request->promotion_value,
                'uses_expiry'     => $uses_expiry,
                'start_date'      => $uses_expiry ? $request->start_date : null,
                'end_date'        => $uses_expiry ? $request->end_date : null,
                'active_days'     => $request->active_days ?: null,
                'is_active'       => $request->has('is_active') ? (bool) $request->is_active : false,
                'description'     => $request->description,
            ]);

            DB::commit();

            return redirect()
                ->route("employee.{$empRole}.promotions.index")
                ->with('success', 'Promotion updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Promotion $promotion)
    {
        $this->authorizePromotion($promotion);
        $employee = auth('employee')->user();
        $empRole  = strtolower($employee->role ?? 'manager');

        $promotion->delete();

        return redirect()
            ->route("employee.{$empRole}.promotions.index")
            ->with('success', 'Promotion deleted successfully!');
    }

    /**
     * Pastikan promotion milik owner yang sama dengan employee ini.
     */
    private function authorizePromotion(Promotion $promotion): void
    {
        $ownerId = $this->getOwnerId();

        if ((int) $promotion->owner_id !== $ownerId) {
            abort(403, 'Akses ditolak.');
        }
    }
}