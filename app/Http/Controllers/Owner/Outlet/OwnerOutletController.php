<?php

namespace App\Http\Controllers\Owner\Outlet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileOutlet;
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
        $outlets = User::where('owner_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('pages.owner.outlet.index', compact('owner', 'outlets'));
    }

    /**
     * Show the form for creating a new resource.
     * 
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
        try {
            DB::beginTransaction();

            $partnerCode = $this->generateUniquePartnerCode();
            $request->merge(['partner_code' => $partnerCode]);

            $imagePath = null;
            $imagePathLogo = null;

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
                'user_wifi' => ['nullable', 'string', 'max:255'],
                'pass_wifi' => ['nullable', 'string', 'max:255'],
                'is_wifi_shown' => ['nullable', 'boolean'],
                'partner_code' => ['required', 'size:4', 'alpha_num', Rule::unique('users', 'partner_code')],
                'qr_mode' => ['nullable', 'string', 'in:disabled,barcode_only,cashier_only,both'],

                // Validasi untuk field profile_outlet
                'contact_person' => ['nullable', 'string', 'max:255'],
                'contact_phone' => ['nullable', 'string', 'max:20'],
                'gmaps_url' => ['nullable', 'url', 'max:500'],
                'instagram' => ['nullable', 'string', 'max:255'],
                'facebook' => ['nullable', 'string', 'max:255'],
                'twitter' => ['nullable', 'string', 'max:255'],
                'tiktok' => ['nullable', 'string', 'max:255'],
                'whatsapp' => ['nullable', 'string', 'max:20'],
                'website' => ['nullable', 'url', 'max:255'],
            ]);

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
                $path     = null;
                $binary   = null;

                try {
                    $binary = (string) $img->encode('webp', 78);
                    $path   = "{$dir}/{$basename}.webp";
                } catch (\Throwable $e) {
                    $binary = (string) $img->encode('jpg', 80);
                    $path   = "{$dir}/{$basename}.jpg";
                }

                $disk->put($path, $binary);
                $imagePath = $path;
            }

            if ($request->hasFile('logo')) {
                $fileLogo = $request->file('logo');

                $lg = Image::make($fileLogo->getPathname())->orientate();
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

                try {
                    $binaryLogo = (string) $lg->encode('webp', 78);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.webp";
                } catch (\Throwable $e) {
                    $binaryLogo = (string) $lg->encode('jpg', 80);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.jpg";
                }

                $diskLogo->put($pathLogo, $binaryLogo);
                $imagePathLogo = $pathLogo;
            }

            $auth = Auth::user();

            $auth = Auth::user();

            // Tentukan is_qr_active dan is_cashier_active berdasarkan qr_mode
            $qrMode = $request->input('qr_mode', 'disabled');
            $isQrActive = in_array($qrMode, ['barcode_only', 'both']) ? 1 : 0;
            $isCashierActive = in_array($qrMode, ['cashier_only', 'both']) ? 1 : 0;

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
                'user_wifi' => $request->user_wifi,
                'pass_wifi' => $request->pass_wifi,
                'is_active' => $request->is_active ?? 0,
                'is_wifi_shown' => $request->is_wifi_shown ?? 0,
                'is_qr_active' => $isQrActive,
                'is_cashier_active' => $isCashierActive,
            ]);

            // Create profile outlet
            $user->profileOutlet()->create([
                'contact_person' => $request->contact_person,
                'contact_phone'  => $request->contact_phone,
                'gmaps_url'      => $request->gmaps_url,
                'instagram'      => $request->instagram,
                'facebook'       => $request->facebook,
                'twitter'        => $request->twitter,
                'tiktok'         => $request->tiktok,
                'whatsapp'       => $request->whatsapp,
                'website'        => $request->website,
            ]);

            event(new Registered($user));

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
            $code = strtoupper(Str::random(4));
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
        abort_if($outlet->role !== 'partner', 404);
        abort_if($outlet->owner_id !== Auth::id(), 403);

        // Tentukan qr_mode berdasarkan kombinasi is_qr_active dan is_cashier_active
        if ($outlet->is_qr_active && $outlet->is_cashier_active) {
            $outlet->qr_mode = 'both';
        } elseif ($outlet->is_qr_active) {
            $outlet->qr_mode = 'barcode_only';
        } elseif ($outlet->is_cashier_active) {
            $outlet->qr_mode = 'cashier_only';
        } else {
            $outlet->qr_mode = 'disabled';
        }


        return view('pages.owner.outlet.edit', compact('outlet'));
    }

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
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'province' => ['required', 'string', 'max:255'],
            'city'     => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'village'  => ['required', 'string', 'max:255'],
            'address'  => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'qr_mode' => ['nullable', 'string', 'in:disabled,barcode_only,cashier_only,both'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'logo'     => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],

            // flag hapus
            'remove_background_picture' => ['nullable', 'boolean'],
            'remove_logo'              => ['nullable', 'boolean'],

            'user_wifi' => ['nullable', 'string', 'max:255'],
            'pass_wifi' => ['nullable', 'string', 'max:255'],
            'is_wifi_shown' => ['nullable', 'boolean'],

            // Validasi untuk field profile_outlet
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:20'],
            'gmaps_url'      => ['nullable', 'url', 'max:500'],
            'instagram'      => ['nullable', 'string', 'max:255'],
            'facebook'       => ['nullable', 'string', 'max:255'],
            'twitter'        => ['nullable', 'string', 'max:255'],
            'tiktok'         => ['nullable', 'string', 'max:255'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'website'        => ['nullable', 'url', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $disk = Storage::disk('public');

            // ====== HANDLE BACKGROUND PICTURE ======
            $newImagePath = $outlet->background_picture;

            // Jika user klik X (hapus background)
            if ($request->boolean('remove_background_picture')) {
                if ($outlet->background_picture && $disk->exists($outlet->background_picture)) {
                    $disk->delete($outlet->background_picture);
                }
                $newImagePath = null;
            }

            // Jika upload background baru
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $img = Image::make($file->getPathname())->orientate();
                $img->resize(1600, 1600, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

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

                // Hapus file lama kalau masih ada & belum dihapus
                if ($outlet->background_picture && $disk->exists($outlet->background_picture)) {
                    $disk->delete($outlet->background_picture);
                }

                $newImagePath = $path;
            }

            // ====== HANDLE LOGO ======
            $newImagePathLogo = $outlet->logo;

            // Jika user klik X (hapus logo)
            if ($request->boolean('remove_logo')) {
                if ($outlet->logo && $disk->exists($outlet->logo)) {
                    $disk->delete($outlet->logo);
                }
                $newImagePathLogo = null;
            }

            // Jika upload logo baru
            if ($request->hasFile('logo')) {
                $fileLogo = $request->file('logo');

                $imgLogo = Image::make($fileLogo->getPathname())->orientate();
                $imgLogo->resize(1600, 1600, function ($cLogo) {
                    $cLogo->aspectRatio();
                    $cLogo->upsize();
                });

                $dirLogo  = 'outlets';
                $disk->makeDirectory($dirLogo);

                $basenameLogo = Str::uuid()->toString();

                try {
                    $binaryLogo = (string) $imgLogo->encode('webp', 78);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.webp";
                } catch (\Throwable $e) {
                    $binaryLogo = (string) $imgLogo->encode('jpg', 80);
                    $pathLogo   = "{$dirLogo}/{$basenameLogo}.jpg";
                }

                $disk->put($pathLogo, $binaryLogo);

                if ($outlet->logo && $disk->exists($outlet->logo)) {
                    $disk->delete($outlet->logo);
                }

                $newImagePathLogo = $pathLogo;
            }

            // ====== QR MODE ======
            $qrMode         = $request->input('qr_mode', 'disabled');
            $isQrActive     = in_array($qrMode, ['barcode_only', 'both']) ? 1 : 0;
            $isCashierActive = in_array($qrMode, ['cashier_only', 'both']) ? 1 : 0;

            // Data update untuk user
            $updateData = [
                'name'   => $request->name,
                'email'  => $request->email,
                'username' => $request->username,
                'logo'   => $newImagePathLogo,
                'background_picture' => $newImagePath,
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
                'is_qr_active'     => $isQrActive,
                'is_cashier_active' => $isCashierActive,
                'user_wifi'        => $request->user_wifi,
                'pass_wifi'        => $request->pass_wifi,
                'is_wifi_shown'    => $request->is_wifi_shown ?? 0,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $outlet->update($updateData);

            // Update atau create profile outlet
            $profileData = [
                'contact_person' => $request->contact_person,
                'contact_phone'  => $request->contact_phone,
                'gmaps_url'      => $request->gmaps_url,
                'instagram'      => $request->instagram,
                'facebook'       => $request->facebook,
                'twitter'        => $request->twitter,
                'tiktok'         => $request->tiktok,
                'whatsapp'       => $request->whatsapp,
                'website'        => $request->website,
            ];

            if ($outlet->profileOutlet) {
                $outlet->profileOutlet->update($profileData);
            } else {
                $outlet->profileOutlet()->create($profileData);
            }

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
        $ownerId = Auth::id();
        $partners = User::where('owner_id', $ownerId)->get();
        abort_if(!$partners->contains($outlet->id), 403);

        try {
            if (!empty($outlet->background_picture)) {
                $disk = Storage::disk('public');
                if ($disk->exists($outlet->background_picture)) {
                    $disk->delete($outlet->background_picture);
                }
            }

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
