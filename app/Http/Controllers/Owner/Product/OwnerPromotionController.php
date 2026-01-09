<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Admin\Product\Category;
use App\Models\Product\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class OwnerPromotionController extends Controller
{
    public function index()
    {
        $owner = Auth::user();

        // Get semua data untuk JavaScript filter
        $allPromotions = Promotion::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Format data untuk JavaScript
        $allPromotionsFormatted = $allPromotions->map(function ($promotion) {
            $daysMap = [
                'sun' => __('messages.owner.products.promotions.sunday'),
                'mon' => __('messages.owner.products.promotions.monday'),
                'tue' => __('messages.owner.products.promotions.tuesday'),
                'wed' => __('messages.owner.products.promotions.wednesday'),
                'thu' => __('messages.owner.products.promotions.thursday'),
                'fri' => __('messages.owner.products.promotions.friday'),
                'sat' => __('messages.owner.products.promotions.saturday'),
            ];

            $activeDaysArr = is_array($promotion->active_days) ? $promotion->active_days : [];
            $activeDays = count($activeDaysArr) === 7
                ? 'Setiap Hari'
                : collect($activeDaysArr)->map(fn($d) => $daysMap[$d] ?? $d)->join(', ');

            // Status calculation
            $now = now();
            $start = $promotion->start_date;
            $end = $promotion->end_date;

            if (!$promotion->is_active) {
                $status = 'inactive';
                $statusLabel = __('messages.owner.products.promotions.inactive');
            } else {
                if ($start && $now->lt($start)) {
                    $status = 'will_be_active';
                    $statusLabel = __('messages.owner.products.promotions.will_be_active');
                } elseif ($end && $now->gt($end)) {
                    $status = 'expired';
                    $statusLabel = __('messages.owner.products.promotions.expired');
                } else {
                    $status = 'active';
                    $statusLabel = __('messages.owner.products.promotions.active');
                }
            }

            return [
                'id' => $promotion->id,
                'promotion_code' => $promotion->promotion_code,
                'promotion_name' => $promotion->promotion_name,
                'promotion_type' => $promotion->promotion_type,
                'promotion_value' => $promotion->promotion_value,
                'start_date' => $promotion->start_date ? $promotion->start_date->translatedFormat('d F Y') : null,
                'start_time' => $promotion->start_date ? $promotion->start_date->format('H:i') : null,
                'end_date' => $promotion->end_date ? $promotion->end_date->translatedFormat('d F Y') : null,
                'end_time' => $promotion->end_date ? $promotion->end_date->format('H:i') : null,
                'active_days' => $activeDays,
                'is_active' => (int) $promotion->is_active,
                'status' => $status,
                'status_label' => $statusLabel,
            ];
        });

        // Simulasi pagination object untuk compatibility dengan view
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $promotions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPromotions->slice($offset, $perPage)->values(),
            $allPromotions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.owner.products.promotion.index', compact('promotions', 'allPromotionsFormatted'));
    }

    public function create()
    {
        $categories = Category::where('owner_id', Auth::id())->get();
        return view('pages.owner.products.promotion.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $owner = Auth::user();

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
                'uses_expiry'     => ['required', 'boolean'],
                'start_date'      => ['required_if:uses_expiry,1', 'nullable', 'date'],
                'end_date'        => ['required_if:uses_expiry,1', 'nullable', 'date', 'after:start_date'],
                'is_active'       => ['nullable', 'boolean'],
                'description'     => ['nullable', 'string'],
                'active_days'     => ['nullable', 'array'],
                'active_days.*'   => ['in:mon,tue,wed,thu,fri,sat,sun'],
            ]);

            $promotion = Promotion::create([
                'promotion_code'  => 'PROMO-' . '' . strtoupper(uniqid()),
                'owner_id'        => $owner->id,
                'promotion_name'  => $request->promotion_name,
                'promotion_type'  => $request->promotion_type,
                'promotion_value' => $request->promotion_value,
                'start_date'      => $request->uses_expiry ? $request->start_date : null,
                'end_date'        => $request->uses_expiry ? $request->end_date : null,
                'active_days'     => $request->active_days ? $request->active_days : null,
                'is_active'       => $request->has('is_active') ? (bool)$request->is_active : true,
                'uses_expiry'     => $request->has('uses_expiry') ? (bool)$request->uses_expiry : false,
                'description'     => $request->description,
            ]);

            // dd($promotion);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.promotions.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function show(Promotion $promotion)
    {
        $data = Promotion::where('id', $promotion->id)->first();
        return view('pages.owner.products.promotion.show', compact('data'));
    }

    public function edit(Promotion $promotion)
    {
        $owner = Auth::user();
        // dd($owner);
        $data = Promotion::where('owner_id', $owner->id)
            ->where('id', $promotion->id)
            ->first();

        return view('pages.owner.products.promotion.edit', compact('data'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $owner = Auth::user();

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
                'uses_expiry'     => ['nullable', 'boolean'],
                'start_date'      => ['required_if:uses_expiry,1', 'nullable', 'date'],
                'end_date'        => ['required_if:uses_expiry,1', 'nullable', 'date', 'after:start_date'],
                'is_active'       => ['nullable', 'boolean'],
                'description'     => ['nullable', 'string'],
                'active_days'     => ['nullable', 'array'],
                'active_days.*'   => ['in:mon,tue,wed,thu,fri,sat,sun'],
            ]);

            $uses_expiry = $request->has('uses_expiry') ? (bool)$request->uses_expiry : false;

            // Opsional: guard kepemilikan
            if ((int)$promotion->owner_id !== (int)$owner->id) {
                abort(403);
            }

            $promotion->update([
                'promotion_name'  => $request->promotion_name,
                'promotion_type'  => $request->promotion_type,
                'promotion_value' => $request->promotion_value,
                'uses_expiry'     => $uses_expiry,
                'start_date'      => $request->uses_expiry ? $request->start_date : null,
                'end_date'        => $request->uses_expiry ? $request->end_date : null,
                'active_days'     => $request->active_days ?: null, // model cast ke array
                'is_active'       => $request->has('is_active') ? (bool)$request->is_active : false,
                'description'     => $request->description,
            ]);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.promotions.index')
                ->with('success', 'Promotion updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }



    public function destroy(Promotion $promotion)
    {
        // Hapus semua paket dan spesifikasi terkait

        $promotion->delete();

        return redirect()->route('owner.user-owner.promotions.index')->with('success', 'Promotion deleted successfully!');
    }
}
