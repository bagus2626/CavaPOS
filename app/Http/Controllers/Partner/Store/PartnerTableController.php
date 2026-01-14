<?php

namespace App\Http\Controllers\Partner\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PartnerTableController extends Controller
{
    public function index(Request $request)
    {
        $partnerId = Auth::id();

        // Ambil semua table classes untuk filter dropdown
        $table_classes = Table::where('partner_id', $partnerId)
            ->pluck('table_class')
            ->unique()
            ->values();

        $tableClass = $request->query('table_class');

        // Query untuk semua data
        $tablesQuery = Table::where('partner_id', $partnerId);

        if ($tableClass && $tableClass !== 'all') {
            $tablesQuery->where('table_class', $tableClass);
        }

        // Get semua data untuk JavaScript filter
        $allTables = $tablesQuery->orderBy('table_class')->orderBy('table_no')->get();

        // Format data untuk JavaScript
        $allTablesFormatted = $allTables->map(function ($table) {
            return [
                'id' => $table->id,
                'table_no' => $table->table_no,
                'table_code' => $table->table_code,
                'table_class' => $table->table_class,
                'description' => $table->description,
                'status' => $table->status,
                'images' => $table->images,
                'table_url' => $table->table_url,
            ];
        });

        // Simulasi pagination object untuk compatibility dengan view
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $tables = new \Illuminate\Pagination\LengthAwarePaginator(
            $allTables->slice($offset, $perPage)->values(),
            $allTables->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.partner.store.tables.index', compact(
            'tables',
            'table_classes',
            'tableClass',
            'allTablesFormatted'
        ));
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
            $validated = $request->validate([
                'table_no' => 'required|string|max:255',
                'table_class' => 'required_without:new_table_class|string|max:255',
                'new_table_class' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'images' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ], [
                'table_no.required' => 'Nomor meja wajib diisi.',
                'table_class.required_without' => 'Kelas meja wajib diisi.',
                'images.image' => 'File harus berupa gambar.',
                'images.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            // Handle table class (select or new input)
            $tableClass = $request->filled('new_table_class')
                ? $request->new_table_class
                : $request->table_class;

            // Handle image upload - PENTING: Simpan sebagai ARRAY
            $storedImageData = null;
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/store/tables', $filename, 'public');

                // Compress & resize image
                $img = Image::make(storage_path('app/public/' . $path));
                $img->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save();

                // PENTING: Simpan sebagai ARRAY dengan satu elemen
                $storedImageData = [
                    [
                        'path'     => 'storage/' . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ]
                ];
            }

            $barcode = 'TB' . strtoupper(uniqid());
            $table_url = 'customer/' . Auth::user()->slug . '/menu' . '/' . $barcode;

            $table = new Table();
            $table->table_no = $validated['table_no'];
            $table->table_code = $barcode;
            $table->partner_id = Auth::id();
            $table->table_class = $tableClass;
            $table->description = $validated['description'] ?? null;
            $table->status = $request->input('status', 'available');
            $table->images = $storedImageData; // Akan auto-convert ke JSON karena cast di model
            $table->table_url = $table_url ?? null;
            $table->save();

            DB::commit();

            return redirect()
                ->route('partner.store.tables.index')
                ->with('success', 'Meja berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
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
            $table = Table::where('id', $id)->where('partner_id', Auth::id())->firstOrFail();

            $validated = $request->validate([
                'table_no'    => 'required|string|max:255',
                'table_class' => 'required_without:new_table_class|string|max:255',
                'new_table_class' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'images'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);

            // Handle table class
            $tableClass = $request->filled('new_table_class')
                ? $request->new_table_class
                : $request->table_class;

            $disk = Storage::disk('public');
            $storedImages = $table->images;

            // Logika Hapus Gambar Lama
            if ($request->keep_existing_image == '0' || $request->hasFile('images')) {
                if (!empty($storedImages) && is_array($storedImages)) {
                    foreach ($storedImages as $img) {
                        $relativePath = ltrim(preg_replace('#^storage/#', '', $img['path']), '/');
                        if ($disk->exists($relativePath)) {
                            $disk->delete($relativePath);
                        }
                    }
                }
                $storedImages = null;
            }

            // Simpan Gambar Baru (Jika ada)
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/store/tables', $filename, 'public');

                // Resize & Compress
                $imgRes = Image::make(storage_path('app/public/' . $path));
                $imgRes->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save();

                // PENTING: Simpan sebagai ARRAY dengan satu elemen
                $storedImages = [
                    [
                        'path'     => "storage/" . $path,
                        'filename' => $filename,
                        'mime'     => $image->getClientMimeType(),
                        'size'     => $image->getSize(),
                    ]
                ];
            }

            $table->update([
                'table_no'    => $validated['table_no'],
                'table_class' => $tableClass,
                'description' => $validated['description'] ?? null,
                'status'      => $request->input('status', $table->status),
                'images'      => $storedImages,
            ]);

            DB::commit();
            return redirect()->route('partner.store.tables.index')->with('success', 'Table updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    public function destroy(Table $table)
    {
        if ($table->images && is_array($table->images)) {
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

        $tables = Table::where('partner_id', $outlet->id)
            ->orderBy('table_class')
            ->orderBy('table_no')
            ->get();

        if ($tables->isEmpty()) {
            return back()->with('error', 'Tidak ada meja yang tersedia untuk dibuat barcode.');
        }

        $storeName = $outlet->name;

        $barcodes = $tables->map(function ($table) use ($storeName) {
            $pngBinary   = $this->buildBarcodePng($table, $storeName);
            $base64      = base64_encode($pngBinary);
            $dataUri     = 'data:image/png;base64,' . $base64;

            return [
                'table' => $table,
                'src'   => $dataUri,
            ];
        });

        $pdf = Pdf::loadView('pages.partner.store.tables.pdf.all-barcodes-pdf', [
            'barcodes'  => $barcodes,
            'storeName' => $storeName,
        ])->setPaper('a6', 'portrait');

        $fileName = 'barcodes-' . Str::slug($storeName) . '.pdf';

        return $pdf->stream($fileName);
    }

    private function buildBarcodePng(Table $table, string $storeName): string
    {
        if (!$table->table_url) {
            throw new \RuntimeException('Table URL not found');
        }

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data(url($table->table_url))
            ->size(300)
            ->margin(10)
            ->backgroundColor(new Color(255, 255, 255))
            ->foregroundColor(new Color(0, 0, 0))
            ->build();

        $qrString = $result->getString();
        $qrImage = imagecreatefromstring($qrString);

        $qrWidth  = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        $paddingTop    = 100;
        $paddingSide   = 40;
        $paddingBottom = 100;

        $finalWidth  = $qrWidth + ($paddingSide * 2);
        $finalHeight = $qrHeight + $paddingTop + $paddingBottom;

        $canvas = imagecreatetruecolor($finalWidth, $finalHeight);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $black);

        $tableText = "Table: {$table->table_no}";
        $fontPath  = public_path('fonts/MotleyForcesRegular-w1rZ3.ttf');

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

        imagecopy($canvas, $qrImage, $paddingSide, $paddingTop, 0, 0, $qrWidth, $qrHeight);

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

        ob_start();
        imagepng($canvas);
        $pngBinary = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($canvas);

        return $pngBinary;
    }
}
