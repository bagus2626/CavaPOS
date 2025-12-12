@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.create_outlet'))
@section('page_title', __('messages.owner.outlet.all_outlets.create_new_outlet'))

@section('content')
    <div class="modern-outlet-editor">
        {{-- Header --}}
        <div class="editor-header">
            <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        {{-- Main Content --}}
        <div class="editor-content">
            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>
                        <strong>{{ __('messages.owner.outlet.all_outlets.re_check_input') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-info">
                    <i class="fas fa-circle-info"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('owner.user-owner.outlets.store') }}" method="POST" enctype="multipart/form-data"
                id="outletForm">
                @csrf

                {{-- Hidden Inputs --}}
                <input type="hidden" id="usernameCheckUrl" value="{{ route('owner.user-owner.outlets.check-username') }}">
                <input type="hidden" id="slugCheckUrl" value="{{ route('owner.user-owner.outlets.check-slug') }}">

                {{-- Form Sections Container --}}
                <div class="sections-container">

                    {{-- SECTION 1: Basic Info --}}
                    <div class="section-item" open>
                        <div class="section-header">
                            <div class="section-header-content">
                                <i class="fas fa-info-circle"></i>
                                <span
                                    class="section-title">{{ __('messages.owner.outlet.all_outlets.base_information') }}</span>
                            </div>
                            <i class="fas fa-chevron-down section-arrow"></i>
                        </div>
                        <div class="section-body">
                            <div class="form-grid form-grid-2x2">
                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</span>
                                    <input type="text" name="name" id="name"
                                        class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required maxlength="255" placeholder="ex: Cava Coffee - Malioboro">
                                    @error('name')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                    <small class="char-counter" id="name-counter">0/255</small>
                                </label>

                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.username') }}</span>
                                    <div class="input-with-button">
                                        <div class="input-prefix">@</div>
                                        <input type="text" name="username" id="username"
                                            class="form-input @error('username') is-invalid @enderror"
                                            value="{{ old('username') }}" required minlength="3" maxlength="30"
                                            pattern="^[A-Za-z0-9._\-]+$" placeholder="budi.setiawan">
                                        <button type="button" id="btnCheckUsername" class="input-button">
                                            <span class="label">Check</span>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                    </div>
                                    <small
                                        class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_1') }}</small>
                                    <div id="usernameStatus" class="status-message"></div>
                                    @error('username')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="form-group">
                                    <span class="form-label required">Slug</span>
                                    <div class="input-with-button">
                                        <input type="text" name="slug" id="slug"
                                            class="form-input @error('slug') is-invalid @enderror"
                                            value="{{ old('slug') }}" required minlength="3" maxlength="30"
                                            pattern="^[A-Za-z0-9._\-]+$" placeholder="cava-coffee-malioboro">
                                        <button type="button" id="btnCheckSlug" class="input-button">
                                            <span class="label">Check</span>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                    </div>
                                    <small
                                        class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_1') }}</small>
                                    <div id="slugStatus" class="status-message"></div>
                                    @error('slug')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.email_outlet') }}</span>
                                    <input type="email" name="email" id="email"
                                        class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                        required placeholder="name@company.com">
                                    @error('email')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Address --}}
                    <div class="section-item" open>
                        <div class="section-header">
                            <div class="section-header-content">
                                <i class="fas fa-location-dot"></i>
                                <span
                                    class="section-title">{{ __('messages.owner.outlet.all_outlets.outlet_address') }}</span>
                            </div>
                            <i class="fas fa-chevron-down section-arrow"></i>
                        </div>
                        <div class="section-body">
                            <div class="form-grid form-grid-2x2">
                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.province') }}</span>
                                    <div class="select-wrapper">
                                        <select id="province" name="province" class="form-input" disabled>
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.load_province') }}</option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnProvince"></span>
                                    </div>
                                    <input type="hidden" id="province_name" name="province_name"
                                        value="{{ old('province_name') }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.city') }}</span>
                                    <div class="select-wrapper">
                                        <select id="city" name="city" class="form-input" disabled>
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_province_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnCity"></span>
                                    </div>
                                    <input type="hidden" id="city_name" name="city_name"
                                        value="{{ old('city_name') }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.district') }}</span>
                                    <div class="select-wrapper">
                                        <select id="district" name="district" class="form-input" disabled>
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_city_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnDistrict"></span>
                                    </div>
                                    <input type="hidden" id="district_name" name="district_name"
                                        value="{{ old('district_name') }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.village') }}</span>
                                    <div class="select-wrapper">
                                        <select id="village" name="village" class="form-input" disabled>
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_district_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnVillage"></span>
                                    </div>
                                    <input type="hidden" id="village_name" name="village_name"
                                        value="{{ old('village_name') }}">
                                </label>

                                <label class="form-group form-group-full">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.detail_address') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-road input-icon"></i>
                                        <input type="text" id="address" name="address" class="form-input"
                                            value="{{ old('address') }}" maxlength="500"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.detail_address_placeholder') }}">
                                    </div>
                                    <small class="char-counter" id="address-counter">0/500</small>
                                </label>

                                <label class="form-group form-group-full">
                                    <span class="form-label">Google Maps Embed URL</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-marked-alt input-icon"></i>
                                        <input type="url" id="gmaps_url" name="gmaps_url" class="form-input"
                                            value="{{ old('gmaps_url') }}"
                                            placeholder="https://www.google.com/maps/embed?pb=...">
                                    </div>
                                    <small class="form-hint">
                                        <i class="fas fa-info-circle"></i> Copy embed URL dari Google Maps
                                    </small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 4: Media/Branding --}}
                    <div class="section-item" open>
                        <div class="section-header">
                            <div class="section-header-content">
                                <i class="fas fa-image"></i>
                                <span
                                    class="section-title">{{ __('messages.owner.outlet.all_outlets.contact_picture') }}</span>
                            </div>
                            <i class="fas fa-chevron-down section-arrow"></i>
                        </div>
                        <div class="section-body">
                            <div class="form-grid form-grid-2">
                                {{-- Background Picture --}}
                                <div class="form-group">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_picture_optional') }}</span>
                                    <div class="media-upload-group">
                                        <div class="media-preview d-none" id="imagePreviewWrapper">
                                            <img id="imagePreview" src="" alt="Background Preview"
                                                class="media-image media-image-cover">
                                            <button type="button" id="clearImageBtn"
                                                class="media-remove-btn">&times;</button>
                                        </div>
                                        <button type="button" class="btn-upload"
                                            onclick="document.getElementById('image').click()">
                                            {{ __('Change Photo') }}
                                        </button>
                                        <input type="file" name="image" id="image" class="d-none"
                                            accept="image/*">
                                        <small
                                            class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_2') }}</small>
                                    </div>
                                </div>

                                {{-- Logo --}}
                                <div class="form-group">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_logo_optional') }}</span>
                                    <div class="media-upload-group">
                                        <div class="media-preview media-preview-logo d-none" id="imagePreviewWrapper2">
                                            <img id="imagePreview2" src="" alt="Logo Preview"
                                                class="media-image media-image-logo">
                                            <button type="button" id="clearImageBtn2"
                                                class="media-remove-btn">&times;</button>
                                        </div>
                                        <button type="button" class="btn-upload"
                                            onclick="document.getElementById('logo').click()">
                                            {{ __('Change Logo') }}
                                        </button>
                                        <input type="file" name="logo" id="logo" class="d-none"
                                            accept="image/*">
                                        <small
                                            class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_2') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Contact --}}
                    <div class="section-item" open>
                        <div class="section-header">
                            <div class="section-header-content">
                                <i class="fas fa-phone"></i>
                                <span class="section-title">Kontak & Sosial Media</span>
                            </div>
                            <i class="fas fa-chevron-down section-arrow"></i>
                        </div>
                        <div class="section-body">
                            <div class="form-grid form-grid-2">
                                <label class="form-group">
                                    <span class="form-label">{{ __('Contact Name') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" id="contact_person" name="contact_person"
                                            class="form-input" value="{{ old('contact_person') }}" placeholder="Name">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('Phone Number') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" id="contact_phone" name="contact_phone" class="form-input"
                                            value="{{ old('contact_phone') }}" placeholder="08123456789">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Instagram</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="instagram" class="form-input"
                                            value="{{ old('instagram') }}" placeholder="username">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Twitter / X</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="twitter" class="form-input"
                                            value="{{ old('twitter') }}" placeholder="username">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">WhatsApp</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">+62</span>
                                        <input type="tel" name="whatsapp" class="form-input"
                                            value="{{ old('whatsapp') }}" placeholder="8123456789">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Facebook</span>
                                    <input type="text" name="facebook" class="form-input"
                                        value="{{ old('facebook') }}" placeholder="facebook.com/yourpage">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">TikTok</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="tiktok" class="form-input"
                                            value="{{ old('tiktok') }}" placeholder="username">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Website</span>
                                    <input type="url" name="website" class="form-input"
                                        value="{{ old('website') }}" placeholder="https://example.com">
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 5: Status --}}
                    <div class="section-item" open>
                        <div class="section-header">
                            <div class="section-header-content">
                                <i class="fas fa-toggle-on"></i>
                                <span
                                    class="section-title">{{ __('messages.owner.outlet.all_outlets.outlet_status') }}</span>
                            </div>
                            <i class="fas fa-chevron-down section-arrow"></i>
                        </div>
                        <div class="section-body">
                            {{-- Outlet Status --}}
                            <div class="toggle-group">
                                <div>
                                    <p class="toggle-label">
                                        {{ __('messages.owner.outlet.all_outlets.activate_outlet') }}</p>
                                    <p class="toggle-description">
                                        {{ __('messages.owner.outlet.all_outlets.muted_text_3') }}</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        {{ old('is_active') ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            {{-- QR Mode --}}
                            <div class="form-group mt-4">
                                <span class="form-label">{{ __('messages.owner.outlet.all_outlets.activate_qr') }}</span>
                                <select id="qr_mode" name="qr_mode" class="form-input">
                                    <option value="disabled"
                                        {{ old('qr_mode', 'disabled') == 'disabled' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.inactive') }}
                                    </option>
                                    <option value="barcode_only" {{ old('qr_mode') == 'barcode_only' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.qr_only') }}
                                    </option>
                                    <option value="cashier_only" {{ old('qr_mode') == 'cashier_only' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.cashier_only') }}
                                    </option>
                                    <option value="both" {{ old('qr_mode') == 'both' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.all_methods') }}
                                    </option>
                                </select>
                                <small
                                    class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_4') }}</small>
                            </div>

                            {{-- Password --}}
                            <div class="form-grid form-grid-2 mt-4">
                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.password') }}</span>
                                    <div class="input-with-button">
                                        <input type="password" name="password" id="password" class="form-input"
                                            minlength="8" required
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.min_character') }}">
                                        <button type="button" id="togglePassword" class="input-button">
                                            {{ __('messages.owner.outlet.all_outlets.show') }}
                                        </button>
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.password_confirmation') }}</span>
                                    <div class="input-with-button">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-input" minlength="8" required
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.repeat_password') }}">
                                        <button type="button" id="togglePasswordConfirm" class="input-button">
                                            {{ __('messages.owner.outlet.all_outlets.show') }}
                                        </button>
                                    </div>
                                </label>
                            </div>

                            {{-- WiFi --}}
                            <div class="toggle-group mt-4">
                                <div>
                                    <p class="toggle-label">
                                        {{ __('messages.owner.outlet.all_outlets.wifi_information') }}</p>
                                    <p class="toggle-description">
                                        {{ __('messages.owner.outlet.all_outlets.wifi_description') }}</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="hidden" name="is_wifi_shown" value="0">
                                    <input type="checkbox" id="is_wifi_shown" name="is_wifi_shown" value="1"
                                        {{ old('is_wifi_shown') ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div id="wifiFormFields" class="form-grid form-grid-2 mt-4"
                                style="display: {{ old('is_wifi_shown') ? 'grid' : 'none' }};">
                                <label class="form-group">
                                    <span class="form-label">{{ __('WiFi Name (SSID)') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-wifi input-icon"></i>
                                        <input type="text" id="user_wifi" name="user_wifi" class="form-input"
                                            value="{{ old('user_wifi') }}" placeholder="WiFi Name">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('WiFi Password') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-key input-icon"></i>
                                        <input type="password" id="pass_wifi" name="pass_wifi" class="form-input"
                                            value="{{ old('pass_wifi') }}" placeholder="WiFi Password">
                                        <button type="button" id="toggleWifiPassword"
                                            class="input-button-inline">{{ __('Show') }}</button>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- End sections-container --}}

                {{-- Action Buttons --}}
                <div class="form-actions">
                    <button type="button" id="cancelBtn" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" id="saveBtn" class="btn-save">
                        <i class="fas fa-save"></i>
                        <span class="btn-label">{{ __('Save') }}</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Crop Modals --}}
    <div class="modal fade" id="cropBackgroundModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-choco text-white border-0" style="background: #8a1000 !important;">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-crop mr-2"></i>Crop Background Picture
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-3"
                        style="background: #eff6ff; border: 1px solid #dbeafe !important; color: #1d4ed8;">
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
                    <button type="button" id="cropBackgroundBtn" class="btn btn-md px-4"
                        style="background: #8a1000; color: white; border: none;">
                        <i class="fas fa-check mr-2"></i>Crop
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cropLogoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-choco text-white border-0" style="background: #8a1000 !important;">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-crop mr-2"></i>Crop Logo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-3"
                        style="background: #eff6ff; border: 1px solid #dbeafe !important; color: #1d4ed8;">
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
                    <button type="button" id="cropLogoBtn" class="btn btn-md px-4"
                        style="background: #8a1000; color: white; border: none;">
                        <i class="fas fa-check mr-2"></i>Crop
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ===== Modern Outlet Editor ===== */
        .modern-outlet-editor {
            --primary: #8a1000;
            --primary-dark: #6b0c00;
            --bg-light: #f8f6f5;
            --text-dark: #1d0e0c;
            --border-color: rgba(138, 16, 0, 0.2);
            --radius: 0.75rem;
            --radius-lg: 1.5rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--bg-light);
        }

        .editor-toolbar {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }

        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            background: transparent;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back:hover {
            background: rgba(138, 16, 0, 0.1);
            color: var(--primary);
            transform: scale(1.05);
        }

        .editor-content {
            flex: 1;
            padding: 2rem 1.5rem 100px;
            overflow-y: auto;
        }

        .editor-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-heading {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* ===== Alerts ===== */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border: none;
        }

        .alert i {
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }

        .alert-danger {
            background: #fee;
            color: #c00;
        }

        .alert-success {
            background: #efe;
            color: #0a0;
        }

        .alert-info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .alert ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.25rem;
        }

        .alert li {
            margin: 0.25rem 0;
        }

        /* ===== Sections Container ===== */
        .sections-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .section-item {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .section-item:hover {
            box-shadow: 0 4px 12px rgba(138, 16, 0, 0.08);
        }

        .section-item[open] {
            border-color: var(--primary);
        }

        .section-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, rgba(138, 16, 0, 0.03) 0%, rgba(138, 16, 0, 0.01) 100%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            transition: all 0.3s ease;
            list-style: none;
        }

        .section-header::-webkit-details-marker {
            display: none;
        }

        .section-header:hover {
            background: linear-gradient(135deg, rgba(138, 16, 0, 0.06) 0%, rgba(138, 16, 0, 0.02) 100%);
        }

        .section-header-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-header-content i {
            color: var(--primary);
            font-size: 1.25rem;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .section-arrow {
            color: var(--primary);
            transition: transform 0.3s ease;
            font-size: 0.875rem;
        }

        .section-item[open] .section-arrow {
            transform: rotate(180deg);
        }

        .section-body {
            padding: 1.5rem;
            border-top: 1px solid rgba(138, 16, 0, 0.1);
        }

        /* ===== Form Grid ===== */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .form-grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        /* ===== Form Grid 2x2 (Fixed 2 columns) ===== */
        .form-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        /* Responsive untuk mobile tetap 1 kolom */
        @media (max-width: 768px) {
            .form-grid-2x2 {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label.required::after {
            content: '*';
            color: #dc2626;
            margin-left: 0.25rem;
        }

        .form-input {
            padding: 0.75rem 1rem;
            border: 1.5px solid rgba(138, 16, 0, 0.2);
            border-radius: var(--radius);
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(138, 16, 0, 0.1);
        }

        .form-input:disabled {
            background: rgba(138, 16, 0, 0.05);
            cursor: not-allowed;
            opacity: 0.7;
        }

        .form-input.is-invalid {
            border-color: #dc2626;
        }

        .form-error {
            color: #dc2626;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .form-hint {
            color: #6b7280;
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .char-counter {
            display: block;
            text-align: right;
            color: #6c757d;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        /* ===== Input Variations ===== */
        /* ===== Input Variations ===== */
        .input-with-button {
            display: flex;
            position: relative;
        }

        .input-prefix {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-weight: 500;
            pointer-events: none;
            z-index: 1;
            font-size: 0.9375rem;
        }

        /* DEFAULT: Input dengan button TANPA prefix = padding normal */
        .input-with-button .form-input {
            flex: 1;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            padding-left: 1rem;
            /* TAMBAHKAN INI - padding normal seperti input biasa */
        }

        /* KHUSUS: Hanya yang punya prefix dapat padding ekstra */
        .input-with-button:has(.input-prefix) .form-input {
            padding-left: 2.25rem;
        }

        .input-button {
            padding: 0 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-top-right-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .input-button:hover {
            background: var(--primary-dark);
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            color: #9ca3af;
            pointer-events: none;
            z-index: 1;
            font-size: 0.9375rem;
        }

        .input-with-icon .form-input {
            padding-left: 2.5rem;
        }

        .input-with-prefix {
            position: relative;
        }

        .input-with-prefix .input-prefix {
            left: 0.875rem;
        }

        .input-with-prefix .form-input {
            padding-left: 2.75rem;
        }

        .input-button-inline {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.375rem 0.875rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-button-inline:hover {
            background: var(--primary-dark);
        }

        .input-with-icon:has(.input-button-inline) .form-input {
            padding-right: 5.5rem;
        }

        .select-wrapper {
            position: relative;
        }

        .loading-spinner {
            position: absolute;
            right: 2.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--primary);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* ===== Status Messages ===== */
        .status-message {
            margin-top: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 500;
            display: none;
        }

        .status-message.show {
            display: block;
        }

        .status-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-message.error {
            background: #ffe5e5;
            color: #cc0000;
            border: 1px solid #ff9999;
        }

        /* ===== Toggle Switch ===== */
        .toggle-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            background: rgba(138, 16, 0, 0.02);
            border-radius: var(--radius);
            border: 1px solid rgba(138, 16, 0, 0.1);
        }

        .toggle-label {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 0.25rem 0;
        }

        .toggle-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: 0.3s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        .toggle-switch input:checked+.toggle-slider {
            background-color: var(--primary);
        }

        .toggle-switch input:checked+.toggle-slider:before {
            transform: translateX(24px);
        }

        /* ===== Media Upload ===== */
        .media-upload-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .media-preview {
            position: relative;
            width: 100%;
            max-width: 400px;
            border-radius: var(--radius);
            overflow: hidden;
            border: 2px solid rgba(138, 16, 0, 0.2);
        }

        .media-preview-logo {
            max-width: 200px;
            aspect-ratio: 1;
        }

        .media-image {
            width: 100%;
            display: block;
        }

        .media-image-cover {
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .media-image-logo {
            aspect-ratio: 1;
            object-fit: contain;
            background: #f9fafb;
            padding: 1rem;
        }

        .media-remove-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .media-remove-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .btn-upload {
            padding: 0.75rem 1.5rem;
            background: white;
            border: 2px dashed rgba(138, 16, 0, 0.3);
            border-radius: var(--radius);
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-upload:hover {
            border-color: var(--primary);
            background: rgba(138, 16, 0, 0.05);
        }

        /* ===== Form Actions (Small buttons, after content) ===== */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(138, 16, 0, 0.1);
        }

        .btn-cancel,
        .btn-save {
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            height: 40px;
            min-width: 120px;
            border: none;
        }

        .btn-cancel {
            background: #f9fafb;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .btn-cancel:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #374151;
        }

        .btn-save {
            background: var(--primary);
            color: white;
            position: relative;
        }

        .btn-save:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(138, 16, 0, 0.15);
        }

        .btn-save:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(138, 16, 0, 0.1);
        }

        .btn-save:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        .btn-save .spinner-border {
            width: 0.875rem;
            height: 0.875rem;
            border-width: 2px;
            border-color: currentColor transparent currentColor transparent;
            margin-left: 0.5rem;
        }

        /* Loading state */
        .btn-save.loading .btn-label {
            opacity: 0.8;
        }

        .btn-save.loading .spinner-border {
            display: inline-block !important;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .editor-content {
                padding: 1rem 1rem 100px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .form-grid,
            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .section-header {
                padding: 1rem;
            }

            .section-body {
                padding: 1rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 0.75rem;
                margin-top: 1.5rem;
                padding-top: 1rem;
            }

            .btn-cancel,
            .btn-save {
                width: 100%;
                min-width: unset;
                height: 42px;
                font-size: 0.9375rem;
            }
        }

        /* ===== Cropper Modal Adjustments ===== */
        .img-container-crop {
            max-height: 400px;
            overflow: hidden;
        }

        .modal-content {
            border-radius: var(--radius-lg);
        }

        .editor-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        .page-title {
            margin: 0;
        }
    </style>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==== Cropper variables ====
            let cropperBackground = null;
            let cropperLogo = null;
            let currentBackgroundFile = null;
            let currentLogoFile = null;
            const MAX_SIZE = 2 * 1024 * 1024;
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

            // ==== Character counters ====
            function updateCharCounter(input, counterId) {
                const counter = document.getElementById(counterId);
                if (counter && input) {
                    const update = () => {
                        const length = input.value.length;
                        const max = input.getAttribute('maxlength');
                        counter.textContent = `${length}/${max}`;
                    };
                    input.addEventListener('input', update);
                    update();
                }
            }

            updateCharCounter(document.getElementById('name'), 'name-counter');
            updateCharCounter(document.getElementById('address'), 'address-counter');

            // ==== WiFi toggle ====
            const wifiToggle = document.getElementById('is_wifi_shown');
            const wifiFields = document.getElementById('wifiFormFields');
            if (wifiToggle && wifiFields) {
                wifiToggle.addEventListener('change', function() {
                    wifiFields.style.display = this.checked ? 'grid' : 'none';
                });
                // Set initial state
                wifiFields.style.display = wifiToggle.checked ? 'grid' : 'none';
            }

            // ==== Password toggles ====
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                if (toggle && input) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        const type = input.type === 'password' ? 'text' : 'password';
                        input.type = type;
                        this.textContent = type === 'password' ? 'Show' : 'Hide';
                    });
                }
            }

            setupPasswordToggle('togglePassword', 'password');
            setupPasswordToggle('togglePasswordConfirm', 'password_confirmation');
            setupPasswordToggle('toggleWifiPassword', 'wifi_password');

            // ==== Username check ====
            const btnCheck = document.getElementById('btnCheckUsername');
            const usernameInput = document.getElementById('username');
            const statusDiv = document.getElementById('usernameStatus');
            const checkUrl = document.getElementById('usernameCheckUrl')?.value;

            if (btnCheck && usernameInput && checkUrl) {
                async function checkUsername() {
                    const username = usernameInput.value.trim();

                    if (!username) {
                        showStatus('error', 'Username tidak boleh kosong');
                        return;
                    }

                    if (username.length < 3 || username.length > 30) {
                        showStatus('error', 'Username harus 3-30 karakter');
                        return;
                    }

                    if (!/^[A-Za-z0-9._\-]+$/.test(username)) {
                        showStatus('error', 'Format username tidak valid');
                        return;
                    }

                    btnCheck.disabled = true;
                    const label = btnCheck.querySelector('.label');
                    const spinner = btnCheck.querySelector('.spinner-border');
                    if (label) label.style.display = 'none';
                    if (spinner) spinner.classList.remove('d-none');

                    try {
                        const response = await fetch(`${checkUrl}?username=${encodeURIComponent(username)}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.content ||
                                    document.querySelector('[name="_token"]')?.value || ''
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.available) {
                            showStatus('success', 'Username tersedia');
                        } else {
                            showStatus('error', 'Username sudah digunakan');
                        }
                    } catch (error) {
                        showStatus('error', 'Terjadi kesalahan saat memeriksa username');
                    } finally {
                        btnCheck.disabled = false;
                        if (label) label.style.display = 'inline';
                        if (spinner) spinner.classList.add('d-none');
                    }
                }

                function showStatus(type, message) {
                    if (statusDiv) {
                        statusDiv.className = `status-message show ${type}`;
                        statusDiv.textContent = message;
                    }
                }

                btnCheck.addEventListener('click', checkUsername);

                let debounceTimer;
                usernameInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    if (statusDiv) statusDiv.className = 'status-message';
                    debounceTimer = setTimeout(() => {
                        if (usernameInput.value.trim().length >= 3) {
                            checkUsername();
                        }
                    }, 800);
                });
            }

            // ==== Slug check ====
            const btnCheckSlug = document.getElementById('btnCheckSlug');
            const slugInput = document.getElementById('slug');
            const slugStatusDiv = document.getElementById('slugStatus');
            const slugCheckUrl = document.getElementById('slugCheckUrl')?.value;

            if (btnCheckSlug && slugInput && slugCheckUrl) {
                function normalizeSlug(s) {
                    return s.toLowerCase().trim()
                        .replace(/[\s]+/g, '-')
                        .replace(/[^a-z0-9._-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^[-._]+|[-._]+$/g, '');
                }

                async function checkSlug() {
                    const raw = slugInput.value.trim();
                    const val = normalizeSlug(raw);

                    if (raw !== val) {
                        slugInput.value = val;
                    }

                    if (!val) {
                        showSlugStatus('neutral', '');
                        return;
                    }

                    if (val.length < 3 || val.length > 30) {
                        showSlugStatus('error', 'Slug harus 3-30 karakter');
                        return;
                    }

                    btnCheckSlug.disabled = true;
                    const label = btnCheckSlug.querySelector('.label');
                    const spinner = btnCheckSlug.querySelector('.spinner-border');
                    if (label) label.style.display = 'none';
                    if (spinner) spinner.classList.remove('d-none');

                    try {
                        const response = await fetch(`${slugCheckUrl}?slug=${encodeURIComponent(val)}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.content || ''
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.available) {
                            showSlugStatus('success', 'Slug tersedia');
                        } else {
                            showSlugStatus('error', 'Slug sudah digunakan');
                        }
                    } catch (error) {
                        showSlugStatus('error', 'Terjadi kesalahan saat memeriksa slug');
                    } finally {
                        btnCheckSlug.disabled = false;
                        if (label) label.style.display = 'inline';
                        if (spinner) spinner.classList.add('d-none');
                    }
                }

                function showSlugStatus(type, message) {
                    if (slugStatusDiv) {
                        if (type === 'neutral') {
                            slugStatusDiv.className = 'status-message';
                            slugStatusDiv.textContent = '';
                        } else {
                            slugStatusDiv.className = `status-message show ${type}`;
                            slugStatusDiv.textContent = message;
                        }
                    }
                }

                btnCheckSlug.addEventListener('click', checkSlug);

                let debounceTimer;
                slugInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    showSlugStatus('neutral', '');
                    debounceTimer = setTimeout(() => {
                        if (slugInput.value.trim().length >= 3) {
                            checkSlug();
                        }
                    }, 800);
                });

                // Auto-generate dari name
                const nameInput = document.getElementById('name');
                if (nameInput) {
                    nameInput.addEventListener('blur', () => {
                        if (!slugInput.value.trim() && nameInput.value.trim()) {
                            slugInput.value = normalizeSlug(nameInput.value);
                            setTimeout(checkSlug, 300);
                        }
                    });
                }
            }

            // ==== Background Image Upload Handler ====
            const inputBackground = document.getElementById('image');
            if (inputBackground) {
                inputBackground.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (!ALLOWED.includes(file.type)) {
                        alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                        this.value = '';
                        return;
                    }

                    if (file.size > MAX_SIZE) {
                        alert('Ukuran file lebih dari 2 MB.');
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

            // ==== Logo Upload Handler ====
            const inputLogo = document.getElementById('logo');
            if (inputLogo) {
                inputLogo.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (!ALLOWED.includes(file.type)) {
                        alert('Tipe file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                        this.value = '';
                        return;
                    }

                    if (file.size > MAX_SIZE) {
                        alert('Ukuran file lebih dari 2 MB.');
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

            // ==== Initialize Cropper Background ====
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
            // ==== Crop Background Button ====
            document.getElementById('cropBackgroundBtn')?.addEventListener('click', function() {
                if (!cropperBackground) return;

                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                const isTransparent = currentBackgroundFile.type === 'image/png' || currentBackgroundFile
                    .type ===
                    'image/webp';
                const outputType = isTransparent ? currentBackgroundFile.type : 'image/jpeg';
                const quality = isTransparent ? 1 : 0.92;

                const canvas = cropperBackground.getCroppedCanvas({
                    width: 1920,
                    height: 1080,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], currentBackgroundFile.name, {
                        type: outputType,
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    inputBackground.files = dataTransfer.files;

                    const url = URL.createObjectURL(blob);
                    const preview = document.getElementById('imagePreview');
                    if (preview) {
                        preview.src = url;
                        preview.style.display = 'block';
                        const wrapper = document.getElementById('imagePreviewWrapper');
                        if (wrapper) wrapper.classList.remove('d-none');
                    }

                    $('#cropBackgroundModal').modal('hide');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, outputType, quality);
            });

            // ==== Initialize Cropper Logo ====
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

            // ==== Crop Logo Button ====
            document.getElementById('cropLogoBtn')?.addEventListener('click', function() {
                if (!cropperLogo) return;

                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                const isTransparent = currentLogoFile.type === 'image/png' || currentLogoFile.type ===
                    'image/webp';
                const outputType = isTransparent ? currentLogoFile.type : 'image/jpeg';
                const quality = isTransparent ? 1 : 0.92;
                const canvas = cropperLogo.getCroppedCanvas({
                    width: 800,
                    height: 800,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], currentLogoFile.name, {
                        type: outputType,
                        lastModified: Date.now()
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    inputLogo.files = dataTransfer.files;

                    const url = URL.createObjectURL(blob);
                    const preview = document.getElementById('imagePreview2');
                    if (preview) {
                        preview.src = url;
                        preview.style.display = 'block';
                        const wrapper = document.getElementById('imagePreviewWrapper2');
                        if (wrapper) wrapper.classList.remove('d-none');
                    }

                    $('#cropLogoModal').modal('hide');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, outputType, quality);
            });

            // ==== Delete Background Handler ====
            document.getElementById('clearImageBtn')?.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus background picture?')) {
                    const wrapper = document.getElementById('imagePreviewWrapper');
                    const preview = document.getElementById('imagePreview');
                    if (wrapper) wrapper.classList.add('d-none');
                    if (preview) preview.src = '';
                    if (inputBackground) inputBackground.value = '';
                }
            });

            // ==== Delete Logo Handler ====
            document.getElementById('clearImageBtn2')?.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus logo?')) {
                    const wrapper = document.getElementById('imagePreviewWrapper2');
                    const preview = document.getElementById('imagePreview2');
                    if (wrapper) wrapper.classList.add('d-none');
                    if (preview) preview.src = '';
                    if (inputLogo) inputLogo.value = '';
                }
            });

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
                if (!selectEl) return;
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
                if (selectEl) {
                    selectEl.innerHTML = `<option value="">${message}</option>`;
                }
            }

            function fillHiddenName(selectEl, hiddenInput) {
                if (hiddenInput && selectEl) {
                    hiddenInput.value = selectEl.options[selectEl.selectedIndex]?.text || '';
                }
            }

            function loadOptions(url, selectEl, spinnerEl, defaultMsg) {
                if (!selectEl) return Promise.resolve();

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
                    })
                    .finally(() => setLoading(selectEl, spinnerEl, false));
            }

            // Load Provinces on page load
            if (provinceSelect) {
                loadOptions(
                    `${API_BASE}/provinces.json`,
                    provinceSelect,
                    spnProvince,
                    'Pilih Provinsi'
                );
            }

            // Province Change Handler
            provinceSelect?.addEventListener('change', function() {
                fillHiddenName(provinceSelect, provinceNameInput);
                resetSelect(citySelect, 'Pilih Kota/Kabupaten');
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Kelurahan');
                if (citySelect) citySelect.disabled = true;
                if (districtSelect) districtSelect.disabled = true;
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/regencies/${this.value}.json`,
                        citySelect,
                        spnCity,
                        'Pilih Kota/Kabupaten'
                    );
                }
            });

            // City Change Handler
            citySelect?.addEventListener('change', function() {
                fillHiddenName(citySelect, cityNameInput);
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Kelurahan');
                if (districtSelect) districtSelect.disabled = true;
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/districts/${this.value}.json`,
                        districtSelect,
                        spnDistrict,
                        'Pilih Kecamatan'
                    );
                }
            });

            // District Change Handler
            districtSelect?.addEventListener('change', function() {
                fillHiddenName(districtSelect, districtNameInput);
                resetSelect(villageSelect, 'Pilih Kelurahan');
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/villages/${this.value}.json`,
                        villageSelect,
                        spnVillage,
                        'Pilih Kelurahan'
                    );
                }
            });

            // Village Change Handler
            villageSelect?.addEventListener('change', function() {
                fillHiddenName(villageSelect, villageNameInput);
            });

            // Form submission handling
            const outletForm = document.getElementById('outletForm');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            // Cancel button
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Simple check for changes
                let hasChanges = false;

                // Check only key fields
                const nameInput = document.querySelector('input[name="name"]');
                const usernameInput = document.querySelector('input[name="username"]');
                const emailInput = document.querySelector('input[name="email"]');

                if ((nameInput && nameInput.value.trim()) ||
                    (usernameInput && usernameInput.value.trim()) ||
                    (emailInput && emailInput.value.trim())) {
                    hasChanges = true;
                }

                const confirmMsg = hasChanges ?
                    '{{ __('You have unsaved changes. Are you sure you want to cancel?') }}' :
                    '{{ __('Are you sure you want to cancel?') }}';

                if (confirm(confirmMsg)) {
                    window.location.href = '{{ route('owner.user-owner.outlets.index') }}';
                }
            });

            // Form submission handler
            outletForm.addEventListener('submit', function(e) {
                // Prevent double submission
                if (saveBtn.classList.contains('loading')) {
                    e.preventDefault();
                    return;
                }

                // Show loading state
                saveBtn.classList.add('loading');
                saveBtn.disabled = true;
                saveBtn.querySelector('.btn-label').textContent = 'Saving...';

                // Validate required fields
                const requiredFields = outletForm.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                // Validate password match
                const password = document.getElementById('password')?.value || '';
                const passwordConfirm = document.getElementById('password_confirmation')?.value || '';

                if (password !== passwordConfirm) {
                    alert('Password dan konfirmasi password tidak sama');
                    isValid = false;
                }

                if (!isValid) {
                    // Scroll to first error
                    const firstError = outletForm.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }

                    // Reset button
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    saveBtn.querySelector('.btn-label').textContent = 'Save';

                    e.preventDefault();
                    return;
                }

                // Allow form to submit normally
            });

            // Reset button on page reload
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    saveBtn.querySelector('.btn-label').textContent = 'Save';
                }
            });

            // Handle Enter key (prevent accidental submission)
            outletForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
                    // Find next focusable element
                    const formElements = Array.from(outletForm.elements);
                    const currentIndex = formElements.indexOf(e.target);

                    if (currentIndex < formElements.length - 1) {
                        e.preventDefault();
                        const nextElement = formElements[currentIndex + 1];
                        if (nextElement && nextElement.tabIndex !== -1) {
                            nextElement.focus();
                        }
                    }
                }
            });

            // Simple change detection for beforeunload
            window.addEventListener('beforeunload', function(e) {
                if (saveBtn.classList.contains('loading')) {
                    return; // Don't warn if already submitting
                }

                const nameInput = document.querySelector('input[name="name"]');
                const usernameInput = document.querySelector('input[name="username"]');

                if ((nameInput && nameInput.value.trim()) ||
                    (usernameInput && usernameInput.value.trim())) {
                    e.preventDefault();
                    e.returnValue =
                        '{{ __('You have unsaved changes. Are you sure you want to leave?') }}';
                }
            });
        });
    </script>
@endpush
