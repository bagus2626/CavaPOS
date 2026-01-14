<?php

namespace App\Http\Controllers\Owner\Product;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product\Category;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Product\MasterProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class OwnerCategoryController extends Controller
{
    public function index()
    {
        $owner_id = Auth::id();

        // Ambil SEMUA categories (tanpa pagination di backend)
        $allCategories = Category::where('owner_id', $owner_id)
            ->orderBy('category_order')
            ->get();

        // Format data untuk JavaScript
        $allCategoriesFormatted = $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'category_name' => $category->category_name,
                'description' => $category->description,
                'has_image' => $category->images && isset($category->images['path']),
                'image_path' => $category->images && isset($category->images['path']) ? $category->images['path'] : null,
            ];
        });

        // Simulasi pagination untuk compatibility dengan view
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $categories = new \Illuminate\Pagination\LengthAwarePaginator(
            $allCategories->slice($offset, $perPage)->values(),
            $allCategories->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.owner.products.categories.index', compact('categories', 'allCategoriesFormatted'));
    }

    public function create()
    {
        return view('pages.owner.products.categories.create');
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
            'owner_id'      => Auth::id(),
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'images'        => $imageData, // Laravel simpan sebagai JSON
        ]);

        return redirect()->route('owner.user-owner.categories.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Category $category)
    {
        return view('pages.owner.products.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'images'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'keep_existing_image' => 'nullable|in:0,1',
        ]);

        $imageData = $category->images;

        if ($request->keep_existing_image == '0' && $category->images) {
            // Hapus file fisik dari server
            if (is_array($category->images) && isset($category->images['path'])) {
                $oldPath = public_path($category->images['path']);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            // Set imageData menjadi null
            $imageData = null;
        }

        if ($request->hasFile('images')) {

            if (is_array($category->images) && isset($category->images['path'])) {
                $oldPath = public_path($category->images['path']);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file = $request->file('images');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

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

        return redirect()->route('owner.user-owner.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function reorder(Request $request)
    {
        $owner_id = Auth::id();

        foreach ($request->orders as $item) {
            Category::where('id', $item['id'])
                ->where('owner_id', $owner_id)
                ->update([
                    'category_order' => $item['order']
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated'
        ]);
    }


    public function destroy(Category $category)
    {
        $isUsedByMaster  = MasterProduct::where('category_id', $category->id)->exists();
        $isUsedByPartner = PartnerProduct::where('category_id', $category->id)->exists();

        if ($isUsedByMaster || $isUsedByPartner) {
            return redirect()
                ->route('owner.user-owner.categories.index')
                ->with('swal_error', [
                    'title' => __('messages.owner.products.categories.cannot_delete_title'),
                    'text'  => __('messages.owner.products.categories.cannot_delete_used_text', [
                        'name' => $category->category_name
                    ]),
                ]);
        }

        $images = is_string($category->images)
            ? json_decode($category->images, true)
            : $category->images;

        if (is_array($images)) {
            $paths = isset($images['path'])
                ? [$images['path']]
                : array_column($images, 'path');

            foreach ($paths as $relativePath) {
                if (!$relativePath) continue;

                $relativePath = ltrim(preg_replace('#^public/#', '', $relativePath), '/');
                $fullPath = public_path($relativePath);

                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }

        $category->delete();

        return redirect()
            ->route('owner.user-owner.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
