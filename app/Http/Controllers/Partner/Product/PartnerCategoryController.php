<?php

namespace App\Http\Controllers\Partner\Product;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product\Category;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

class PartnerCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('partner_id', Auth::id())->paginate(5);
        return view('pages.partner.products.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.partner.products.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'images' => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG|max:2048',
        ]);

        $imageData = null;

        if ($request->hasFile('images')) {
            $file = $request->file('images');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Compress & resize
            $img = Image::make($file->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath . '/' . $filename, 70);

            $imagePath = 'uploads/categories/' . $filename;

            // Simpan data gambar (array)
            $imageData = [
                'path'     => $imagePath,
                'filename' => $filename,
                'mime'     => $file->getClientMimeType(),
                'size'     => $file->getSize(),
            ];
        }

        Category::create([
            'partner_id'    => Auth::id(), // ambil id user login
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'images'        => $imageData, // Laravel simpan sebagai JSON
        ]);

        return redirect()->route('partner.categories.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Category $category)
    {
        return view('pages.partner.products.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'images'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageData = $category->images; // ambil data lama dulu

        if ($request->hasFile('images')) {
            $file = $request->file('images');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // resize & compress
            $img = Image::make($file->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath . '/' . $filename, 70);

            $imagePath = 'uploads/categories/' . $filename;

            $imageData = [
                'path'     => $imagePath,
                'filename' => $filename,
                'mime'     => $file->getClientMimeType(),
                'size'     => $file->getSize(),
            ];
        }

        $category->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'images'        => $imageData,
        ]);

        return redirect()->route('partner.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('partner.categories.index')->with('success', 'Category deleted successfully.');
    }
}

