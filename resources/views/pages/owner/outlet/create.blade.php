@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.create_outlet'))
@section('page_title', __('messages.owner.outlet.all_outlets.create_new_outlet'))

@section('content')
    <div class="container owner-outlet-create">
        <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn btn-outline-choco mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.owner.outlet.all_outlets.back') }}
        </a>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-store text-choco"></i>
                    {{ __('messages.owner.outlet.all_outlets.create_new_outlet') }}
                </h5>
            </div>

            <div class="card-body pt-0">
                {{-- Alerts --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-start gap-2">
                        <i class="fas fa-circle-exclamation mt-1"></i>
                        <div>
                            <strong>{{ __('messages.owner.outlet.all_outlets.re_check_input') }}</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-info">
                        <i class="fas fa-circle-info me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('owner.user-owner.outlets.store') }}" method="POST" enctype="multipart/form-data"
                    id="employeeForm" class="needs-validation" novalidate>
                    @csrf

                    {{-- SECTION: Info Dasar --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-id-card"></i></span>
                            <h6 class="mb-0">{{ __('messages.owner.outlet.all_outlets.base_information') }}</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name"
                                    class="form-label required">{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="ex: Cava Coffee - Malioboro" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- URL cek username --}}
                            <input type="hidden" id="usernameCheckUrl"
                                value="{{ route('owner.user-owner.outlets.check-username') }}">

                            <div class="col-md-6">
                                <label for="username"
                                    class="form-label required">{{ __('messages.owner.outlet.all_outlets.username') }}</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="username" id="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        value="{{ old('username') }}" required minlength="3" maxlength="30"
                                        pattern="^[A-Za-z0-9._\-]+$" autocomplete="username" autocapitalize="none"
                                        spellcheck="false" placeholder="contoh: budi.setiawan">
                                    <button type="button" id="btnCheckUsername" class="btn btn-outline-choco">
                                        <span class="label">{{ __('messages.owner.outlet.all_outlets.check') }}</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                    @error('username')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_1') }}</small>
                                <div id="usernameStatus" class="form-text mt-1"></div>
                            </div>

                            {{-- URL cek slug --}}
                            <input type="hidden" id="slugCheckUrl"
                                value="{{ route('owner.user-owner.outlets.check-slug') }}">

                            <div class="col-md-6">
                                <label for="slug" class="form-label required">Slug</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug') }}" required minlength="3" maxlength="30"
                                        pattern="^[A-Za-z0-9._\-]+$" placeholder="contoh: cava-coffee-malioboro"
                                        autocomplete="off" autocapitalize="none" spellcheck="false">
                                    <button type="button" id="btnCheckSlug" class="btn btn-outline-choco">
                                        <span class="label">{{ __('messages.owner.outlet.all_outlets.check') }}</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                    @error('slug')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_1') }}</small>
                                <div id="slugStatus" class="form-text mt-1"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="email"
                                    class="form-label required">{{ __('messages.owner.outlet.all_outlets.email_outlet') }}</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="name@company.com" required maxlength="254" autocomplete="email"
                                    autocapitalize="off" spellcheck="false" inputmode="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Alamat Outlet --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-location-dot"></i></span>
                            <h6 class="mb-0">{{ __('messages.owner.outlet.all_outlets.outlet_address') }}</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="province"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.province') }}</label>
                                <div class="position-relative">
                                    <select id="province" name="province"
                                        class="form-select w-100 @error('province') is-invalid @enderror" disabled>
                                        <option value="">{{ __('messages.owner.outlet.all_outlets.load_province') }}
                                        </option>
                                    </select>
                                    <span class="loading-spinner d-none" id="spnProvince"></span>
                                </div>
                                <input type="hidden" id="province_name" name="province_name"
                                    value="{{ old('province_name') }}">
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="city"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.city') }}</label>
                                <div class="position-relative">
                                    <select id="city" name="city"
                                        class="form-select w-100 @error('city') is-invalid @enderror" disabled>
                                        <option value="">
                                            {{ __('messages.owner.outlet.all_outlets.select_province_first') }}</option>
                                    </select>
                                    <span class="loading-spinner d-none" id="spnCity"></span>
                                </div>
                                <input type="hidden" id="city_name" name="city_name" value="{{ old('city_name') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="district"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.district') }}</label>
                                <div class="position-relative">
                                    <select id="district" name="district"
                                        class="form-select w-100 @error('district') is-invalid @enderror" disabled>
                                        <option value="">
                                            {{ __('messages.owner.outlet.all_outlets.select_city_first') }}</option>
                                    </select>
                                    <span class="loading-spinner d-none" id="spnDistrict"></span>
                                </div>
                                <input type="hidden" id="district_name" name="district_name"
                                    value="{{ old('district_name') }}">
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="village"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.village') }}</label>
                                <div class="position-relative">
                                    <select id="village" name="village"
                                        class="form-select w-100 @error('village') is-invalid @enderror" disabled>
                                        <option value="">
                                            {{ __('messages.owner.outlet.all_outlets.select_district_first') }}</option>
                                    </select>
                                    <span class="loading-spinner d-none" id="spnVillage"></span>
                                </div>
                                <input type="hidden" id="village_name" name="village_name"
                                    value="{{ old('village_name') }}">
                                @error('village')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address"
                                    class="form-label mt-2">{{ __('messages.owner.outlet.all_outlets.detail_address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                    <input type="text" id="address" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        value="{{ old('address') }}"
                                        placeholder="{{ __('messages.owner.outlet.all_outlets.detail_address_placeholder') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="gmaps_url" class="form-label">Google Maps Embed URL</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                    <input type="url" id="gmaps_url" name="gmaps_url"
                                        class="form-control @error('gmaps_url') is-invalid @enderror"
                                        value="{{ old('gmaps_url') }}"
                                        placeholder="https://www.google.com/maps/embed?pb=...">
                                    @error('gmaps_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Copy embed URL dari Google Maps (Share → Embed a
                                    map)
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Kontak Person --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-address-book"></i></span>
                            <h6 class="mb-0">Kontak Person</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="contact_person" class="form-label">{{ __('Contact Name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" id="contact_person" name="contact_person"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        value="{{ old('contact_person') }}" placeholder="Name">
                                </div>
                                @error('contact_person')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label for="contact_phone" class="form-label">{{ __('Phone Number') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" id="contact_phone" name="contact_phone"
                                        class="form-control @error('contact_phone') is-invalid @enderror"
                                        value="{{ old('contact_phone') }}" placeholder="08123456789">
                                </div>
                                @error('contact_phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Sosial Media --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-share-nodes"></i></span>
                            <h6 class="mb-0">{{ __('Social Media') }}</h6>
                        </div>

                        <div class="row g-4">
                            {{-- Kolom Kiri --}}
                            <div class="col-lg-6">
                                <div class="d-grid gap-3">
                                    <div class="form-group">
                                        <label for="instagram" class="form-label">
                                            <i class="fab fa-instagram me-1"></i>Instagram
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" id="instagram" name="instagram"
                                                class="form-control @error('instagram') is-invalid @enderror"
                                                value="{{ old('instagram') }}" placeholder="username">
                                        </div>
                                        @error('instagram')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="twitter" class="form-label">
                                            <i class="fab fa-twitter me-1"></i>Twitter / X
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" id="twitter" name="twitter"
                                                class="form-control @error('twitter') is-invalid @enderror"
                                                value="{{ old('twitter') }}" placeholder="username">
                                        </div>
                                        @error('twitter')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="whatsapp" class="form-label">
                                            <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">+62</span>
                                            <input type="tel" id="whatsapp" name="whatsapp"
                                                class="form-control @error('whatsapp') is-invalid @enderror"
                                                value="{{ old('whatsapp') }}" placeholder="8123456789">
                                        </div>
                                        <small class="text-muted">{{ __('Format: 628xxx (tanpa +)') }}</small>
                                        @error('whatsapp')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="col-lg-6">
                                <div class="d-grid gap-3">
                                    <div class="form-group">
                                        <label for="facebook" class="form-label">
                                            <i class="fab fa-facebook me-1"></i>Facebook
                                        </label>
                                        <input type="text" id="facebook" name="facebook"
                                            class="form-control @error('facebook') is-invalid @enderror"
                                            value="{{ old('facebook') }}" placeholder="facebook.com/yourpage">
                                        @error('facebook')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="tiktok" class="form-label">
                                            <i class="fab fa-tiktok me-1"></i>TikTok
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input type="text" id="tiktok" name="tiktok"
                                                class="form-control @error('tiktok') is-invalid @enderror"
                                                value="{{ old('tiktok') }}" placeholder="username">
                                        </div>
                                        @error('tiktok')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="website" class="form-label">
                                            <i class="fas fa-globe me-1"></i>Website
                                        </label>
                                        <input type="url" id="website" name="website"
                                            class="form-control @error('website') is-invalid @enderror"
                                            value="{{ old('website') }}" placeholder="https://example.com">
                                        @error('website')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Media & Branding --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-image"></i></span>
                            <h6 class="mb-0">{{ __('messages.owner.outlet.all_outlets.contact_picture') }}</h6>
                        </div>

                        <div class="row g-3">
                            {{-- Background Picture --}}
                            <div class="col-md-6">
                                <label for="image"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_picture_optional') }}</label>
                                <input type="file" name="image" id="image"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_2') }}</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div id="imagePreviewWrapper" class="mt-2 d-none">
                                    <div class="position-relative preview-box">
                                        <img id="imagePreview" src="" alt="Preview"
                                            class="img-thumbnail rounded w-100 h-auto">
                                        <button type="button" id="clearImageBtn"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                            aria-label="Remove Picture">&times;</button>
                                    </div>
                                    <small id="imageInfo" class="text-muted d-block mt-1"></small>
                                </div>
                            </div>

                            {{-- Logo --}}
                            <div class="col-md-6">
                                <label for="logo"
                                    class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_logo_optional') }}</label>
                                <input type="file" name="logo" id="logo"
                                    class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_2') }}</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div id="imagePreviewWrapper2" class="mt-2 d-none">
                                    <div class="position-relative preview-box">
                                        <img id="imagePreview2" src="" alt="Preview"
                                            class="img-thumbnail rounded w-100 h-auto">
                                        <button type="button" id="clearImageBtn2"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                            aria-label="Remove Logo">&times;</button>
                                    </div>
                                    <small id="imageInfo2" class="text-muted d-block mt-1"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Status Outlet --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-toggle-on"></i></span>
                            <h6 class="mb-0">{{ __('messages.owner.outlet.all_outlets.outlet_status') }}</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label
                                    class="form-label d-block">{{ __('messages.owner.outlet.all_outlets.activate_outlet') }}</label>
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-ios @error('is_active') is-invalid @enderror"
                                        type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                        {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <span id="isActiveLabel">{{ old('is_active') ? 'Aktif' : 'Nonaktif' }}</span>
                                    </label>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_3') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label for="qr_mode" class="form-label d-block">Payment Scheme</label>
                                <select id="qr_mode" name="qr_mode"
                                    class="form-select @error('qr_mode') is-invalid @enderror"
                                    style="padding-left: 8px; padding-right: 30px; padding-top: 1px; padding-bottom: 1px; font-size: 14px;">
                                    <option value="disabled"
                                        {{ old('qr_mode', 'disabled') == 'disabled' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="barcode_only" {{ old('qr_mode') == 'barcode_only' ? 'selected' : '' }}>
                                        QR Only</option>
                                    <option value="cashier_only" {{ old('qr_mode') == 'cashier_only' ? 'selected' : '' }}>
                                        Cashier Only</option>
                                    <option value="both" {{ old('qr_mode') == 'both' ? 'selected' : '' }}>All</option>
                                </select>
                                @error('qr_mode')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small
                                    class="text-muted d-block mt-1">{{ __('messages.owner.outlet.all_outlets.muted_text_4') }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Keamanan --}}
                    <div class="form-section">
                        <div class="section-header">
                            <span class="section-icon"><i class="fas fa-lock"></i></span>
                            <h6 class="mb-0">{{ __('messages.owner.outlet.all_outlets.security') }}</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password"
                                    class="form-label required">{{ __('messages.owner.outlet.all_outlets.password') }}</label>
                                <div class="input-group has-validation">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" minlength="8"
                                        required autocomplete="new-password"
                                        placeholder="{{ __('messages.owner.outlet.all_outlets.min_character') }}">
                                    <button class="btn btn-outline-choco" type="button" id="togglePassword"
                                        tabindex="-1">{{ __('messages.owner.outlet.all_outlets.show') }}</button>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small
                                    class="text-muted">{{ __('messages.owner.outlet.all_outlets.muted_text_5') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation"
                                    class="form-label required">{{ __('messages.owner.outlet.all_outlets.password_confirmation') }}</label>
                                <div class="input-group has-validation">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        minlength="8" required autocomplete="new-password"
                                        placeholder="{{ __('messages.owner.outlet.all_outlets.repeat_password') }}">
                                    <button class="btn btn-outline-choco" type="button" id="togglePasswordConfirm"
                                        tabindex="-1">{{ __('messages.owner.outlet.all_outlets.show') }}</button>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sticky Actions --}}
                    <div class="form-actions sticky-actions mt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn btn-outline-choco">
                                <i class="fas fa-xmark me-2"></i>{{ __('messages.owner.outlet.all_outlets.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-choco">
                                <i class="fas fa-save me-2"></i>{{ __('messages.owner.outlet.all_outlets.save') }}
                            </button>
                        </div>
                    </div>
                </form>
                </form>

                <!-- Crop Photo Modal - Background Picture -->
                <div class="modal fade" id="cropBackgroundModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-choco text-white border-0">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-crop mr-2"></i>Crop Background Picture
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-info border-0 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <small>Drag to move, scroll to zoom, or use the corners to resize the crop area.</small>
                                </div>

                                <div class="img-container-crop">
                                    <img id="imageToCropBackground" style="max-width: 100%;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                                <button type="button" id="cropBackgroundBtn" class="btn btn-choco btn-md px-4">
                                    <i class="fas fa-check mr-2"></i>Crop
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crop Photo Modal - Logo -->
                <div class="modal fade" id="cropLogoModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-choco text-white border-0">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-crop mr-2"></i>Crop Logo
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-info border-0 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <small>Drag to move, scroll to zoom, or use the corners to resize the crop area.</small>
                                </div>

                                <div class="img-container-crop">
                                    <img id="imageToCropLogo" style="max-width: 100%;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                                <button type="button" id="cropLogoBtn" class="btn btn-choco btn-md px-4">
                                    <i class="fas fa-check mr-2"></i>Crop
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>



    <style>
        /* ===== Owner › Outlet Create (page scope) ===== */
        .owner-outlet-create {
            --choco: #8c1000;
            --soft-choco: #c12814;
            --ink: #22272b;
            --paper: #f7f7f8;
            --radius: 12px;
            --shadow: 0 6px 20px rgba(0, 0, 0, .08);
            --switch-w: 2.6rem;
            --switch-h: 1.4rem;
            --switch-gap: .65rem;
        }

        .owner-outlet-create .form-check.form-switch {
            padding-left: calc(var(--switch-w) + var(--switch-gap));
        }

        .owner-outlet-create .form-check.form-switch .form-check-input {
            width: var(--switch-w);
            height: var(--switch-h);
            margin-left: calc(-1 * (var(--switch-w) + var(--switch-gap)));
        }

        .owner-outlet-create .form-check.form-switch .form-check-label {
            margin-left: .1rem;
        }

        .owner-outlet-create .card {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .owner-outlet-create .card-header {
            background: #fff;
            border-bottom: 1px solid #eef1f4;
        }

        .owner-outlet-create .card-title {
            color: var(--ink);
            font-weight: 700;
        }

        .owner-outlet-create .text-choco {
            color: var(--choco) !important;
        }

        .owner-outlet-create .btn-choco {
            background: var(--choco);
            border-color: var(--choco);
            color: #fff;
        }

        .owner-outlet-create .btn-choco:hover {
            background: var(--soft-choco);
            border-color: var(--soft-choco);
            color: #fff;
        }

        .owner-outlet-create .btn-outline-choco {
            color: var(--choco);
            border-color: var(--choco);
        }

        .owner-outlet-create .btn-outline-choco:hover {
            color: #fff;
            background: var(--choco);
            border-color: var(--choco);
        }

        .owner-outlet-create .alert {
            border-left: 4px solid var(--choco);
            border-radius: 10px;
        }

        .owner-outlet-create .alert-danger {
            background: #fff5f5;
            border-color: #fde2e2;
            color: #991b1b;
        }

        .owner-outlet-create .alert-success {
            background: #f0fdf4;
            border-color: #dcfce7;
            color: #166534;
        }

        .owner-outlet-create .alert-info {
            background: #eff6ff;
            border-color: #dbeafe;
            color: #1d4ed8;
        }

        .owner-outlet-create .form-label {
            font-weight: 600;
            color: #374151;
        }

        .owner-outlet-create .required::after {
            content: " *";
            color: #dc3545;
        }

        .owner-outlet-create .form-control:focus,
        .owner-outlet-create .form-select:focus {
            border-color: var(--choco);
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
        }

        .owner-outlet-create .input-group-text {
            background: rgba(140, 16, 0, .08);
            color: var(--choco);
            border-color: rgba(140, 16, 0, .25);
        }

        .owner-outlet-create .input-group>.form-control {
            border-right: 0;
        }

        .owner-outlet-create .input-group .btn {
            border-radius: 0 .5rem .5rem 0;
        }

        .owner-outlet-create .form-section {
            padding: 1.15rem 0;
            border-top: 1px solid #eef1f4;
        }

        .owner-outlet-create .form-section:first-of-type {
            border-top: 0;
        }

        .owner-outlet-create .section-header {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: .85rem;
        }

        .owner-outlet-create .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(140, 16, 0, .08);
            color: var(--choco);
        }

        .owner-outlet-create .loading-spinner {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            border: .15rem solid rgba(140, 16, 0, .2);
            border-top-color: var(--choco);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        .owner-outlet-create .d-none {
            display: none !important;
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        .owner-outlet-create .form-check-input:checked {
            background-color: var(--choco);
            border-color: var(--choco);
        }

        .owner-outlet-create .form-check-input:focus {
            box-shadow: 0 0 0 .2rem rgba(140, 16, 0, .15);
        }

        .owner-outlet-create .toggle-ios:focus {
            outline: 0;
        }

        .owner-outlet-create .preview-box {
            width: 200px;
            border-radius: var(--radius);
        }

        .owner-outlet-create #imagePreviewWrapper .img-thumbnail,
        .owner-outlet-create #imagePreviewWrapper2 .img-thumbnail {
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .owner-outlet-create #clearImageBtn,
        .owner-outlet-create #clearImageBtn2 {
            transform: translate(35%, -35%);
            border-radius: 999px;
            width: 28px;
            height: 28px;
            padding: 0;
            line-height: 26px;
        }

        .owner-outlet-create .sticky-actions {
            position: sticky;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #fff 30%);
            padding-top: .75rem;
            margin-top: 1rem;
        }

        .owner-outlet-create .sticky-actions .btn {
            border-radius: 10px;
            min-width: 120px;
        }

        .owner-outlet-create .text-muted {
            color: #6b7280 !important;
        }

        .owner-outlet-create .text-muted {
            color: #6b7280 !important;
        }

        /* ===== Crop Modal Styles ===== */
        /* ===== Crop Modal Styles ===== */
        .owner-outlet-create #cropBackgroundModal .modal-dialog,
        .owner-outlet-create #cropLogoModal .modal-dialog {
            max-width: 650px;
            width: 90%;
            margin: 1.75rem auto;
            /* Tambahkan ini untuk center */
        }

        .owner-outlet-create #cropBackgroundModal .modal-content,
        .owner-outlet-create #cropLogoModal .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .owner-outlet-create #cropBackgroundModal .modal-body,
        .owner-outlet-create #cropLogoModal .modal-body {
            padding: 1.5rem;
            background: #f8f9fa;
        }

        .owner-outlet-create .img-container-crop {
            width: 100%;
            height: 450px;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #dee2e6;
        }

        .owner-outlet-create .img-container-crop img {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        /* Cropper.js Custom Styles */
        .owner-outlet-create .cropper-container {
            background-color: #f8f9fa;
        }

        

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .owner-outlet-create #cropBackgroundModal .modal-dialog,
            .owner-outlet-create #cropLogoModal .modal-dialog {
                margin: 0.5rem auto;
                /* Sesuaikan margin untuk mobile */
                width: 95%;
            }

            .owner-outlet-create .img-container-crop {
                height: 300px;
            }

            .owner-outlet-create #cropBackgroundModal .modal-body,
            .owner-outlet-create #cropLogoModal .modal-body {
                padding: 1rem;
                /* Kurangi padding di mobile */
            }
        }

        @media (max-width: 576px) {

            .owner-outlet-create #cropBackgroundModal .modal-dialog,
            .owner-outlet-create #cropLogoModal .modal-dialog {
                margin: 0.25rem auto;
                /* Margin lebih kecil untuk layar sangat kecil */
                width: 98%;
            }

            .owner-outlet-create .img-container-crop {
                height: 250px;
            }
        }
    </style>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==== Image Preview ====
            const input = document.getElementById('image');
            const wrapper = document.getElementById('imagePreviewWrapper');
            const preview = document.getElementById('imagePreview');
            const info = document.getElementById('imageInfo');
            const clearBtn = document.getElementById('clearImageBtn');

            const input2 = document.getElementById('logo');
            const wrapper2 = document.getElementById('imagePreviewWrapper2');
            const preview2 = document.getElementById('imagePreview2');
            const info2 = document.getElementById('imageInfo2');
            const clearBtn2 = document.getElementById('clearImageBtn2');

            const MAX_SIZE = 2 * 1024 * 1024;
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

            let cropperBackground = null;
            let cropperLogo = null;
            let currentBackgroundFile = null;
            let currentLogoFile = null;

            function bytesToSize(bytes) {
                const units = ['B', 'KB', 'MB', 'GB'];
                let i = 0,
                    size = bytes;
                while (size >= 1024 && i < units.length - 1) {
                    size /= 1024;
                    i++;
                }
                return `${size.toFixed(size < 10 && i > 0 ? 1 : 0)} ${units[i]}`;
            }

            function resetPreview(wrapperEl, previewEl, infoEl) {
                if (!wrapperEl) return;
                previewEl.src = '';
                infoEl.textContent = '';
                wrapperEl.classList.add('d-none');
            }



            // Set up file change handlers with cropping
            // ==== Cropper Handler - Background ====
            if (input) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (!ALLOWED.includes(file.type)) {
                        alert('File type not supported. Use JPG, PNG, atau WEBP.');
                        this.value = '';
                        return;
                    }

                    if (file.size > MAX_SIZE) {
                        alert('File size more than 2 MB.');
                        this.value = '';
                        return;
                    }

                    currentBackgroundFile = file;

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('imageToCropBackground').src = event.target.result;
                        $('#cropBackgroundModal').modal('show');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // ==== Cropper Handler - Logo ====
            if (input2) {
                input2.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (!ALLOWED.includes(file.type)) {
                        alert('File type not supported. Use JPG, PNG, atau WEBP.');
                        this.value = '';
                        return;
                    }

                    if (file.size > MAX_SIZE) {
                        alert('File size more than 2 MB.');
                        this.value = '';
                        return;
                    }

                    currentLogoFile = file;

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('imageToCropLogo').src = event.target.result;
                        $('#cropLogoModal').modal('show');
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Initialize Cropper Background
            $('#cropBackgroundModal').on('shown.bs.modal', function() {
                if (cropperBackground) cropperBackground.destroy();
                const imageElement = document.getElementById('imageToCropBackground');
                setTimeout(function() {
                    cropperBackground = new Cropper(imageElement, {
                        aspectRatio: 16 / 9,
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        guides: true,
                        center: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        responsive: true
                    });
                }, 300);
            });

            $('#cropBackgroundModal').on('hidden.bs.modal', function() {
                if (cropperBackground) {
                    cropperBackground.destroy();
                    cropperBackground = null;
                }
                currentBackgroundFile = null;
            });

            document.getElementById('cropBackgroundBtn')?.addEventListener('click', function() {
                if (!cropperBackground) {
                    alert('Cropper not initialized. Please try again.');
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                const canvas = cropperBackground.getCroppedCanvas({
                    width: 1200,
                    height: 675,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], currentBackgroundFile.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    input.files = dataTransfer.files;

                    const url = URL.createObjectURL(blob);
                    wrapper.classList.remove('d-none');
                    preview.src = url;
                    info.textContent =
                        `${croppedFile.name} • ${(croppedFile.size / 1024).toFixed(1)} KB (Cropped)`;

                    $('#cropBackgroundModal').modal('hide');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, 'image/jpeg', 0.92);
            });

            // Initialize Cropper Logo
            $('#cropLogoModal').on('shown.bs.modal', function() {
                if (cropperLogo) cropperLogo.destroy();
                const imageElement = document.getElementById('imageToCropLogo');
                setTimeout(function() {
                    cropperLogo = new Cropper(imageElement, {
                        aspectRatio: 1,
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        guides: true,
                        center: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        responsive: true
                    });
                }, 300);
            });

            $('#cropLogoModal').on('hidden.bs.modal', function() {
                if (cropperLogo) {
                    cropperLogo.destroy();
                    cropperLogo = null;
                }
                currentLogoFile = null;
            });

            document.getElementById('cropLogoBtn')?.addEventListener('click', function() {
                if (!cropperLogo) {
                    alert('Cropper not initialized. Please try again.');
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                const canvas = cropperLogo.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], currentLogoFile.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    input2.files = dataTransfer.files;

                    const url = URL.createObjectURL(blob);
                    wrapper2.classList.remove('d-none');
                    preview2.src = url;
                    info2.textContent =
                        `${croppedFile.name} • ${(croppedFile.size / 1024).toFixed(1)} KB (Cropped)`;

                    $('#cropLogoModal').modal('hide');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, 'image/jpeg', 0.92);
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    input.value = '';
                    resetPreview(wrapper, preview, info);
                });
            }

            if (clearBtn2) {
                clearBtn2.addEventListener('click', () => {
                    input2.value = '';
                    resetPreview(wrapper2, preview2, info2);
                });
            }

            // ==== Toggle Password ====
            function bindToggle(btnId, inputId) {
                const btn = document.getElementById(btnId);
                const inp = document.getElementById(inputId);
                if (!btn || !inp) return;

                btn.addEventListener('click', () => {
                    const isPassword = inp.type === 'password';
                    inp.type = isPassword ? 'text' : 'password';
                    btn.textContent = isPassword ? 'Hide' : 'Show';
                });
            }

            bindToggle('togglePassword', 'password');
            bindToggle('togglePasswordConfirm', 'password_confirmation');

            // ==== Form Validation ====
            const form = document.getElementById('employeeForm');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (password.value.length > 0 && password.value.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 characters.');
                        password.focus();
                        return;
                    }

                    if (password.value.length > 0 && password.value !== passwordConfirm.value) {
                        e.preventDefault();
                        alert('Password confirmation is not the same.');
                        passwordConfirm.focus();
                    }
                });
            }

            // ==== Region Selector (Indonesia) ====
            const API_BASE = "https://www.emsifa.com/api-wilayah-indonesia/api";
            const provinceSelect = document.getElementById("province");
            const citySelect = document.getElementById("city");
            const districtSelect = document.getElementById("district");
            const villageSelect = document.getElementById("village");

            const provinceNameInput = document.getElementById("province_name");
            const cityNameInput = document.getElementById("city_name");
            const districtNameInput = document.getElementById("district_name");
            const villageNameInput = document.getElementById("village_name");

            const spnProvince = document.getElementById('spnProvince');
            const spnCity = document.getElementById('spnCity');
            const spnDistrict = document.getElementById('spnDistrict');
            const spnVillage = document.getElementById('spnVillage');

            function setLoading(selectEl, spinnerEl, isLoading, placeholder) {
                if (isLoading) {
                    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
                    selectEl.disabled = true;
                    spinnerEl?.classList.remove('d-none');
                } else {
                    spinnerEl?.classList.add('d-none');
                    selectEl.disabled = false;
                }
            }

            function resetSelect(selectEl, message) {
                selectEl.innerHTML = `<option value="">${message}</option>`;
            }

            function fillHiddenName(selectEl, hiddenInput) {
                hiddenInput.value = selectEl.options[selectEl.selectedIndex]?.text || '';
            }

            function loadOptions(url, selectEl, spinnerEl, defaultMsg) {
                setLoading(selectEl, spinnerEl, true, 'Loading...');
                return fetch(url)
                    .then(r => r.json())
                    .then(list => {
                        resetSelect(selectEl, defaultMsg);
                        list.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            selectEl.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        resetSelect(selectEl, 'Failed to load data');
                        alert('Failed to load data');
                    })
                    .finally(() => setLoading(selectEl, spinnerEl, false));
            }

            // Load Provinces
            loadOptions(
                `${API_BASE}/provinces.json`,
                provinceSelect,
                spnProvince,
                '{{ __('messages.owner.outlet.all_outlets.select_province') }}'
            );

            // Province Change Handler
            provinceSelect.addEventListener('change', function() {
                fillHiddenName(provinceSelect, provinceNameInput);
                resetSelect(citySelect, '{{ __('messages.owner.outlet.all_outlets.select_city') }}');
                resetSelect(districtSelect,
                    '{{ __('messages.owner.outlet.all_outlets.select_district') }}');
                resetSelect(villageSelect,
                    '{{ __('messages.owner.outlet.all_outlets.select_village') }}');
                citySelect.disabled = districtSelect.disabled = villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/regencies/${this.value}.json`,
                        citySelect,
                        spnCity,
                        '{{ __('messages.owner.outlet.all_outlets.select_city') }}'
                    );
                }
            });

            // City Change Handler
            citySelect.addEventListener('change', function() {
                fillHiddenName(citySelect, cityNameInput);
                resetSelect(districtSelect,
                    '{{ __('messages.owner.outlet.all_outlets.select_district') }}');
                resetSelect(villageSelect,
                    '{{ __('messages.owner.outlet.all_outlets.select_village') }}');
                districtSelect.disabled = villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/districts/${this.value}.json`,
                        districtSelect,
                        spnDistrict,
                        '{{ __('messages.owner.outlet.all_outlets.select_district') }}'
                    );
                }
            });

            // District Change Handler
            districtSelect.addEventListener('change', function() {
                fillHiddenName(districtSelect, districtNameInput);
                resetSelect(villageSelect,
                    '{{ __('messages.owner.outlet.all_outlets.select_village') }}');
                villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/villages/${this.value}.json`,
                        villageSelect,
                        spnVillage,
                        '{{ __('messages.owner.outlet.all_outlets.select_village') }}'
                    );
                }
            });

            // Village Change Handler
            villageSelect.addEventListener('change', function() {
                fillHiddenName(villageSelect, villageNameInput);
            });

            // ==== Username Checker ====
            const inputUsername = document.getElementById('username');
            const btnCheck = document.getElementById('btnCheckUsername');
            const statusEl = document.getElementById('usernameStatus');
            const urlCheckEl = document.getElementById('usernameCheckUrl');
            const urlCheck = urlCheckEl?.value || '';

            if (inputUsername && btnCheck && statusEl && urlCheck) {
                const spinner = btnCheck.querySelector('.spinner-border');
                const btnLabel = btnCheck.querySelector('.label');
                let debounceTimer;

                function setUsernameLoading(isLoading) {
                    btnCheck.disabled = isLoading;
                    spinner.classList.toggle('d-none', !isLoading);
                    btnLabel.textContent = isLoading ? 'Checking...' : 'Check';
                }

                function showStatus(isAvailable, message) {
                    statusEl.className = 'form-text mt-1';
                    if (isAvailable) {
                        statusEl.innerHTML =
                            `<span class="badge bg-success">{{ __('messages.owner.outlet.all_outlets.available') }}</span> <span class="text-success ms-1">${message}</span>`;
                        inputUsername.classList.remove('is-invalid');
                        inputUsername.classList.add('is-valid');
                    } else {
                        statusEl.innerHTML =
                            `<span class="badge bg-danger">{{ __('messages.owner.outlet.all_outlets.taken') }}</span> <span class="text-danger ms-1">${message}</span>`;
                        inputUsername.classList.remove('is-valid');
                        inputUsername.classList.add('is-invalid');
                    }
                }

                function showNeutral(message = '') {
                    statusEl.className = 'form-text mt-1 text-muted';
                    statusEl.textContent = message;
                    inputUsername.classList.remove('is-valid', 'is-invalid');
                }

                async function checkUsername() {
                    const username = inputUsername.value.trim();

                    if (!username) {
                        showNeutral();
                        return;
                    }

                    if (username.length < 3 || username.length > 30 || !/^[A-Za-z0-9._\-]+$/.test(username)) {
                        showStatus(false, 'Format not valid.');
                        return;
                    }

                    try {
                        setUsernameLoading(true);
                        const params = new URLSearchParams({
                            username
                        });

                        const response = await fetch(`${urlCheck}?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.status === 422) {
                            showStatus(false, 'Format not valid.');
                            return;
                        }

                        const data = await response.json();
                        if (typeof data.available !== 'undefined') {
                            showStatus(
                                data.available,
                                data.available ?
                                '{{ __('messages.owner.outlet.all_outlets.username_available') }} 🎉' :
                                '{{ __('messages.owner.outlet.all_outlets.username_used') }}'
                            );
                        } else {
                            showNeutral("Can't check at this time");
                        }
                    } catch (error) {
                        showNeutral('A network error occurred.');
                    } finally {
                        setUsernameLoading(false);
                    }
                }

                btnCheck.addEventListener('click', checkUsername);

                inputUsername.addEventListener('input', () => {
                    showNeutral();
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(checkUsername, 500);
                });
            }

            // ==== Slug Checker ====
            const inputSlug = document.getElementById('slug');
            const btnCheckSlug = document.getElementById('btnCheckSlug');
            const statusSlug = document.getElementById('slugStatus');
            const urlCheckSlugEl = document.getElementById('slugCheckUrl');
            const urlCheckSlug = urlCheckSlugEl?.value || '';

            if (inputSlug && btnCheckSlug && statusSlug && urlCheckSlug) {
                const spinnerSlug = btnCheckSlug.querySelector('.spinner-border');
                const btnLabelSlug = btnCheckSlug.querySelector('.label');
                let debounceTimerSlug;

                function setSlugLoading(isLoading) {
                    btnCheckSlug.disabled = isLoading;
                    spinnerSlug.classList.toggle('d-none', !isLoading);
                    btnLabelSlug.textContent = isLoading ? 'Checking...' : 'Check';
                }

                function showSlugStatus(isAvailable, message) {
                    statusSlug.className = 'form-text mt-1';
                    if (isAvailable) {
                        statusSlug.innerHTML =
                            `<span class="badge bg-success">{{ __('messages.owner.outlet.all_outlets.available') }}</span> <span class="text-success ms-1">${message}</span>`;
                        inputSlug.classList.remove('is-invalid');
                        inputSlug.classList.add('is-valid');
                    } else {
                        statusSlug.innerHTML =
                            `<span class="badge bg-danger">{{ __('messages.owner.outlet.all_outlets.taken') }}</span> <span class="text-danger ms-1">${message}</span>`;
                        inputSlug.classList.remove('is-valid');
                        inputSlug.classList.add('is-invalid');
                    }
                }

                function showSlugNeutral(message = '') {
                    statusSlug.className = 'form-text mt-1 text-muted';
                    statusSlug.textContent = message;
                    inputSlug.classList.remove('is-valid', 'is-invalid');
                }

                // Normalisasi slug
                function normalizeSlug(s) {
                    return s
                        .toLowerCase()
                        .trim()
                        .replace(/[\s]+/g, '-')
                        .replace(/[^a-z0-9._-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^[-._]+|[-._]+$/g, '');
                }

                async function checkSlug() {
                    const raw = inputSlug.value.trim();
                    const val = normalizeSlug(raw);

                    // Auto-update input dengan nilai yang dinormalisasi
                    if (raw !== val) {
                        inputSlug.value = val;
                    }

                    if (!val) {
                        showSlugNeutral();
                        return;
                    }

                    if (val.length < 3 || val.length > 30 || !/^[a-z0-9._\-]+$/.test(val)) {
                        showSlugStatus(false, 'Format not valid.');
                        return;
                    }

                    try {
                        setSlugLoading(true);
                        const params = new URLSearchParams({
                            slug: val
                        });

                        const response = await fetch(`${urlCheckSlug}?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.status === 422) {
                            showSlugStatus(false, 'Format not valid.');
                            return;
                        }

                        const data = await response.json();
                        if (typeof data.available !== 'undefined') {
                            showSlugStatus(
                                data.available,
                                data.available ?
                                '{{ __('messages.owner.outlet.all_outlets.slug_available') }} 🎉' :
                                '{{ __('messages.owner.outlet.all_outlets.slug_used') }}'
                            );
                        } else {
                            showSlugNeutral("Can't check at this time");
                        }
                    } catch (error) {
                        showSlugNeutral('A network error occurred.');
                    } finally {
                        setSlugLoading(false);
                    }
                }

                btnCheckSlug.addEventListener('click', checkSlug);

                inputSlug.addEventListener('input', () => {
                    showSlugNeutral();
                    clearTimeout(debounceTimerSlug);
                    debounceTimerSlug = setTimeout(checkSlug, 500);
                });

                // Auto-generate slug dari name field
                const nameInput = document.getElementById('name');
                if (nameInput) {
                    nameInput.addEventListener('blur', () => {
                        if (!inputSlug.value.trim() && nameInput.value.trim()) {
                            inputSlug.value = normalizeSlug(nameInput.value);
                            showSlugNeutral('');
                            clearTimeout(debounceTimerSlug);
                            debounceTimerSlug = setTimeout(checkSlug, 300);
                        }
                    });
                }
            }

            // ==== Toggle Status Labels ====
            const activeToggle = document.getElementById('is_active');
            const activeLabel = document.getElementById('isActiveLabel');

            if (activeToggle && activeLabel) {
                activeToggle.addEventListener('change', () => {
                    activeLabel.textContent = activeToggle.checked ?
                        '{{ __('messages.owner.outlet.all_outlets.active') }}' :
                        '{{ __('messages.owner.outlet.all_outlets.inactive') }}';
                });
            }
        });
    </script>
@endpush
