<?php

namespace App\Services;

use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Store\Stock;
use App\Services\LinkedStockCalculatorService;
use Illuminate\Support\Facades\DB;

class StockRecalculationService
{
    protected $calculator;

    public function __construct(LinkedStockCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Menghitung ulang dan memperbarui kuantitas tersimpan (cached quantity) 
     * untuk produk yang menggunakan bahan baku ini.
     *
     * @param Stock $ingredientStock Stok bahan baku yang baru saja diubah.
     * @return void
     */
    public function recalculateLinkedProducts(Stock $ingredientStock): void
    {
        DB::beginTransaction();
        try {
            // Identifikasi SEMUA Produk Utama yang menggunakan bahan baku ini
            $products = PartnerProduct::whereHas('recipes', function ($q) use ($ingredientStock) {
                $q->where('stock_id', $ingredientStock->id);
            })->where('stock_type', 'linked')->get();

            // Identifikasi SEMUA Opsi Produk yang menggunakan bahan baku ini
            $options = PartnerProductOption::whereHas('recipes', function ($q) use ($ingredientStock) {
                $q->where('stock_id', $ingredientStock->id);
            })->where('stock_type', 'linked')->get();


            // Update Produk Utama
            foreach ($products as $product) {
                $newQuantity = $this->calculator->calculateLinkedQuantity($product);
                $product->update([
                    'available_linked_quantity' => $newQuantity
                ]);
            }

            // Update Opsi Produk
            foreach ($options as $option) {
                $newQuantity = $this->calculator->calculateLinkedQuantity($option);
                $option->update([
                    'available_linked_quantity' => $newQuantity
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function recalculateSingleTarget(object $item): void
    {
        // 1. Safety Check (Opsional, tapi disarankan)
        if (!($item instanceof \Illuminate\Database\Eloquent\Model)) {
            throw new \InvalidArgumentException("Item provided is not an Eloquent Model.");
        }

        // 2. Panggil kalkulator inti (LinkedStockCalculatorService)
        // Kalkulator ini menjalankan Analisis Faktor Pembatas
        $newQuantity = $this->calculator->calculateLinkedQuantity($item);

        // 3. Update kolom cache di Model target
        // Note: Kolom 'available_linked_quantity' harus ada di array $fillable Model $item
        $item->update([
            'available_linked_quantity' => $newQuantity
        ]);
    }
}
