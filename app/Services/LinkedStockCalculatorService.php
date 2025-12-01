<?php

namespace App\Services;

use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Partner\Products\PartnerProductOptionsRecipe;
use App\Models\Partner\Products\PartnerProductRecipe;
use App\Models\Store\Stock;

class LinkedStockCalculatorService
{
    protected $converter;

    public function __construct(UnitConversionService $converter)
    {
        $this->converter = $converter;
    }

    public function calculateLinkedQuantity(object $item): float
    {
        // Cek runtime apakah item yang dimasukkan adalah opsi atau produk utama
        $isOption = $item instanceof PartnerProductOption;
        $recipes = $this->getRecipes($item->id, $isOption);

        if ($recipes->isEmpty()) {
            return 0.00;
        }

        $minPortions = INF;


        foreach ($recipes as $recipeItem) {

            // Ambil stok bahan mentah
            $ingredientStock = Stock::find($recipeItem->stock_id);

            // Tentukan Stok Fisik Tersedia (Sudah Dikurangi Reservasi)
            $quantityReserved = $ingredientStock->quantity_reserved ?? 0;
            $availablePhysicalQty = $ingredientStock->quantity - $quantityReserved;

            // Periksa: Jika stok fisik yang tersedia <= 0
            if (!$ingredientStock || $availablePhysicalQty <= 0) {
                return 0.00;
            }

            $requiredBaseQty = $recipeItem->quantity_used;
            $availableBaseQty = $availablePhysicalQty;

            if ($requiredBaseQty <= 0) {
                continue;
            }

            $maxPortionsPerIngredient = $availableBaseQty / $requiredBaseQty;

            $minPortions = min($minPortions, $maxPortionsPerIngredient);
        }

        if ($minPortions === INF) {
            return 0.00;
        }

        return floor($minPortions);
    }

    /**
     * Helper untuk mengambil data resep.
     */
    private function getRecipes($itemId, $isOption)
    {
        if ($isOption) {
            // Ambil resep dari tabel opsi
            return PartnerProductOptionsRecipe::where('partner_product_option_id', $itemId)
                ->get();
        }
        // Ambil resep dari tabel produk utama
        return PartnerProductRecipe::where('partner_product_id', $itemId)
            ->get();
    }
}
