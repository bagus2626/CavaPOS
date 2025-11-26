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

    /**
     * Menghitung kuantitas maksimum produk linked berdasarkan faktor pembatas resep.
     * Logika ini menjalankan Analisis Faktor Pembatas (Limiting Factor Analysis).
     *
     * @param PartnerProduct $product Model produk utama atau opsi produk.
     * @return float Kuantitas maksimum porsi yang tersedia (dibulatkan ke bawah).
     */
    // App/Services/LinkedStockCalculatorService.php

    // ... (di dalam class LinkedStockCalculatorService) ...

    /**
     * Menghitung kuantitas maksimum produk linked berdasarkan faktor pembatas resep.
     *
     * @param object $item Model produk utama atau opsi produk (PartnerProduct/PartnerProductOption).
     * @return float Kuantitas maksimum porsi yang tersedia (dibulatkan ke bawah).
     */
    public function calculateLinkedQuantity(object $item): float // UBAH TIPE HINT
    {
        // Cek runtime apakah item yang dimasukkan adalah opsi atau produk utama
        $isOption = $item instanceof PartnerProductOption;
        $recipes = $this->getRecipes($item->id, $isOption); // Gunakan $item->id

        if ($recipes->isEmpty()) {
            return 0.00;
        }

        $minPortions = INF; // Mulai dengan nilai tak terhingga (Faktor Pembatas)

        foreach ($recipes as $recipeItem) {

            // Ambil stok bahan mentah
            $ingredientStock = Stock::find($recipeItem->stock_id);

            if (!$ingredientStock || $ingredientStock->quantity <= 0) {
                return 0.00;
            }

            $requiredBaseQty = $recipeItem->quantity_used;
            $availableBaseQty = $ingredientStock->quantity;

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
