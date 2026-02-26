<?php

namespace App\Http\Controllers\Employee\Product;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product\Category;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Product\MasterProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class StaffCategoryController extends Controller
{
    /**
     * Ambil owner_id dari employee yang sedang login.
     * Chain: employees.partner_id -> users.id -> users.owner_id (= owners.id)
     */
    private function getOwnerId(): int
    {
        $employee = auth('employee')->user();
        $partner  = User::find($employee->partner_id);

        if (!$partner || !$partner->owner_id) {
            abort(403, 'Owner tidak ditemukan untuk employee ini.');
        }

        return (int) $partner->owner_id;
    }

    public function index()
    {
        $owner_id = $this->getOwnerId();
        $q        = request('q');

        $categories = Category::where('owner_id', $owner_id)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('category_name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderBy('category_order')
            ->paginate(10)
            ->withQueryString();

        $allCategoriesFormatted = Category::where('owner_id', $owner_id)
            ->orderBy('category_order')
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'category_name' => $c->category_name,
                'description'   => $c->description,
                'has_image'     => $c->images && isset($c->images['path']),
                'image_path'    => ($c->images && isset($c->images['path'])) ? $c->images['path'] : null,
            ]);

        $allCategories = Category::where('owner_id', $owner_id)
            ->orderBy('category_order')
            ->get();

        $empRole = strtolower(auth('employee')->user()->role ?? 'manager');

        return view('pages.employee.staff.products.categories.index', compact(
            'categories',
            'allCategoriesFormatted',
            'q',
            'allCategories',
            'empRole'
        ));
    }

    public function create()
    {
        return view('pages.employee.staff.products.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'images'        => 'nullable|image|mimes:jpg,jpeg,png,JPG,JPEG,PNG|max:2048',
        ]);

        $imageData = null;

        if ($request->hasFile('images')) {
            $file            = $request->file('images');
            $filename        = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $img = Image::make($file->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath . '/' . $filename, 70);

            $imageData = [
                'path'     => 'uploads/categories/' . $filename,
                'filename' => $filename,
                'mime'     => $file->getClientMimeType(),
                'size'     => $file->getSize(),
            ];
        }

        $empRole = strtolower(auth('employee')->user()->role ?? 'manager');

        Category::create([
            'owner_id'      => $this->getOwnerId(),
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'images'        => $imageData,
        ]);

        return redirect()->route("employee.{$empRole}.categories.index")
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('pages.employee.staff.products.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name'       => 'required|string|max:255',
            'description'         => 'nullable|string',
            'images'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'keep_existing_image' => 'nullable|in:0,1',
        ]);

        $imageData = $category->images;

        if ($request->keep_existing_image == '1' && $category->images) {
            if (is_array($category->images) && isset($category->images['path'])) {
                $oldPath = public_path($category->images['path']);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $imageData = null;
        }

        if ($request->hasFile('images')) {
            if (is_array($category->images) && isset($category->images['path'])) {
                $oldPath = public_path($category->images['path']);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file            = $request->file('images');
            $filename        = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $img = Image::make($file->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath . '/' . $filename, 70);

            $imageData = [
                'path'     => 'uploads/categories/' . $filename,
                'filename' => $filename,
                'mime'     => $file->getClientMimeType(),
                'size'     => $file->getSize(),
            ];
        }

        $empRole = strtolower(auth('employee')->user()->role ?? 'manager');

        $category->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'images'        => $imageData,
        ]);

        return redirect()->route("employee.{$empRole}.categories.index")
            ->with('success', 'Category updated successfully.');
    }

    public function reorder(Request $request)
    {
        $owner_id = $this->getOwnerId();

        foreach ($request->orders as $item) {
            Category::where('id', $item['id'])
                ->where('owner_id', $owner_id)
                ->update(['category_order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated',
        ]);
    }

    public function destroy(Category $category)
    {
        $isUsedByMaster  = MasterProduct::where('category_id', $category->id)->exists();
        $isUsedByPartner = PartnerProduct::where('category_id', $category->id)->exists();

        $empRole = strtolower(auth('employee')->user()->role ?? 'manager');

        if ($isUsedByMaster || $isUsedByPartner) {
            return redirect()
                ->route("employee.{$empRole}.categories.index")
                ->with('swal_error', [
                    'title' => __('messages.owner.products.categories.cannot_delete_title'),
                    'text'  => __('messages.owner.products.categories.cannot_delete_used_text', [
                        'name' => $category->category_name,
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
                $fullPath     = public_path($relativePath);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }

        $category->delete();

        return redirect()
            ->route("employee.{$empRole}.categories.index")
            ->with('success', 'Category deleted successfully.');
    }
}