@extends('layouts.partner')

@section('title', __('messages.partner.outlet.table_management.tables.edit_table'))
@section('page_title', __('messages.partner.outlet.table_management.tables.edit_table'))

@section('content')
  <div class="modern-container">
    <div class="container-modern">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">{{ __('messages.partner.outlet.table_management.tables.edit_table') }}</h1>
          <p class="page-subtitle">{{ __('messages.partner.outlet.table_management.tables.update_table_information') }}</p>
        </div>
      </div>

      <!-- Error Messages -->
      @if ($errors->any())
        <div class="alert alert-danger alert-modern">
          <div class="alert-icon">
            <span class="material-symbols-outlined">error</span>
          </div>
          <div class="alert-content">
            <strong>{{ __('messages.partner.outlet.table_management.tables.re_check_input') }}</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <!-- Main Card -->
      <div class="modern-card">
        <form action="{{ route('partner.store.tables.update', $table->id) }}" method="POST"
          enctype="multipart/form-data" id="tableEditForm">
          @csrf
          @method('PUT')

          <div class="card-body-modern">

            <!-- Table Image & Basic Info Section -->
            <div class="profile-section">
              <!-- Table Image Upload (Single Image) -->
              <div class="profile-picture-wrapper">
                <div class="profile-picture-container" id="tableImageContainer">
                  @php
                    $existingImage = is_array($table->images) ? ($table->images[0]['path'] ?? null) : null;
                  @endphp
                  <div class="upload-placeholder" id="uploadPlaceholder"
                    style="{{ $existingImage ? 'display:none;' : '' }}">
                    <span class="material-symbols-outlined">image</span>
                    <span class="upload-text">Upload</span>
                  </div>
                  <img id="imagePreview" class="profile-preview {{ $existingImage ? 'active' : '' }}"
                    src="{{ $existingImage ? asset($existingImage) : '' }}" alt="Table Preview">
                </div>
                <input type="file" name="images" id="tableImage" accept="image/*" style="display: none;">
                <input type="hidden" name="keep_existing_image" id="keep_existing_image" value="1">

                <small class="text-muted d-block text-center mt-2">JPG, PNG, WEBP. Max 2 MB</small>
                @error('images')
                  <div class="text-danger text-center mt-1 small">{{ $message }}</div>
                @enderror
              </div>

              <!-- Basic Table Information -->
              <div class="personal-info-fields">
                <div class="section-header">
                  <div class="section-icon section-icon-red">
                    <span class="material-symbols-outlined">table_restaurant</span>
                  </div>
                  <h3 class="section-title">
                    {{ __('messages.partner.outlet.table_management.tables.table_information') ?? 'Table Information' }}
                  </h3>
                </div>
                <div class="row g-4">
                  <!-- Table Number -->
                  <div class="col-md-6">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.partner.outlet.table_management.tables.table_no') }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" name="table_no" id="table_no"
                        class="form-control-modern @error('table_no') is-invalid @enderror"
                        value="{{ old('table_no', $table->table_no) }}" required>
                      @error('table_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <!-- Status -->
                  <div class="col-md-6">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.partner.outlet.table_management.tables.status') }}
                        <span class="text-danger">*</span>
                      </label>
                      <div class="select-wrapper">
                        <select name="status" id="status"
                          class="form-control-modern @error('status') is-invalid @enderror" required>
                          <option value="">
                            {{ __('messages.partner.outlet.table_management.tables.choose_status') }}
                          </option>
                          <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>
                            {{ __('messages.partner.outlet.table_management.tables.available') }}
                          </option>
                          <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>
                            {{ __('messages.partner.outlet.table_management.tables.occupied') }}
                          </option>
                          <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>
                            {{ __('messages.partner.outlet.table_management.tables.reserved') }}
                          </option>
                          <option value="not_available" {{ old('status', $table->status) == 'not_available' ? 'selected' : '' }}>
                            {{ __('messages.partner.outlet.table_management.tables.not_available') }}
                          </option>
                        </select>
                        <span class="material-symbols-outlined select-arrow">expand_more</span>
                      </div>
                      @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <!-- Table Class with Toggle -->
                  <div class="col-md-12">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.partner.outlet.table_management.tables.class_type') }}
                        <span class="text-danger">*</span>
                      </label>

                      {{-- SELECT MODE (default) --}}
                      <div id="select_mode">
                        <div class="select-wrapper">
                          <select name="table_class" id="table_class"
                            class="form-control-modern @error('table_class') is-invalid @enderror" required>
                            <option value="">
                              {{ __('messages.partner.outlet.table_management.tables.placeholder_1') }}
                            </option>
                            @if (!empty($table_classes) && count($table_classes) > 0)
                              @foreach ($table_classes as $class)
                                <option value="{{ $class }}"
                                  {{ old('table_class', $table->table_class) == $class ? 'selected' : '' }}>
                                  {{ $class }}
                                </option>
                              @endforeach
                            @endif
                          </select>
                          <span class="material-symbols-outlined select-arrow">expand_more</span>
                        </div>

                        @error('table_class')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        {{-- Button Add New Class --}}
                        <button type="button" class="btn-modern btn-primary-modern btn-sm-modern mt-3" id="btn_add_new_class">
                          <span class="material-symbols-outlined">add_circle</span>
                          <span>{{ __('messages.partner.outlet.table_management.tables.add_class') }}</span>
                        </button>
                      </div>

                      {{-- INPUT MODE (hidden by default) --}}
                      <div id="input_mode" style="display: none;">
                        <input type="text" name="new_table_class" id="new_table_class"
                          class="form-control-modern"
                          placeholder="{{ __('messages.partner.outlet.table_management.tables.placeholder_1') }}">
                        
                        <button type="button" class="btn-modern btn-secondary-modern btn-sm-modern mt-3"
                          id="cancel_new_class">
                          {{ __('messages.partner.outlet.table_management.tables.cancel') }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Description -->
                  <div class="col-12">
                    <div class="form-group-modern">
                      <label class="form-label-modern">
                        {{ __('messages.partner.outlet.table_management.tables.description') }}
                      </label>
                      <textarea name="description" id="description"
                        class="form-control-modern @error('description') is-invalid @enderror"
                        rows="4">{{ old('description', $table->description) }}</textarea>
                      @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Card Footer -->
          <div class="card-footer-modern">
            <button type="button" class="btn-cancel-modern"
              onclick="window.location.href='{{ route('partner.store.tables.index') }}'">
              {{ __('messages.partner.outlet.table_management.tables.cancel') }}
            </button>
            <button type="submit" class="btn-submit-modern">
              {{ __('messages.partner.outlet.table_management.tables.update') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // ==== Image Upload & Preview ====
      const imageContainer = document.getElementById('tableImageContainer');
      const imageInput = document.getElementById('tableImage');
      const imagePreview = document.getElementById('imagePreview');
      const uploadPlaceholder = document.getElementById('uploadPlaceholder');
      const keepInput = document.getElementById('keep_existing_image');

      // Click to upload
      if (imageContainer && imageInput) {
        imageContainer.addEventListener('click', function(e) {
          if (!e.target.closest('.btn-remove-image')) {
            imageInput.click();
          }
        });

        // Preview image
        imageInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
              alert('Please use JPG, PNG, or WEBP format.');
              this.value = '';
              return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
              alert('File size must not exceed 2 MB.');
              this.value = '';
              return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(event) {
              imagePreview.src = event.target.result;
              imagePreview.classList.add('active');
              uploadPlaceholder.style.display = 'none';
              if (keepInput) keepInput.value = '0';
            };
            reader.readAsDataURL(file);
          }
        });
      }

      // ==== Table Class Toggle Logic ====
      const selectMode = document.getElementById('select_mode');
      const inputMode = document.getElementById('input_mode');
      const selectClass = document.getElementById('table_class');
      const newClassInput = document.getElementById('new_table_class');
      const btnAddNewClass = document.getElementById('btn_add_new_class');
      const cancelBtn = document.getElementById('cancel_new_class');
      const form = document.getElementById('tableEditForm');

      let isInputMode = false;

      function switchToInputMode() {
        isInputMode = true;
        selectMode.style.display = 'none';
        inputMode.style.display = 'block';

        selectClass.required = false;
        newClassInput.required = true;

        setTimeout(() => {
          newClassInput.focus();
        }, 50);
      }

      function switchToSelectMode() {
        isInputMode = false;
        inputMode.style.display = 'none';
        selectMode.style.display = 'block';

        selectClass.required = true;
        newClassInput.required = false;
        newClassInput.value = '';

        // Restore original value
        selectClass.value = '{{ old('table_class', $table->table_class) }}';
      }

      if (btnAddNewClass) {
        btnAddNewClass.addEventListener('click', switchToInputMode);
      }

      if (cancelBtn) {
        cancelBtn.addEventListener('click', switchToSelectMode);
      }

      // Handle Form Submit
      if (form) {
        form.addEventListener('submit', function(e) {
          if (isInputMode) {
            const newClassName = newClassInput.value.trim();

            if (!newClassName) {
              e.preventDefault();
              alert('Please enter a new class name or click "Cancel"');
              newClassInput.focus();
              return false;
            }

            // Set the value to select element
            selectClass.value = newClassName;
            if (!selectClass.querySelector(`option[value="${newClassName}"]`)) {
              const newOption = new Option(newClassName, newClassName, true, true);
              selectClass.appendChild(newOption);
            }

            selectClass.required = true;
            newClassInput.required = false;
          }
        });
      }

      // Handle old input mode if validation fails
      const oldNewClass = '{{ old('new_table_class') }}';
      if (oldNewClass) {
        switchToInputMode();
        newClassInput.value = oldNewClass;
      }
    });
  </script>
@endpush