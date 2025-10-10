<?php

namespace App\Http\Controllers\Owner\Outlet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class OwnerOutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owner = Auth::user();
        // dd($owner);
        $outlets = User::where('owner_id', Auth::id())->get();

        return view('pages.owner.outlet.index', compact('owner', 'outlets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = User::where('owner_id', Auth::id())->get();
        return view('pages.owner.outlet.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $partnerCode = $this->generateUniquePartnerCode();
            $request->merge(['partner_code' => $partnerCode]);

            $imagePath = null;
            $imagePathLogo = null;
            // dd($request->all());
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
                'slug' => ['required', 'string', 'max:255', 'unique:' . User::class],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'province' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'district' => ['required', 'string', 'max:255'],
                'village' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string'],
                'partner_code' => ['required', 'size:4', 'alpha_num', Rule::unique('users', 'partner_code')],
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Baca & perbaiki orientasi EXIF
                $img = Image::make($file->getPathname())->orientate();

                // Batasi dimensi supaya aman (maks 1600x1600, tetap proporsional)
                $img->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $disk = Storage::disk('public');
                $dir  = 'outlets';
                $disk->makeDirectory($dir);

                $basename = Str::uuid()->toString();
                $path     = null;
                $binary   = null;

                // Coba simpan sebagai WebP (ukuran kecil, modern)
                try {
                    $binary = (string) $img->encode('webp', 78); // kualitas 0-100
                    $path   = "{$dir}/{$basename}.webp";
                } catch (\Throwable $e) {
                    // Fallback ke JPEG kalau WebP tidak didukung di server
                    $binary = (string) $img->encode('jpg', 80);
                    $path   = "{$dir}/{$basename}.jpg";
                }

                // Tulis ke disk public
                $disk->put($path, $binary);

                // Simpan path relatif untuk database
                $imagePath = $path;
            }

            if ($request->hasFile('logo')) {
                $fileLogo = $request->file('logo');

                // Baca & perbaiki orientasi EXIF
                $lg = Image::make($fileLogo->getPathname())->orientate();

                // Batasi dimensi supaya aman (maks 1600x1600, tetap proporsional)
                $lg->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $diskLogo = Storage::disk('public');
                $dirLogo  = 'outlets';
                $diskLogo->makeDirectory($dirLogo);

                $basenameLogo = Str::uuid()->toString();
                $pathLogo     = null;
                $binaryLogo   = null;

                // Coba simpan sebagai WebP (ukuran kecil, modern)
                try {
                    $binaryLogo = (string) $lg->encode('webp', 78); // kualitas 0-100
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.webp";
                } catch (\Throwable $e) {
                    // Fallback ke JPEG kalau WebP tidak didukung di server
                    $binaryLogo = (string) $lg->encode('jpg', 80);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.jpg";
                }

                // Tulis ke disk public
                $diskLogo->put($pathLogo, $binaryLogo);

                // Simpan path relatif untuk database
                $imagePathLogo = $pathLogo;
            }
            $auth = Auth::user();
            // dd($auth);

            $user = User::create([
                'name' => $request->name,
                'owner_id' => $auth->id,
                'email' => $request->email,
                'username' => $request->username,
                'role' => 'partner',
                'slug' => $request->slug,
                'partner_code' => $request->partner_code,
                'logo' => $imagePathLogo,
                'background_picture' => $imagePath,
                'password' => Hash::make($request->password),
                'province' => $request->province_name,
                'province_id' => $request->province,
                'city' => $request->city_name,
                'city_id' => $request->city,
                'subdistrict' => $request->district_name,
                'subdistrict_id' => $request->district,
                'urban_village' => $request->village_name,
                'urban_village_id' => $request->village,
                'address' => $request->address,
            ]);

            event(new Registered($user));
            // Auth::login($user);

            DB::commit();
            return redirect()
                ->route('owner.user-owner.outlets.index')
                ->with('success', 'Outlet berhasil dibuat! Selamat datang, ' . $user->name . ' dari ' . $user->city);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function generateUniquePartnerCode(int $maxTries = 50): string
    {
        for ($i = 0; $i < $maxTries; $i++) {
            // Str::random sudah alnum, kita uppercase agar A–Z
            $code = strtoupper(Str::random(4)); // contoh: 7K3B

            // (opsional) hilangkan karakter yang membingungkan:
            // $code = strtr($code, ['O'=>'A','0'=>'1','I'=>'B','l'=>'C']);

            $exists = User::where('partner_code', $code)->exists();
            if (!$exists) return $code;
        }
        throw new \RuntimeException('Gagal membuat partner_code unik setelah beberapa percobaan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $outlet)
    {
        // Pastikan ini milik owner yang login & rolenya partner
        abort_if($outlet->role !== 'partner', 404);
        abort_if($outlet->owner_id !== Auth::id(), 403);

        return view('pages.owner.outlet.edit', compact('outlet'));
    }

    // Update data
    public function update(Request $request, User $outlet)
    {
        // dd($request->all());
        abort_if($outlet->role !== 'partner', 404);
        abort_if($outlet->owner_id !== Auth::id(), 403);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class, 'username')->ignore($outlet->id),
            ],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($outlet->id),
            ],

            // password opsional saat edit
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            // alamat (ID yang dipost adalah kode dari API Emsifa)
            'province' => ['required', 'string', 'max:255'],
            'city'     => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'village'  => ['required', 'string', 'max:255'],
            'address'  => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_qr_active' => ['nullable', 'boolean'],

            'image'    => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            // Upload gambar baru bila ada
            $newImagePath = $outlet->background_picture;
            $newImagePathLogo = $outlet->logo;
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $img = Image::make($file->getPathname())->orientate();
                $img->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                $disk = Storage::disk('public');
                $dir  = 'outlets';
                $disk->makeDirectory($dir);

                $basename = Str::uuid()->toString();

                try {
                    $binary = (string) $img->encode('webp', 78);
                    $path   = "{$dir}/{$basename}.webp";
                } catch (\Throwable $e) {
                    $binary = (string) $img->encode('jpg', 80);
                    $path   = "{$dir}/{$basename}.jpg";
                }

                $disk->put($path, $binary);

                // Hapus lama bila ada
                if ($outlet->background_picture && $disk->exists($outlet->background_picture)) {
                    $disk->delete($outlet->background_picture);
                }

                $newImagePath = $path;
            }

            if ($request->hasFile('logo')) {
                $fileLogo = $request->file('logo');

                $imgLogo = Image::make($fileLogo->getPathname())->orientate();
                $imgLogo->resize(1600, 1600, function ($cLogo) {
                    $cLogo->aspectRatio();
                    $cLogo->upsize();
                });

                $diskLogo = Storage::disk('public');
                $dirLogo  = 'outlets';
                $diskLogo->makeDirectory($dirLogo);

                $basenameLogo = Str::uuid()->toString();

                try {
                    $binaryLogo = (string) $imgLogo->encode('webp', 78);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.webp";
                } catch (\Throwable $e) {
                    $binaryLogo = (string) $imgLogo->encode('jpg', 80);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.jpg";
                }

                $diskLogo->put($pathLogo, $binaryLogo);

                // Hapus lama bila ada
                if ($outlet->logo && $diskLogo->exists($outlet->logo)) {
                    $diskLogo->delete($outlet->logo);
                }

                $newImagePathLogo = $pathLogo;
            }

            // Data update
            $updateData = [
                'name'   => $request->name,
                'email'  => $request->email,
                'username' => $request->username,
                'logo' => $newImagePathLogo,
                'background_picture'   => $newImagePath,

                // alamat simpan nama & id
                'province'         => $request->province_name,
                'province_id'      => $request->province,
                'city'             => $request->city_name,
                'city_id'          => $request->city,
                'subdistrict'      => $request->district_name,
                'subdistrict_id'   => $request->district,
                'urban_village'    => $request->village_name,
                'urban_village_id' => $request->village,
                'address'          => $request->address,
                'is_active'        => $request->is_active ?? 0,
                'is_qr_active'     => $request->is_qr_active ?? 0,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $outlet->update($updateData);

            DB::commit();

            return redirect()
                ->route('owner.user-owner.outlets.index')
                ->with('success', 'Outlet berhasil diperbarui: ' . $outlet->name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $outlet)
    {
        // (opsional tapi bagus) pastikan employee milik partner yang login
        $ownerId = Auth::id();
        $partners = User::where('owner_id', $ownerId)->get();
        abort_if(!$partners->contains($outlet->id), 403);

        try {
            // Hapus file gambar jika ada
            if (!empty($outlet->background_picture)) {
                $disk = Storage::disk('public');       // path DB: "employees/xxxx.webp"
                if ($disk->exists($outlet->background_picture)) {
                    $disk->delete($outlet->background_picture);
                }
            }

            // Hapus record
            $outlet->delete();

            return redirect()
                ->route('owner.user-owner.outlets.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function checkUsername(Request $request)
    {
        $v = Validator::make($request->all(), [
            'username'   => ['required', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._\-]+$/'],
            'exclude_id' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'username.regex' => 'Format username tidak valid.',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $query = User::query()->where('username', $request->username);
        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', (int) $request->exclude_id);
        }

        return response()->json([
            'available' => ! $query->exists(),
        ]);
    }

    public function checkSlug(Request $request)
    {
        $v = Validator::make($request->all(), [
            'slug'       => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9-]+$/'],
            'exclude_id' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'slug.regex' => 'Format slug tidak valid.',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $query = User::query()->where('slug', $request->slug);
        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', (int) $request->exclude_id);
        }

        return response()->json([
            'available' => ! $query->exists(),
        ]);
    }
}
