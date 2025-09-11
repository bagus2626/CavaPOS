<?php

namespace App\Http\Controllers\Public;

use App\Models\Product\Product;
use App\Http\Controllers\Controller;

class PriceController extends Controller
{
    public function index()
    {
        $products = Product::with('packages.specificationValues.specification')->get();
        return view('pages.client.price.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('packages.specificationValues.specification');
        return view('pages.client.price.show', compact('product'));
    }

    public function data()
    {
        $products = Product::with('packages.specificationValues.specification')->get();
        return response()->json($products);
    }

}

