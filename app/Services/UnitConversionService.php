<?php

namespace App\Services;

use App\Models\Store\MasterUnit;
use Illuminate\Support\Facades\Cache; // Opsional: Untuk caching unit

class UnitConversionService
{
    /**
     * @param float $displayQuantity Kuantitas yang diinput pengguna.
     * @param int $displayUnitId ID unit yang digunakan pengguna.
     * @return float Kuantitas yang sudah dikonversi ke unit dasar.
     */
    public function convertToBaseUnit(float $displayQuantity, int $displayUnitId): float
    {
        $unit = $this->getUnit($displayUnitId);

        if (!$unit || $unit->base_unit_conversion_value <= 0) {
            return $displayQuantity;
        }

        // Konversi ke unit dasar
        return $displayQuantity * $unit->base_unit_conversion_value;
    }

    /**
     * @param float $baseQuantity Kuantitas yang tersimpan di database.
     * @param int $displayUnitId ID unit yang ingin digunakan sebagai tampilan.
     * @return float Kuantitas yang sudah dikonversi untuk tampilan.
     */
    public function convertToDisplayUnit(float $baseQuantity, int $displayUnitId): float
    {
        $unit = $this->getUnit($displayUnitId);

        if (!$unit || $unit->base_unit_conversion_value <= 0) {
            return $baseQuantity;
        }

        return $baseQuantity / $unit->base_unit_conversion_value;
    }

    /**

     * @param int $unitId
     * @return MasterUnit|null
     */
    private function getUnit(int $unitId): ?MasterUnit
    {
        // Jika Anda ingin mengaktifkan caching, Anda dapat menggunakan Cache::remember
        // return Cache::remember("master_unit_{$unitId}", now()->addHours(1), function () use ($unitId) {
        //     return MasterUnit::find($unitId);
        // });

        return MasterUnit::find($unitId);
    }
}
