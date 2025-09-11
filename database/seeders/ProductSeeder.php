<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product\Product;
use App\Models\Product\ProductPackage;
use App\Models\Product\PackageSpecificationValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $specifications = DB::table('specifications')->get(); // ambil semua spesifikasi

            for ($i = 1; $i <= 5; $i++) {
                $name = fake()->company();
                $product = Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => fake()->text(100),
                    'created_by' => 1,
                    'modified_by' => 1,
                ]);

                foreach (['Lite', 'Premium', 'Business', 'Custom'] as $packageType) {
                    $package = ProductPackage::create([
                        'product_id' => $product->id,
                        'name' => $packageType,
                        'price' => fake()->randomFloat(2, 100, 999),
                        'monthly_price' => fake()->randomFloat(2, 10, 99),
                        'annual_price' => fake()->randomFloat(2, 100, 999),
                        'description' => fake()->text(60),
                    ]);

                    foreach ($specifications as $spec) {
                        PackageSpecificationValue::create([
                            'product_package_id' => $package->id,
                            'specification_id' => $spec->id,
                            'value' => fake()->word(),
                            'created_by' => 1,
                            'modified_by' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
