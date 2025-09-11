<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductPackage;
use App\Models\Product\Specification;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('packages.specificationValues.specification')->get();
        return view('pages.admin.products.index', compact('products'));
    }

    public function create()
    {
        $specifications = Specification::all();
        return view('pages.admin.products.create', compact('specifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'slug' => ['required', 'regex:/^[a-z0-9\-]+$/', 'unique:products,slug'],
            'packages' => 'required|array',
        ]);

        $product = Product::create($request->only(['name', 'description', 'slug']));

        foreach ($request->packages as $package) {
            $pkg = $product->packages()->create([
                'name' => $package['name'],
                'monthly_price' => $package['monthly_price'] ?? 0,
                'annual_price' => $package['annual_price'] ?? 0,
                'description' => $package['description'] ?? null,
            ]);

            if (!empty($package['specifications'])) {
                foreach ($package['specifications'] as $spec) {
                    if (!empty($spec['id']) && $spec['value'] !== null && $spec['value'] !== '') {
                        $pkg->specificationValues()->create([
                            'specification_id' => $spec['id'],
                            'value' => $spec['value'],
                        ]);
                    }
                }
            }

        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }


    public function show(Product $product)
    {
        $product->load('packages.specificationValues.specification');
        return view('pages.admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('packages.specificationValues');
        $specifications = Specification::all();

        return view('pages.admin.products.edit', compact('product', 'specifications'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'slug' => ['required', 'regex:/^[a-z0-9\-]+$/', 'unique:products,slug,' . $product->id],
            'packages' => 'required|array',
        ]);

        $product->update($request->only(['name', 'description', 'slug']));

        // Hapus semua paket dan spesifikasi lama
        foreach ($product->packages as $oldPkg) {
            $oldPkg->specificationValues()->delete();
            $oldPkg->delete();
        }

        // Simpan ulang semua paket
        foreach ($request->packages as $package) {
            $pkg = $product->packages()->create([
                'name' => $package['name'],
                'monthly_price' => $package['monthly_price'] ?? 0,
                'annual_price' => $package['annual_price'] ?? 0,
                'description' => $package['description'] ?? null,
            ]);

            if (!empty($package['specifications'])) {
                foreach ($package['specifications'] as $spec) {
                    if (!empty($spec['id']) && $spec['value'] !== null && $spec['value'] !== '') {
                        $pkg->specificationValues()->create([
                            'specification_id' => $spec['id'],
                            'value' => $spec['value'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

}
