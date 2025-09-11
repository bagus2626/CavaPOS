<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::paginate(5);
        return view('pages.admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        $categories = Portfolio::select('category')->distinct()->pluck('category');
        return view('pages.admin.portfolios.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'category' => 'required|string|max:100',
            'image' => 'required|image|max:2048',
        ]);

        $portfolio = Portfolio::create(Arr::except($data, 'image'));

        if ($request->hasFile('image')) {
            $portfolio->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('admin.portfolios.index')->with('success', 'Portfolio created successfully.');
    }



    public function update(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'link' => 'nullable|url',
            'category' => 'required',
            'image' => 'sometimes|image|max:2048', // changed from 'required' to 'sometimes'
        ]);

        $portfolio->update($request->except('image'));

        if ($request->hasFile('image')) {
            $portfolio->clearMediaCollection('images'); // clear existing images first
            $portfolio->addMediaFromRequest('image')
                    ->toMediaCollection('images');
        }

        return redirect()->route('admin.portfolios.index')->with('success', 'Portfolio updated.');
    }

    public function edit(Portfolio $portfolio)
    {
        $categories = Portfolio::select('category')->distinct()->pluck('category');
        return view('pages.admin.portfolios.edit', compact('portfolio', 'categories'));
    }


    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        return back()->with('success', 'Portfolio deleted.');
    }
}
