<?php

namespace App\Http\Controllers\Partner\Store;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\Store\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Color\Color;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PartnerTableController extends Controller
{
    public function index()
    {
        $tables = Table::where('partner_id', Auth::id())->get();
        $table_classes = $tables->pluck('table_class')->unique();

        return view('pages.partner.store.tables.index', compact('tables', 'table_classes'));
    }

    public function create()
    {
        $table_classes = Table::where('partner_id', Auth::id())->pluck('table_class')->unique();
        return view('pages.partner.store.tables.create', compact('table_classes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $validated = $request->validate([
                'table_no' => 'required|string|max:255',
                'table_class' => 'required|string|max:255',
                'description' => 'nullable|string',
                'images' => 'nullable|array|max:5',
            ], [
                'table_no.required' => 'Nomor meja wajib diisi.',
                'table_class.required' => 'Kelas meja wajib diisi.',
                'images.array' => 'Gambar harus berupa array.',
                'images.max' => 'Maksimal 5 gambar yang diizinkan.',
            ]);

            $storedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/store/tables', $filename, 'public');

                    // Optional: Compress & resize image
                    $img = Image::make(storage_path('app/public/' . $path));
                    $img->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save();

                    $storedImages[] = [
                        'path'     => 'storage/' . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ];
                }
            }
            //generate table barcode
            $barcode = 'TB' . strtoupper(uniqid());
            //generate table URL
            // $table_url = route('customer.menu.index', [
            //     'partner_slug' => Auth::user()->slug,
            //     'table_code'   => $barcode,
            // ]);

            $table_url = 'customer/' . Auth::user()->slug . '/menu' . '/' . $barcode;

            $table = new Table();
            $table->table_no = $validated['table_no'];
            $table->table_code = $barcode;
            $table->partner_id = Auth::id();
            $table->table_class = $validated['table_class'];
            $table->description = $validated['description'] ?? null;
            $table->status = $request->input('status', 'available');
            $table->images = $storedImages ?? null;
            $table->table_url = $table_url ?? null;
            $table->save();

            DB::commit();

            return redirect()
                ->route('partner.store.tables.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function show(Table $table)
    {
        $data = $table;
        return view('pages.partner.store.tables.show', compact('data'));
    }

    public function edit(Table $table)
    {
        $table_classes = Table::where('partner_id', Auth::id())->pluck('table_class')->unique();
        return view('pages.partner.store.tables.edit', compact('table', 'table_classes'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $table = Table::where('id', $id)
                ->where('partner_id', Auth::id())
                ->firstOrFail();

            $validated = $request->validate([
                'table_no'    => 'required|string|max:255',
                'table_class' => 'required|string|max:255',
                'description' => 'nullable|string',
                'images'      => 'nullable|array|max:5',
            ]);

            $storedImages = $table->images ?? [];

            // 🔹 Hapus gambar jika ada pilihan
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $filename) {
                    $storedImages = array_filter($storedImages, function ($img) use ($filename) {
                        if ($img['filename'] === $filename) {
                            // hapus file fisik juga
                            $path = str_replace('storage/', 'public/', $img['path']);
                            if (Storage::exists($path)) {
                                Storage::delete($path);
                            }
                            return false; // exclude dari array
                        }
                        return true;
                    });
                }
                // re-index array biar rapi
                $storedImages = array_values($storedImages);
            }

            // 🔹 Upload gambar baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('uploads/store/tables', $filename, 'public');

                    // Resize (opsional)
                    $img = Image::make(storage_path('app/public/' . $path));
                    $img->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save();

                    $storedImages[] = [
                        'path'     => 'storage/' . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ];
                }
            }

            $table->update([
                'table_no'    => $validated['table_no'],
                'table_class' => $validated['table_class'],
                'description' => $validated['description'] ?? null,
                'status'      => $request->input('status', $table->status),
                'images'      => $storedImages,
            ]);

            DB::commit();

            return redirect()->route('partner.store.tables.index')
                ->with('success', 'Table updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }


    public function destroy(Table $table)
    {
        // Hapus gambar dari storage
        if ($table->images) {
            foreach ($table->images as $image) {
                if (isset($image['path'])) {
                    $imagePath = str_replace('storage/', '', $image['path']);
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }
        $table->delete();
        return redirect()->route('partner.store.tables.index')->with('success', 'Table deleted successfully!');
    }

    public function generateBarcode($tableId)
    {
        $table = Table::findOrFail($tableId);

        if (!$table->table_url) {
            abort(404, 'Table URL not found');
        }

        $storeName = Auth::user()->name;

        $pngBinary = $this->buildBarcodePng($table, $storeName);

        return response($pngBinary, 200)->header('Content-Type', 'image/png');
    }


    public function generateAllBarcode(Request $request)
    {
        $outlet = Auth::user();

        // ambil semua table milik outlet ini
        $tables = Table::where('partner_id', $outlet->id)
            ->orderBy('table_class')
            ->orderBy('table_no')
            ->get();

        if ($tables->isEmpty()) {
            return back()->with('error', 'Tidak ada meja yang tersedia untuk dibuat barcode.');
        }

        $storeName = $outlet->name;

        // buat array data: tiap item punya table dan src base64 png
        $barcodes = $tables->map(function ($table) use ($storeName) {
            $pngBinary   = $this->buildBarcodePng($table, $storeName);
            $base64      = base64_encode($pngBinary);
            $dataUri     = 'data:image/png;base64,' . $base64;

            return [
                'table' => $table,
                'src'   => $dataUri,
            ];
        });

        // render ke PDF A6
        $pdf = Pdf::loadView('pages.partner.store.tables.pdf.all-barcodes-pdf', [
            'barcodes'  => $barcodes,
            'storeName' => $storeName,
        ])->setPaper('a6', 'portrait'); // ukuran A6, 1 barcode per halaman

        $fileName = 'barcodes-' . Str::slug($storeName) . '.pdf';

        return $pdf->stream($fileName);
    }


    private function buildBarcodePng(Table $table, string $storeName): string
    {
        if (!$table->table_url) {
            throw new \RuntimeException('Table URL not found');
        }

        // Generate QR code normal (tanpa label bawaan!)
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data(url($table->table_url))
            ->size(300)
            ->margin(10)
            ->backgroundColor(new Color(255, 255, 255)) // putih
            ->foregroundColor(new Color(0, 0, 0))       // hitam
            ->build();

        // Ambil string PNG dari QR
        $qrString = $result->getString();
        $qrImage = imagecreatefromstring($qrString);

        $qrWidth  = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // Tambahkan padding hitam
        $paddingTop    = 100;  // diperbesar agar muat teks di atas QR
        $paddingSide   = 40;
        $paddingBottom = 100;

        $finalWidth  = $qrWidth + ($paddingSide * 2);
        $finalHeight = $qrHeight + $paddingTop + $paddingBottom;

        // Buat canvas hitam
        $canvas = imagecreatetruecolor($finalWidth, $finalHeight);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $black);

        // Ambil nama store dan teks table
        $tableText = "Table: {$table->table_no}";
        $fontPath  = public_path('fonts/MotleyForcesRegular-w1rZ3.ttf');

        // ====== TULIS STORE NAME DI ATAS QR ======
        $fontSizeStore = 18;
        $bboxStore     = imagettfbbox($fontSizeStore, 0, $fontPath, $storeName);
        $storeWidth    = $bboxStore[2] - $bboxStore[0];
        $storeHeight   = $bboxStore[1] - $bboxStore[7];
        $xStore        = ($finalWidth - $storeWidth) / 2;
        $yStore        = ($paddingTop / 2) + ($storeHeight / 2);

        imagettftext(
            $canvas,
            $fontSizeStore,
            0,
            (int) $xStore,
            (int) $yStore,
            $white,
            $fontPath,
            $storeName
        );

        // ====== TEMPATKAN QR DI TENGAH ======
        imagecopy($canvas, $qrImage, $paddingSide, $paddingTop, 0, 0, $qrWidth, $qrHeight);

        // ====== TULIS TABLE TEXT DI BAWAH QR ======
        $fontSizeTable = 28;
        $bboxTable     = imagettfbbox($fontSizeTable, 0, $fontPath, $tableText);
        $tableWidth    = $bboxTable[2] - $bboxTable[0];
        $tableHeight   = $bboxTable[1] - $bboxTable[7];
        $xTable        = ($finalWidth - $tableWidth) / 2;
        $yTable        = $qrHeight + $paddingTop + (($paddingBottom + $tableHeight) / 2);

        imagettftext(
            $canvas,
            $fontSizeTable,
            0,
            (int) $xTable,
            (int) $yTable,
            $white,
            $fontPath,
            $tableText
        );

        // Kembalikan sebagai string PNG
        ob_start();
        imagepng($canvas);
        $pngBinary = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($canvas);

        return $pngBinary; // string binary PNG
    }

}
