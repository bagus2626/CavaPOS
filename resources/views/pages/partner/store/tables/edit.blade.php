@extends('layouts.partner')

@section('title', 'Edit Table')
@section('page_title', 'Edit Table')

@section('content')
<div class="container">
    <a href="{{ route('partner.store.tables.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>Back to Tables
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Table</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('partner.store.tables.update', $table->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Table No --}}
                    <div class="col-md-6 mb-3">
                        <label for="table_no" class="form-label">Table No</label>
                        <input type="text" name="table_no" id="table_no"
                               class="form-control @error('table_no') is-invalid @enderror"
                               value="{{ old('table_no', $table->table_no) }}" required>
                        @error('table_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Table Class --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="table_class" class="form-label">Table Class</label>
                            <input list="table_class_list"
                                name="table_class"
                                id="table_class"
                                class="form-control @error('table_class') is-invalid @enderror"
                                value="{{ old('table_class', $table->table_class) }}"
                                placeholder="Ketik atau pilih class..." required>

                            <datalist id="table_class_list">
                                @if(isset($table_classes) && count($table_classes) > 0)
                                    @foreach($table_classes as $class)
                                        <option value="{{ $class }}"></option>
                                    @endforeach
                                @endif
                            </datalist>

                            @error('table_class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="col-md-6 mb-3">
                        <label for="images" class="form-label">Upload Images (max 3)</label>
                        <input type="file" name="images[]" id="images"
                               class="form-control @error('images') is-invalid @enderror"
                               accept="image/*" multiple>
                        <small class="text-muted">Upload baru akan menimpa gambar lama (max 3).</small>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Preview Images --}}
                        @if($table->images && count($table->images) > 0)
                            <div class="mt-2">
                                <label class="form-label">Current Images:</label>
                                <div class="d-flex flex-wrap">
                                    @foreach($table->images as $index => $img)
                                        <div class="me-2 mb-2 text-center">
                                            <img src="{{ asset($img['path']) }}"
                                                alt="Table Image"
                                                class="img-thumbnail"
                                                style="max-width: 120px; height: 120px; object-fit: cover;">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox"
                                                    name="delete_images[]"
                                                    value="{{ $img['filename'] }}"
                                                    id="delete_image_{{ $index }}">
                                                <label class="form-check-label small" for="delete_image_{{ $index }}">
                                                    Hapus
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $table->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('partner.store.tables.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
