@extends('layouts.owner')

@section('title', __('messages.owner.outlet.all_outlets.edit_outlet'))
@section('page_title', __('messages.owner.outlet.all_outlets.edit_outlet'))

@section('content')
    <div class="modern-container">
            <div class="container-modern">
                <div class="page-header">
                    <div class="header-content">
                        <h1 class="page-title">{{ __('messages.owner.outlet.all_outlets.edit_outlet') }}</h1>
                        <p class="page-subtitle">{{ __('messages.owner.outlet.all_outlets.edit_subtitle') }}</p>
                    </div>
                <a href="{{ route('owner.user-owner.outlets.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    {{ __('messages.owner.outlet.all_outlets.back') }}
                </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-modern">
                        <div class="alert-icon">
                            <span class="material-symbols-outlined">error</span>
                        </div>
                        <div class="alert-content">
                            <strong>{{ __('messages.owner.outlet.all_outlets.re_check_input') }}</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-modern">
                        <div class="alert-icon">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <div class="alert-content">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-info alert-modern">
                        <div class="alert-icon">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <div class="alert-content">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="modern-card">
                    <input type="hidden" id="usernameCheckUrl" value="{{ route('owner.user-owner.outlets.check-username') }}">

                    <form action="{{ route('owner.user-owner.outlets.update', $outlet->id) }}" method="POST"
                        enctype="multipart/form-data" id="employeeForm">
                        @csrf
                        @method('PUT')

                        <div class="card-body-modern">
                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">info</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.base_information') }}</h3>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.outlet.all_outlets.outlet_name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control-modern @error('name') is-invalid @enderror"
                                            value="{{ old('name', $outlet->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- username --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.outlet.all_outlets.username') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        
                                        <div class="input-wrapper position-relative">
                                            <span class="input-icon">
                                                <span class="material-symbols-outlined">alternate_email</span>
                                            </span>
                                            
                                            <input type="text" name="username" id="username"
                                                class="form-control-modern with-icon @error('username') is-invalid @enderror"
                                                value="{{ old('username', $outlet->username) }}" 
                                                required 
                                                minlength="3"
                                                maxlength="30" 
                                                pattern="^[A-Za-z0-9._\-]+$"
                                                data-exclude-id="{{ $outlet->id }}"
                                                autocomplete="off">

                                            <div id="usernameLoading" class="position-absolute d-none" style="right: 15px; top: 50%; transform: translateY(-50%); z-index: 5;">
                                                <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                    <span class="sr-only">{{ __('messages.owner.outlet.all_outlets.loading') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="usernameStatus" class="mt-2"></div>
                                        
                                        @error('username')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Slug</label>
                                        <input type="text" class="form-control-modern" value="{{ $outlet->slug }}" disabled>
                                        <input type="hidden" name="slug" value="{{ $outlet->slug }}">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.outlet.all_outlets.email_outlet') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" id="email"
                                            class="form-control-modern @error('email') is-invalid @enderror"
                                            value="{{ old('email', $outlet->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.outlet_address') }}</h3>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.province') }}</label>
                                        <div class="select-wrapper">
                                            <select id="province" name="province" class="form-control-modern" disabled
                                                data-selected-id="{{ old('province', $outlet->province_id) }}">
                                                <option value="">{{ __('messages.owner.outlet.all_outlets.load_province') }}</option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <input type="hidden" id="province_name" name="province_name"
                                            value="{{ old('province_name', $outlet->province) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.city') }}</label>
                                        <div class="select-wrapper">
                                            <select id="city" name="city" class="form-control-modern" disabled
                                                data-selected-id="{{ old('city', $outlet->city_id) }}">
                                                <option value="">{{ __('messages.owner.outlet.all_outlets.select_province_first') }}</option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <input type="hidden" id="city_name" name="city_name"
                                            value="{{ old('city_name', $outlet->city) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.district') }}</label>
                                        <div class="select-wrapper">
                                            <select id="district" name="district" class="form-control-modern" disabled
                                                data-selected-id="{{ old('district', $outlet->subdistrict_id) }}">
                                                <option value="">{{ __('messages.owner.outlet.all_outlets.select_city_first') }}</option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <input type="hidden" id="district_name" name="district_name"
                                            value="{{ old('district_name', $outlet->subdistrict) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.village') }}</label>
                                        <div class="select-wrapper">
                                            <select id="village" name="village" class="form-control-modern" disabled
                                                data-selected-id="{{ old('village', $outlet->urban_village_id) }}">
                                                <option value="">{{ __('messages.owner.outlet.all_outlets.select_district_first') }}</option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>
                                        <input type="hidden" id="village_name" name="village_name"
                                            value="{{ old('village_name', $outlet->urban_village) }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.detail_address') }}</label>
                                        <input type="text" id="address" name="address" class="form-control-modern"
                                            value="{{ old('address', $outlet->address) }}"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.detail_address_placeholder') }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.google_maps_embed_url') }}</label>
                                        <input type="url" id="gmaps_url" name="gmaps_url" class="form-control-modern"
                                            value="{{ old('gmaps_url', $outlet->profileOutlet->gmaps_url ?? '') }}"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_maps') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">image</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.contact_picture') }}</h3>
                            </div>

                            <div class="row g-4 mb-4">
                                {{-- Logo --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.outlet.all_outlets.upload_logo_optional') }}
                                        </label>

                                        <div class="profile-picture-container"
                                            id="logoPictureContainer"
                                            onclick="document.getElementById('logo').click()"
                                            style="width: 200px; height: 200px; margin: 0 auto;">

                                            <div class="upload-placeholder"
                                                id="logoPlaceholder"
                                                style="{{ $outlet->logo ? 'display:none;' : '' }}">
                                                <span class="material-symbols-outlined">add_business</span>
                                                <span class="upload-text">{{ __('messages.owner.outlet.all_outlets.upload_logo_text') }}</span>
                                            </div>

                                            <img id="imagePreview2"
                                                class="profile-preview {{ $outlet->logo ? 'active' : '' }}"
                                                src="{{ $outlet->logo ? asset('storage/' . $outlet->logo) : '' }}"
                                                alt="Logo Preview">
                                            <button type="button" id="removeLogoBtn" class="btn-remove btn-remove-top" 
                                                style="{{ $outlet->logo ? 'display: block;' : 'display: none;' }}">
                                                <span class="material-symbols-outlined">close</span>
                                            </button>
                                        </div>
                                        <input type="file" name="logo" id="logo" accept="image/*" style="display:none;">
                                        <input type="hidden" name="remove_logo" id="remove_logo" value="0">

                                        <small class="text-muted d-block mt-2 ">
                                            {{ __('messages.owner.outlet.all_outlets.muted_text_2') }}
                                        </small>
                                    </div>
                                </div>

                                {{-- Background Picture --}}
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            {{ __('messages.owner.outlet.all_outlets.upload_picture_optional') }}
                                        </label>

                                        <div class="profile-picture-container"
                                            id="backgroundPictureContainer"
                                                onclick="document.getElementById('image').click()"
                                            style="width: 100%; height: 200px; border-radius: 1rem;">

                                            <div class="upload-placeholder"
                                                id="backgroundPlaceholder"
                                                style="{{ $outlet->background_picture ? 'display:none;' : '' }}">
                                                <span class="material-symbols-outlined">add_photo_alternate</span>
                                                <span class="upload-text">{{ __('messages.owner.outlet.all_outlets.upload_background_text') }}</span>
                                            </div>

                                            <img id="imagePreview"
                                                class="profile-preview {{ $outlet->background_picture ? 'active' : '' }}"
                                                src="{{ $outlet->background_picture ? asset('storage/' . $outlet->background_picture) : '' }}"
                                                alt="Background Preview">
                                                 <button type="button" id="removeBackgroundBtn" class="btn-remove btn-remove-top" 
                                                    style="{{ $outlet->background_picture ? 'display: block;' : 'display: none;' }}">
                                                    <span class="material-symbols-outlined">close</span>
                                                </button>
                                        </div>

                                        <input type="file" name="image" id="image" accept="image/*" style="display:none;">
                                        <input type="hidden" name="remove_background_picture" id="remove_background_picture" value="0">

                                        <small class="text-muted d-block mt-2">
                                            {{ __('messages.owner.outlet.all_outlets.muted_text_2') }}
                                        </small>
                                    </div>
                                </div>
                            </div>


                            <div class="section-divider"></div>

                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">phone</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.contact_and_social_media') }}</h3>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.contact_name') }}</label>
                                        <input type="text" id="contact_person" name="contact_person" class="form-control-modern"
                                            value="{{ old('contact_person', $outlet->profileOutlet->contact_person ?? '') }}"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.contact_name') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.phone_number') }}</label>
                                        <input type="tel" id="contact_phone" name="contact_phone" class="form-control-modern"
                                            value="{{ old('contact_phone', $outlet->profileOutlet->contact_phone ?? '') }}"
                                            placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_phone') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.instagram') }}</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon">
                                                <span class="material-symbols-outlined">alternate_email</span>
                                            </span>
                                            <input type="text" name="instagram" class="form-control-modern with-icon"
                                                value="{{ old('instagram') }}" placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_social') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.twitter') }}</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon">
                                                <span class="material-symbols-outlined">alternate_email</span>
                                            </span>
                                            <input type="text" name="twitter" class="form-control-modern with-icon"
                                                value="{{ old('twitter') }}" placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_social') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.whatsapp') }}</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon" style="left: 1rem;">+62</span>
                                            <input type="tel" name="whatsapp" class="form-control-modern" style="padding-left: 3.5rem;"
                                                value="{{ old('whatsapp') }}" placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_phone') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.facebook') }}</label>
                                        <input type="text" name="facebook" class="form-control-modern"
                                            value="{{ old('facebook') }}" placeholder="facebook.com/yourpage">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.tiktok') }}</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon">
                                                <span class="material-symbols-outlined">alternate_email</span>
                                            </span>
                                            <input type="text" name="tiktok" class="form-control-modern with-icon"
                                                value="{{ old('tiktok') }}" placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_social') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.website') }}</label>
                                        <input type="url" name="website" class="form-control-modern"
                                            value="{{ old('website') }}" placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_url') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <div class="section-header">
                                <div class="section-icon section-icon-red">
                                    <span class="material-symbols-outlined">settings</span>
                                </div>
                                <h3 class="section-title">{{ __('messages.owner.outlet.all_outlets.outlet_status') }}</h3>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">{{ __('messages.owner.outlet.all_outlets.activate_outlet') }}</label>
                                        <input type="hidden" name="is_active" value="0">
                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', (int) $outlet->is_active) ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label">
                                                {{ old('is_active', (int) $outlet->is_active) ? __('messages.owner.outlet.all_outlets.active') : __('messages.owner.outlet.all_outlets.inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.activate_qr') }}</label>
                                        <div class="select-wrapper">
                                            <select id="qr_mode" name="qr_mode" class="form-control-modern">
                                                <option value="disabled" {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'disabled' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.outlet.all_outlets.inactive') }}
                                                </option>
                                                <option value="barcode_only" {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'barcode_only' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.outlet.all_outlets.qr_only') }}
                                                </option>
                                                <option value="cashier_only" {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'cashier_only' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.outlet.all_outlets.cashier_only') }}
                                                </option>
                                                <option value="both" {{ old('qr_mode', $outlet->qr_mode ?? 'disabled') == 'both' ? 'selected' : '' }}>
                                                    {{ __('messages.owner.outlet.all_outlets.all_methods') }}
                                                </option>
                                            </select>
                                            <span class="material-symbols-outlined select-arrow">expand_more</span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-6"></div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.password_optional') }}</label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password" id="password" class="form-control-modern"
                                                minlength="8" placeholder="{{ __('messages.owner.outlet.all_outlets.min_character') }}">
                                            <button type="button" class="password-toggle" id="togglePassword">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.password_confirmation') }}</label>
                                        <div class="password-wrapper">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control-modern" minlength="8" placeholder="{{ __('messages.owner.outlet.all_outlets.password_optional_placeholder') }}">
                                            <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                                <span class="material-symbols-outlined">visibility_off</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern d-block">{{ __('messages.owner.outlet.all_outlets.wifi_information') }}</label>
                                        <input type="hidden" name="is_wifi_shown" value="0">
                                        <div class="status-switch">
                                            <label class="switch-modern">
                                                <input type="checkbox" id="is_wifi_shown" name="is_wifi_shown" value="1" {{ old('is_wifi_shown', $outlet->is_wifi_shown ?? 0) ? 'checked' : '' }}>
                                                <span class="slider-modern"></span>
                                            </label>
                                            <span class="status-label">
                                                {{ old('is_wifi_shown', $outlet->is_wifi_shown ?? 0) ? __('messages.owner.outlet.all_outlets.wifi_show') : __('messages.owner.outlet.all_outlets.wifi_hide') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div id="wifiFormFields" class="col-12" style="display: {{ old('is_wifi_shown', $outlet->is_wifi_shown ?? 0) ? 'block' : 'none' }};">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.wifi_name_ssid') }}</label>
                                                <input type="text" id="user_wifi" name="user_wifi" class="form-control-modern"
                                                    value="{{ old('user_wifi', $outlet->user_wifi ?? '') }}"
                                                    placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_wifi_ssid') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">{{ __('messages.owner.outlet.all_outlets.wifi_password_field') }}</label>
                                                <div class="password-wrapper">
                                                    <input type="password" id="pass_wifi" name="pass_wifi" class="form-control-modern"
                                                        value="{{ old('pass_wifi', $outlet->pass_wifi ?? '') }}"
                                                        placeholder="{{ __('messages.owner.outlet.all_outlets.placeholder_wifi_pass') }}">
                                                    <button type="button" class="password-toggle" id="toggleWifiPassword">
                                                        <span class="material-symbols-outlined">visibility</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer-modern">
                            <a href="{{ route('owner.user-owner.outlets.index') }}" class="btn-cancel-modern">
                                {{ __('messages.owner.outlet.all_outlets.cancel') }}
                            </a>
                            <button type="submit" id="saveBtn" class="btn-submit-modern">
                                {{ __('messages.owner.outlet.all_outlets.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="cropLogoModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px;">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header">
                            <h5 class="modal-title">
                                <span class="material-symbols-outlined">crop</span>
                                {{ __('messages.owner.outlet.all_outlets.crop_logo_title') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="alert alert-info alert-modern mb-3">
                                <div class="alert-icon">
                                    <span class="material-symbols-outlined">info</span>
                                </div>
                                <div class="alert-content">
                                    <small>{{ __('messages.owner.outlet.all_outlets.drag_to_move_scroll_zoom') }}</small>
                                </div>
                            </div>

                            <div class="img-container-crop">
                                <img id="imageToCropLogo" style="max-width: 100%;" alt="Logo to crop">
                            </div>
                        </div>

                        <div class="modal-footer modern-modal-footer">
                            <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                                <span class="material-symbols-outlined">close</span>
                                {{ __('messages.owner.outlet.all_outlets.cancel') }}
                            </button>
                            <button type="button" id="cropLogoBtn" class="btn-submit-modern">
                                <span class="material-symbols-outlined">check</span>
                                {{ __('messages.owner.outlet.all_outlets.crop_save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="cropBackgroundModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 650px;">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header">
                            <h5 class="modal-title">
                                <span class="material-symbols-outlined">crop</span>
                                {{ __('messages.owner.outlet.all_outlets.crop_background_picture') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="alert alert-info alert-modern mb-3">
                                <div class="alert-icon">
                                    <span class="material-symbols-outlined">info</span>
                                </div>
                                <div class="alert-content">
                                    <small>{{ __('messages.owner.outlet.all_outlets.drag_to_move_scroll_zoom') }}</small>
                                </div>
                            </div>

                            <div class="img-container-crop">
                                <img id="imageToCropBackground" style="max-width: 100%;" alt="Background to crop">
                            </div>
                        </div>

                        <div class="modal-footer modern-modal-footer">
                            <button type="button" class="btn-cancel-modern" data-dismiss="modal">
                                <span class="material-symbols-outlined">close</span>
                                {{ __('messages.owner.outlet.all_outlets.cancel') }}
                            </button>
                            <button type="button" id="cropBackgroundBtn" class="btn-submit-modern">
                                <span class="material-symbols-outlined">check</span>
                                {{ __('messages.owner.outlet.all_outlets.crop_save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>


@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Initialize Background Image Cropper (16:9 Landscape)
        ImageCropper.init({
            id: 'background',
            inputId: 'image',
            previewId: 'imagePreview',
            modalId: 'cropBackgroundModal',
            imageToCropId: 'imageToCropBackground',
            cropBtnId: 'cropBackgroundBtn',
            clearBtnId: 'clearImageBtn',
            removeInputId: 'remove_background_picture',
            aspectRatio: 16 / 9,
            outputWidth: 1920,
            outputHeight: 1080
        });

        // Initialize Logo Cropper (1:1 Square)
        ImageCropper.init({
            id: 'logo',
            inputId: 'logo',
            previewId: 'imagePreview2',
            modalId: 'cropLogoModal',
            imageToCropId: 'imageToCropLogo',
            cropBtnId: 'cropLogoBtn',
            clearBtnId: 'clearImageBtn2',
            removeInputId: 'remove_logo',
            aspectRatio: 1,
            outputWidth: 800,
            outputHeight: 800
        });

        ImageRemoveHandler.init({
        removeBtnId: 'removeLogoBtn',
        imageInputId: 'logo',
        imagePreviewId: 'imagePreview2',
        uploadPlaceholderId: 'logoPlaceholder',
        removeInputId: 'remove_logo',
        confirmRemove: false
    });

    // Initialize Remove Image Handler untuk Background Picture
    ImageRemoveHandler.init({
        removeBtnId: 'removeBackgroundBtn',
        imageInputId: 'image',
        imagePreviewId: 'imagePreview',
        uploadPlaceholderId: 'backgroundPlaceholder',
        removeInputId: 'remove_background_picture',
        confirmRemove: false
    });

        // ==== Realtime Username Check ====
        const usernameInput = document.getElementById('username');
        const usernameStatus = document.getElementById('usernameStatus');
        const usernameLoading = document.getElementById('usernameLoading');
        const checkUrl = document.getElementById('usernameCheckUrl')?.value;
        const excludeId = usernameInput?.dataset.excludeId || '';

        if (usernameInput && checkUrl) {
            function setSpinner(isLoading) {
                if (!usernameLoading) return;
                if (isLoading) {
                    usernameLoading.classList.remove('d-none');
                    if (usernameStatus) usernameStatus.innerHTML = '';
                } else {
                    usernameLoading.classList.add('d-none');
                }
            }

            function updateStatus(type, message) {
                if (!usernameStatus) return;

                if (type === 'success') {
                    usernameStatus.innerHTML = `<span class="badge bg-success">{{ __('messages.owner.outlet.all_outlets.badge_available') }}</span> <span class="text-success ms-2">${message}</span>`;
                    usernameInput.classList.remove('is-invalid');
                    usernameInput.classList.add('is-valid');
                } else if (type === 'error') {
                    usernameStatus.innerHTML = `<span class="badge bg-danger">{{ __('messages.owner.outlet.all_outlets.badge_taken') }}</span> <span class="text-danger ms-2">${message}</span>`;
                    usernameInput.classList.remove('is-valid');
                    usernameInput.classList.add('is-invalid');
                } else {
                    usernameStatus.textContent = message;
                    usernameStatus.className = 'mt-2 text-muted';
                    usernameInput.classList.remove('is-valid', 'is-invalid');
                }
            }

            async function checkUsername() {
                const val = usernameInput.value.trim();

                if (!val) {
                    setSpinner(false);
                    updateStatus('neutral', '');
                    return;
                }

                if (val.length < 3 || val.length > 30 || !/^[A-Za-z0-9._\-]+$/.test(val)) {
                    setSpinner(false);
                    updateStatus('error', '{{ __('messages.owner.outlet.all_outlets.js_username_format') }}');
                    return;
                }

                try {
                    const params = new URLSearchParams({
                        username: val,
                        exclude_id: excludeId
                    });

                    const response = await fetch(`${checkUrl}?${params.toString()}`, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });

                    const data = await response.json();

                    if (data.available) {
                        updateStatus('success', '{{ __('messages.owner.outlet.all_outlets.username_available') }}');
                    } else {
                        updateStatus('error', '{{ __('messages.owner.outlet.all_outlets.username_used') }}');
                    }
                } catch (error) {
                    console.error('Check error:', error);
                } finally {
                    setSpinner(false);
                }
            }

            let debounceTimer;
            usernameInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const val = this.value.trim();

                if (val.length > 0) {
                    setSpinner(true);
                } else {
                    setSpinner(false);
                    updateStatus('neutral', '');
                }

                debounceTimer = setTimeout(checkUsername, 800);
            });
        }

        // ==== Password Toggles ====
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    const icon = this.querySelector('span');
                    if (icon) {
                        icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
                    }
                });
            }
        }

        setupPasswordToggle('togglePassword', 'password');
        setupPasswordToggle('togglePasswordConfirm', 'password_confirmation');
        setupPasswordToggle('toggleWifiPassword', 'pass_wifi');

        // ==== WiFi Toggle ====
        const wifiEnabledCheckbox = document.getElementById('is_wifi_shown');
        const wifiFormFields = document.getElementById('wifiFormFields');
        if (wifiEnabledCheckbox && wifiFormFields) {
            wifiEnabledCheckbox.addEventListener('change', function() {
                wifiFormFields.style.display = this.checked ? 'block' : 'none';
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

        const selectedProvince = provinceSelect?.dataset.selectedId || '';
        const selectedCity = citySelect?.dataset.selectedId || '';
        const selectedDistrict = districtSelect?.dataset.selectedId || '';
        const selectedVillage = villageSelect?.dataset.selectedId || '';

        function setLoading(selectEl, isLoading, placeholder) {
            if (!selectEl) return;
            if (isLoading) {
                selectEl.innerHTML = `<option value="">${placeholder}</option>`;
                selectEl.disabled = true;
            } else {
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

        function loadOptions(url, selectEl, defaultMsg, selectedId = null) {
            if (!selectEl) return Promise.resolve();
            
            setLoading(selectEl, true, '{{ __('messages.owner.outlet.all_outlets.loading') }}');
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
                .catch(() => resetSelect(selectEl, '{{ __('messages.owner.outlet.all_outlets.failed_to_load_data') }}'))
                .finally(() => setLoading(selectEl, false));
        }

        // Initial Load - Chain untuk pre-populate data yang sudah ada
        if (provinceSelect) {
            loadOptions(`${API_BASE}/provinces.json`, provinceSelect, '{{ __('messages.owner.outlet.all_outlets.choose_province') }}', selectedProvince)
                .then(() => {
                    if (selectedProvince && citySelect) {
                        return loadOptions(`${API_BASE}/regencies/${selectedProvince}.json`, citySelect, '{{ __('messages.owner.outlet.all_outlets.choose_city') }}', selectedCity);
                    }
                })
                .then(() => {
                    if (selectedCity && districtSelect) {
                        return loadOptions(`${API_BASE}/districts/${selectedCity}.json`, districtSelect, '{{ __('messages.owner.outlet.all_outlets.choose_district') }}', selectedDistrict);
                    }
                })
                .then(() => {
                    if (selectedDistrict && villageSelect) {
                        return loadOptions(`${API_BASE}/villages/${selectedDistrict}.json`, villageSelect, '{{ __('messages.owner.outlet.all_outlets.choose_village') }}', selectedVillage);
                    }
                });

            // Province Change Handler
            provinceSelect.addEventListener('change', function() {
                fillHiddenName(provinceSelect, provinceNameInput);
                resetSelect(citySelect, '{{ __('messages.owner.outlet.all_outlets.choose_city') }}');
                resetSelect(districtSelect, '{{ __('messages.owner.outlet.all_outlets.choose_district') }}');
                resetSelect(villageSelect, '{{ __('messages.owner.outlet.all_outlets.choose_village') }}');
                if (citySelect) citySelect.disabled = true;
                if (districtSelect) districtSelect.disabled = true;
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(`${API_BASE}/regencies/${this.value}.json`, citySelect, '{{ __('messages.owner.outlet.all_outlets.choose_city') }}');
                }
            });

            // City Change Handler
            citySelect?.addEventListener('change', function() {
                fillHiddenName(citySelect, cityNameInput);
                resetSelect(districtSelect, '{{ __('messages.owner.outlet.all_outlets.choose_district') }}');
                resetSelect(villageSelect, '{{ __('messages.owner.outlet.all_outlets.choose_village') }}');
                if (districtSelect) districtSelect.disabled = true;
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(`${API_BASE}/districts/${this.value}.json`, districtSelect, '{{ __('messages.owner.outlet.all_outlets.choose_district') }}');
                }
            });

            // District Change Handler
            districtSelect?.addEventListener('change', function() {
                fillHiddenName(districtSelect, districtNameInput);
                resetSelect(villageSelect, '{{ __('messages.owner.outlet.all_outlets.choose_village') }}');
                if (villageSelect) villageSelect.disabled = true;

                if (this.value) {
                    loadOptions(`${API_BASE}/villages/${this.value}.json`, villageSelect, '{{ __('messages.owner.outlet.all_outlets.choose_village') }}');
                }
            });

            // Village Change Handler
            villageSelect?.addEventListener('change', function() {
                fillHiddenName(villageSelect, villageNameInput);
            });
        }

        // ==== Form Submission ====
        const employeeForm = document.getElementById('employeeForm');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');

        // Cancel button
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                let hasChanges = false;
                const nameInput = document.querySelector('input[name="name"]');
                const userInput = document.querySelector('input[name="username"]');
                const emailInput = document.querySelector('input[name="email"]');

                if ((nameInput && nameInput.value !== nameInput.defaultValue) ||
                    (userInput && userInput.value !== userInput.defaultValue) ||
                    (emailInput && emailInput.value !== emailInput.defaultValue)) {
                    hasChanges = true;
                }

                const confirmMsg = hasChanges ?
                    '{{ __('messages.owner.outlet.all_outlets.js_unsaved_changes') }}' :
                    '{{ __('messages.owner.outlet.all_outlets.are_you_sure_cancel') }}';

                if (confirm(confirmMsg)) {
                    window.location.href = cancelBtn.dataset.redirectUrl || '/';
                }
            });
        }

        // Form submission handler
        if (employeeForm && saveBtn) {
            employeeForm.addEventListener('submit', function(e) {
                if (saveBtn.classList.contains('loading')) {
                    e.preventDefault();
                    return;
                }

                saveBtn.classList.add('loading');
                saveBtn.disabled = true;
                const btnLabel = saveBtn.querySelector('.btn-label');
                if (btnLabel) {
                    btnLabel.textContent = '{{ __('messages.owner.outlet.all_outlets.js_saving') }}';
                } else {
                    saveBtn.innerHTML = '<span class="material-symbols-outlined spinner-border spinner-border-sm"></span> {{ __('messages.owner.outlet.all_outlets.js_saving') }}';
                }

                // Validate required fields
                const requiredFields = employeeForm.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                // Validate password match (only if password is being changed)
                const password = document.getElementById('password')?.value || '';
                const passwordConfirm = document.getElementById('password_confirmation')?.value || '';

                if (password && password !== passwordConfirm) {
                    alert('{{ __('messages.owner.outlet.all_outlets.js_password_mismatch') }}');
                    isValid = false;
                }

                if (!isValid) {
                    const firstError = employeeForm.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }

                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    if (btnLabel) {
                        btnLabel.textContent = '{{ __('messages.owner.outlet.all_outlets.save_changes') }}';
                    } else {
                        saveBtn.innerHTML = '<span class="material-symbols-outlined">save</span> {{ __('messages.owner.outlet.all_outlets.save_changes') }}';
                    }

                    e.preventDefault();
                    return;
                }
            });

            // Reset button on page reload
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    const btnLabel = saveBtn.querySelector('.btn-label');
                    if (btnLabel) {
                        btnLabel.textContent = '{{ __('messages.owner.outlet.all_outlets.save_changes') }}';
                    } else {
                        saveBtn.innerHTML = '<span class="material-symbols-outlined">save</span> {{ __('messages.owner.outlet.all_outlets.save_changes') }}';
                    }
                }
            });

            // Prevent accidental enter submission
            employeeForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
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
        }
    });
    </script>
@endpush