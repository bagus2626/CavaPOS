@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.edit_outlet'))
@section('page_title', __('messages.owner.outlet.all_outlets.edit_outlet'))

@section('content')
    <div class="modern-outlet-editor">
        {{-- Header Toolbar --}}
        <div class="editor-header">
            <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>

            <h1 class="page-title">{{ $outlet->name }}</h1>
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

            <form action="{{ route('owner.user-owner.outlets.update', $outlet->id) }}" method="POST"
                enctype="multipart/form-data" id="employeeForm">
                @csrf
                @method('PUT')

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
                                        class="form-input @error('name') is-invalid @enderror"
                                        value="{{ old('name', $outlet->name) }}" required>
                                    @error('name')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </label>

                                <input type="hidden" id="usernameCheckUrl"
                                    value="{{ route('owner.user-owner.outlets.check-username') }}">

                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.username') }}</span>
                                    <div class="input-with-button">
                                        <div class="input-prefix">@</div>
                                        <input type="text" name="username" id="username"
                                            class="form-input @error('username') is-invalid @enderror"
                                            value="{{ old('username', $outlet->username) }}" required minlength="3"
                                            maxlength="30" pattern="^[A-Za-z0-9._\-]+$"
                                            data-exclude-id="{{ $outlet->id }}">
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
                                    <input type="text" class="form-input" value="{{ $outlet->slug }}" disabled>
                                    <input type="hidden" name="slug" value="{{ $outlet->slug }}">
                                    <small
                                        class="form-hint">{{ __('messages.owner.outlet.all_outlets.slug_cant_change') }}</small>
                                </label>

                                <label class="form-group">
                                    <span
                                        class="form-label required">{{ __('messages.owner.outlet.all_outlets.email_outlet') }}</span>
                                    <input type="email" name="email" id="email"
                                        class="form-input @error('email') is-invalid @enderror"
                                        value="{{ old('email', $outlet->email) }}" required>
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
                                        <select id="province" name="province" class="form-input" disabled
                                            data-selected-id="{{ old('province', $outlet->province_id) }}">
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.load_province') }}</option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnProvince"></span>
                                    </div>
                                    <input type="hidden" id="province_name" name="province_name"
                                        value="{{ old('province_name', $outlet->province) }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.city') }}</span>
                                    <div class="select-wrapper">
                                        <select id="city" name="city" class="form-input" disabled
                                            data-selected-id="{{ old('city', $outlet->city_id) }}">
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_province_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnCity"></span>
                                    </div>
                                    <input type="hidden" id="city_name" name="city_name"
                                        value="{{ old('city_name', $outlet->city) }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.district') }}</span>
                                    <div class="select-wrapper">
                                        <select id="district" name="district" class="form-input" disabled
                                            data-selected-id="{{ old('district', $outlet->subdistrict_id) }}">
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_city_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnDistrict"></span>
                                    </div>
                                    <input type="hidden" id="district_name" name="district_name"
                                        value="{{ old('district_name', $outlet->subdistrict) }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('messages.owner.outlet.all_outlets.village') }}</span>
                                    <div class="select-wrapper">
                                        <select id="village" name="village" class="form-input" disabled
                                            data-selected-id="{{ old('village', $outlet->urban_village_id) }}">
                                            <option value="">
                                                {{ __('messages.owner.outlet.all_outlets.select_district_first') }}
                                            </option>
                                        </select>
                                        <span class="loading-spinner d-none" id="spnVillage"></span>
                                    </div>
                                    <input type="hidden" id="village_name" name="village_name"
                                        value="{{ old('village_name', $outlet->urban_village) }}">
                                </label>

                                <label class="form-group form-group-full">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.detail_address') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-road input-icon"></i>
                                        <input type="text" id="address" name="address" class="form-input"
                                            value="{{ old('address', $outlet->address) }}"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.detail_address_placeholder') }}">
                                    </div>
                                </label>

                                <label class="form-group form-group-full">
                                    <span class="form-label">Google Maps Embed URL</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-map-marked-alt input-icon"></i>
                                        <input type="url" id="gmaps_url" name="gmaps_url" class="form-input"
                                            value="{{ old('gmaps_url', $outlet->profileOutlet->gmaps_url ?? '') }}"
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
                                {{-- Logo --}}
                                <div class="form-group">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_logo_optional') }}</span>
                                    <div class="media-upload-group">
                                        <div class="media-preview media-preview-logo {{ $outlet->logo ? '' : 'd-none' }}"
                                            id="imagePreviewWrapper2">
                                            <img id="imagePreview2"
                                                src="{{ $outlet->logo ? asset('storage/' . $outlet->logo) : '' }}"
                                                alt="Logo Preview" class="media-image media-image-logo">
                                            <button type="button" id="clearImageBtn2"
                                                class="media-remove-btn">&times;</button>
                                        </div>
                                        <button type="button" class="btn-upload"
                                            onclick="document.getElementById('logo').click()">
                                            {{ __('Change Logo') }}
                                        </button>
                                        <input type="file" name="logo" id="logo" class="d-none"
                                            accept="image/*">
                                        <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                                        <small
                                            class="form-hint">{{ __('messages.owner.outlet.all_outlets.muted_text_2') }}</small>
                                    </div>
                                </div>
                                {{-- Background Picture --}}
                                <div class="form-group">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.upload_picture_optional') }}</span>
                                    <div class="media-upload-group">
                                        <div class="media-preview {{ $outlet->background_picture ? '' : 'd-none' }}"
                                            id="imagePreviewWrapper">
                                            <img id="imagePreview"
                                                src="{{ $outlet->background_picture ? asset('storage/' . $outlet->background_picture) : '' }}"
                                                alt="Background Preview" class="media-image media-image-cover">
                                            <button type="button" id="clearImageBtn"
                                                class="media-remove-btn">&times;</button>
                                        </div>
                                        <button type="button" class="btn-upload"
                                            onclick="document.getElementById('image').click()">
                                            {{ __('Change Photo') }}
                                        </button>
                                        <input type="file" name="image" id="image" class="d-none"
                                            accept="image/*">
                                        <input type="hidden" name="remove_background_picture"
                                            id="remove_background_picture" value="0">
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
                                            class="form-input"
                                            value="{{ old('contact_person', $outlet->profileOutlet->contact_person ?? '') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('Phone Number') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" id="contact_phone" name="contact_phone" class="form-input"
                                            value="{{ old('contact_phone', $outlet->profileOutlet->contact_phone ?? '') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Instagram</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="instagram" class="form-input"
                                            value="{{ old('instagram') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Twitter / X</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="twitter" class="form-input"
                                            value="{{ old('twitter') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">WhatsApp</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">+62</span>
                                        <input type="tel" name="whatsapp" class="form-input"
                                            value="{{ old('whatsapp') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Facebook</span>
                                    <input type="text" name="facebook" class="form-input"
                                        value="{{ old('facebook') }}">
                                </label>

                                <label class="form-group">
                                    <span class="form-label">TikTok</span>
                                    <div class="input-with-prefix">
                                        <span class="input-prefix">@</span>
                                        <input type="text" name="tiktok" class="form-input"
                                            value="{{ old('tiktok') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">Website</span>
                                    <input type="url" name="website" class="form-input"
                                        value="{{ old('website') }}">
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
                                        {{ old('is_active', (int) $outlet->is_active) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            {{-- QR Mode --}}
                            <div class="form-group mt-4">
                                <span class="form-label">{{ __('messages.owner.outlet.all_outlets.activate_qr') }}</span>
                                <select id="qr_mode" name="qr_mode" class="form-input">
                                    <option value="disabled"
                                        {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'disabled' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.inactive') }}
                                    </option>
                                    <option value="barcode_only"
                                        {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'barcode_only' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.qr_only') }}
                                    </option>
                                    <option value="cashier_only"
                                        {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'cashier_only' ? 'selected' : '' }}>
                                        {{ __('messages.owner.outlet.all_outlets.cashier_only') }}
                                    </option>
                                    <option value="both"
                                        {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'both' ? 'selected' : '' }}>
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
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.password_optional') }}</span>
                                    <div class="input-with-button">
                                        <input type="password" name="password" id="password" class="form-input"
                                            minlength="8"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.password_optional_placeholder') }}">
                                        <button type="button" id="togglePassword" class="input-button">
                                            {{ __('messages.owner.outlet.all_outlets.show') }}
                                        </button>
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span
                                        class="form-label">{{ __('messages.owner.outlet.all_outlets.password_confirmation') }}</span>
                                    <div class="input-with-button">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-input" minlength="8">
                                        <button type="button" id="togglePasswordConfirm" class="input-button">
                                            {{ __('messages.owner.outlet.all_outlets.show') }}
                                        </button>
                                    </div>
                                </label>
                            </div>

                            {{-- WiFi --}}
                            <div class="toggle-group mt-4">
                                <div>
                                    <p class="toggle-label">{{ __('messages.owner.outlet.all_outlets.wifi_information') }}
                                    </p>
                                    <p class="toggle-description">
                                        {{ __('messages.owner.outlet.all_outlets.wifi_description') }}</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="hidden" name="is_wifi_shown" value="0">
                                    <input type="checkbox" id="is_wifi_shown" name="is_wifi_shown" value="1"
                                        {{ old('is_wifi_shown', $outlet->is_wifi_shown ?? 0) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div id="wifiFormFields" class="form-grid form-grid-2 mt-4"
                                style="display: {{ old('is_wifi_shown', $outlet->is_wifi_shown ?? 0) ? 'grid' : 'none' }};">
                                <label class="form-group">
                                    <span class="form-label">{{ __('WiFi Name (SSID)') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-wifi input-icon"></i>
                                        <input type="text" id="user_wifi" name="user_wifi" class="form-input"
                                            value="{{ old('user_wifi', $outlet->user_wifi ?? '') }}">
                                    </div>
                                </label>

                                <label class="form-group">
                                    <span class="form-label">{{ __('WiFi Password') }}</span>
                                    <div class="input-with-icon">
                                        <i class="fas fa-key input-icon"></i>
                                        <div class="input-with-button" style="width: 100%;">
                                            <input type="password" id="pass_wifi" name="pass_wifi" class="form-input"
                                                value="{{ old('pass_wifi', $outlet->pass_wifi ?? '') }}"
                                                style="padding-left: 2.75rem;">
                                            <button type="button" id="toggleWifiPassword"
                                                class="input-button">{{ __('messages.owner.outlet.all_outlets.show') }}</button>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>


                    {{-- Action Buttons --}}
                    <div class="form-actions">
                        <button type="button" id="cancelBtn" class="btn-cancel">
                            <i class="fas fa-times"></i>
                            {{ __('messages.owner.outlet.all_outlets.cancel') }}
                        </button>
                        <button type="submit" form="employeeForm" id="saveBtn" class="btn-save">
                            <i class="fas fa-save"></i>
                            <span class="btn-label">{{ __('messages.owner.outlet.all_outlets.save') }}</span>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </div>
                </div>
                {{-- End sections-container --}}
            </form>
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

        /* ===== Input Variations ===== */
        .input-with-button {
            display: flex;
            position: relative;
        }

        .input-prefix {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-weight: 500;
            pointer-events: none;
            z-index: 1;
        }

        .input-with-button .form-input {
            flex: 1;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            padding-left: 2.5rem;
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
            left: 1rem;
            color: #9ca3af;
            pointer-events: none;
            z-index: 1;
        }

        .input-with-icon .form-input {
            padding-left: 2.75rem;
        }

        .input-with-prefix {
            position: relative;
        }

        .input-with-prefix .input-prefix {
            left: 1rem;
        }

        .input-with-prefix .form-input {
            padding-left: 2.5rem;
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

            .footer-container {
                flex-direction: column;
            }

            .btn-secondary,
            .btn-primary {
                width: 100%;
                text-align: center;
            }
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

        /* Responsive */
        @media (max-width: 768px) {
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
            /* rata tengah vertikal */
            gap: 16px;
            /* spasi antar elemen */
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        .page-title {
            margin: 0;
            /* biar tidak geser */
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Username check functionality
            const usernameInput = document.getElementById('username');
            const btnCheckUsername = document.getElementById('btnCheckUsername');
            const usernameStatus = document.getElementById('usernameStatus');
            const checkUrl = document.getElementById('usernameCheckUrl').value;
            const excludeId = usernameInput.dataset.excludeId;

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

            const selectedProvince = provinceSelect?.dataset.selectedId || '';
            const selectedCity = citySelect?.dataset.selectedId || '';
            const selectedDistrict = districtSelect?.dataset.selectedId || '';
            const selectedVillage = villageSelect?.dataset.selectedId || '';

            // Form submission handling
            const employeeForm = document.getElementById('employeeForm');
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

                if ((nameInput && nameInput.value !== '{{ $outlet->name }}') ||
                    (usernameInput && usernameInput.value !== '{{ $outlet->username }}') ||
                    (emailInput && emailInput.value !== '{{ $outlet->email }}')) {
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
            employeeForm.addEventListener('submit', function(e) {
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
                const requiredFields = employeeForm.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    // Scroll to first error
                    const firstError = employeeForm.querySelector('.is-invalid');
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
                    saveBtn.querySelector('.btn-label').textContent = 'Save Changes';

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
                    saveBtn.querySelector('.btn-label').textContent = 'Save Changes';
                }
            });

            // Handle Enter key (prevent accidental submission)
            employeeForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
                    // Find next focusable element
                    const formElements = Array.from(employeeForm.elements);
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

                if ((nameInput && nameInput.value !== '{{ $outlet->name }}') ||
                    (usernameInput && usernameInput.value !== '{{ $outlet->username }}')) {
                    e.preventDefault();
                    e.returnValue =
                        '{{ __('You have unsaved changes. Are you sure you want to leave?') }}';
                }
            });

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

            function loadOptions(url, selectEl, spinnerEl, defaultMsg, selectedId = null) {
                setLoading(selectEl, spinnerEl, true, 'Loading...');
                return fetch(url)
                    .then(r => r.json())
                    .then(list => {
                        resetSelect(selectEl, defaultMsg);
                        list.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            if (selectedId && item.id == selectedId) opt.selected = true;
                            selectEl.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        resetSelect(selectEl, 'Failed to load data');
                        alert('Failed to load data');
                    })
                    .finally(() => setLoading(selectEl, spinnerEl, false));
            }

            // Load initial provinces
            loadOptions(
                `${API_BASE}/provinces.json`,
                provinceSelect,
                spnProvince,
                'Pilih Provinsi',
                selectedProvince
            ).then(() => {
                if (selectedProvince) {
                    fillHiddenName(provinceSelect, provinceNameInput);

                    loadOptions(
                        `${API_BASE}/regencies/${selectedProvince}.json`,
                        citySelect,
                        spnCity,
                        'Pilih Kota',
                        selectedCity
                    ).then(() => {
                        if (selectedCity) {
                            fillHiddenName(citySelect, cityNameInput);

                            loadOptions(
                                `${API_BASE}/districts/${selectedCity}.json`,
                                districtSelect,
                                spnDistrict,
                                'Pilih Kecamatan',
                                selectedDistrict
                            ).then(() => {
                                if (selectedDistrict) {
                                    fillHiddenName(districtSelect, districtNameInput);

                                    loadOptions(
                                        `${API_BASE}/villages/${selectedDistrict}.json`,
                                        villageSelect,
                                        spnVillage,
                                        'Pilih Kelurahan',
                                        selectedVillage
                                    ).then(() => {
                                        if (selectedVillage) {
                                            fillHiddenName(villageSelect,
                                                villageNameInput);
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                fillHiddenName(provinceSelect, provinceNameInput);
                resetSelect(citySelect, 'Pilih Kota');
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Kelurahan');
                citySelect.disabled = districtSelect.disabled = villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/regencies/${this.value}.json`,
                        citySelect,
                        spnCity,
                        'Pilih Kota'
                    );
                }
            });

            // City change handler
            citySelect.addEventListener('change', function() {
                fillHiddenName(citySelect, cityNameInput);
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Kelurahan');
                districtSelect.disabled = villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/districts/${this.value}.json`,
                        districtSelect,
                        spnDistrict,
                        'Pilih Kecamatan'
                    );
                }
            });

            // District change handler
            districtSelect.addEventListener('change', function() {
                fillHiddenName(districtSelect, districtNameInput);
                resetSelect(villageSelect, 'Pilih Kelurahan');
                villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(
                        `${API_BASE}/villages/${this.value}.json`,
                        villageSelect,
                        spnVillage,
                        'Pilih Kelurahan'
                    );
                }
            });

            // Village change handler
            villageSelect.addEventListener('change', function() {
                fillHiddenName(villageSelect, villageNameInput);
            });

            btnCheckUsername.addEventListener('click', function() {
                const username = usernameInput.value.trim();

                if (!username) {
                    showStatus('error', 'Please enter a username');
                    return;
                }

                // Show loading
                btnCheckUsername.querySelector('.label').textContent = 'Checking...';
                btnCheckUsername.querySelector('.spinner-border').classList.remove('d-none');
                btnCheckUsername.disabled = true;

                fetch(`${checkUrl}?username=${username}&exclude_id=${excludeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            showStatus('success', 'Username available!');
                        } else {
                            showStatus('error', 'Username already taken');
                        }
                    })
                    .catch(error => {
                        showStatus('error', 'Error checking username');
                    })
                    .finally(() => {
                        btnCheckUsername.querySelector('.label').textContent = 'Check';
                        btnCheckUsername.querySelector('.spinner-border').classList.add('d-none');
                        btnCheckUsername.disabled = false;
                    });
            });

            function showStatus(type, message) {
                usernameStatus.className = `status-message ${type}`;
                usernameStatus.textContent = message;
                usernameStatus.style.display = 'block';
            }

            // Password toggle
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });

            document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
                const passwordInput = document.getElementById('password_confirmation');
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });

            // WiFi password toggle
            const toggleWifiPasswordBtn = document.getElementById('toggleWifiPassword');
            if (toggleWifiPasswordBtn) {
                toggleWifiPasswordBtn.addEventListener('click', function() {
                    const wifiPasswordInput = document.getElementById('pass_wifi');
                    const type = wifiPasswordInput.type === 'password' ? 'text' : 'password';
                    wifiPasswordInput.type = type;
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            }

            // WiFi toggle
            const wifiEnabledCheckbox = document.getElementById('is_wifi_shown');
            const wifiFormFields = document.getElementById('wifiFormFields');

            if (wifiEnabledCheckbox && wifiFormFields) {
                wifiEnabledCheckbox.addEventListener('change', function() {
                    wifiFormFields.style.display = this.checked ? 'grid' : 'none';
                });
            }

            // Image preview and cropper functionality
            let backgroundCropper, logoCropper;

            // Background image
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
            const clearImageBtn = document.getElementById('clearImageBtn');
            const removeBackgroundInput = document.getElementById('remove_background_picture');
            const cropBackgroundModal = $('#cropBackgroundModal');
            const imageToCropBackground = document.getElementById('imageToCropBackground');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageToCropBackground.src = event.target.result;
                        cropBackgroundModal.modal('show');
                    };
                    reader.readAsDataURL(file);
                }
            });

            cropBackgroundModal.on('shown.bs.modal', function() {
                if (backgroundCropper) {
                    backgroundCropper.destroy();
                }
                backgroundCropper = new Cropper(imageToCropBackground, {
                    aspectRatio: 16 / 9,
                    viewMode: 1,
                    autoCropArea: 1
                });
            });

            document.getElementById('cropBackgroundBtn').addEventListener('click', function() {
                const canvas = backgroundCropper.getCroppedCanvas({
                    maxWidth: 1600,      // samakan dengan resize di server
                    maxHeight: 1600,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function(blob) {
                    console.log('Background blob size (bytes):', blob.size); // bisa cek di console

                    const file = new File([blob], 'background.jpg', {
                        type: 'image/jpeg'
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    imageInput.files = dataTransfer.files;

                    imagePreview.src = canvas.toDataURL('image/jpeg', 0.8);
                    imagePreviewWrapper.classList.remove('d-none');
                    removeBackgroundInput.value = '0';
                    cropBackgroundModal.modal('hide');
                }, 'image/jpeg', 0.8); // quality 0.8 biar lebih kecil
            });

            clearImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreviewWrapper.classList.add('d-none');
                removeBackgroundInput.value = '1';
            });

            // Logo image
            const logoInput = document.getElementById('logo');
            const imagePreview2 = document.getElementById('imagePreview2');
            const imagePreviewWrapper2 = document.getElementById('imagePreviewWrapper2');
            const clearImageBtn2 = document.getElementById('clearImageBtn2');
            const removeLogoInput = document.getElementById('remove_logo');
            const cropLogoModal = $('#cropLogoModal');
            const imageToCropLogo = document.getElementById('imageToCropLogo');

            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageToCropLogo.src = event.target.result;
                        cropLogoModal.modal('show');
                    };
                    reader.readAsDataURL(file);
                }
            });

            cropLogoModal.on('shown.bs.modal', function() {
                if (logoCropper) {
                    logoCropper.destroy();
                }
                logoCropper = new Cropper(imageToCropLogo, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1
                });
            });

            document.getElementById('cropLogoBtn').addEventListener('click', function() {
                const canvas = logoCropper.getCroppedCanvas({
                    maxWidth: 800,
                    maxHeight: 800,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function(blob) {
                    console.log('Logo blob size (bytes):', blob.size);

                    const file = new File([blob], 'logo.jpg', {
                        type: 'image/jpeg'
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    logoInput.files = dataTransfer.files;

                    imagePreview2.src = canvas.toDataURL('image/jpeg', 0.8);
                    imagePreviewWrapper2.classList.remove('d-none');
                    removeLogoInput.value = '0';
                    cropLogoModal.modal('hide');
                }, 'image/jpeg', 0.8);
            });

            clearImageBtn2.addEventListener('click', function() {
                logoInput.value = '';
                imagePreview2.src = '';
                imagePreviewWrapper2.classList.add('d-none');
                removeLogoInput.value = '1';
            });
        });
    </script>
@endpush
