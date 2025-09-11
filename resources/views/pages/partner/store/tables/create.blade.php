@extends('layouts.partner')

@section('title', 'Create Tables')
@section('page_title', 'Create New Table')

@section('content')
<div class="container">
    <a href="{{ route('partner.store.tables.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left mr-2"></i>Back to Tables
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Create New Table</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('partner.store.tables.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Table No --}}
                    <div class="col-md-6 mb-3">
                        <label for="table_no" class="form-label">Table No</label>
                        <input type="text" name="table_no" id="table_no"
                               class="form-control @error('table_no') is-invalid @enderror"
                               value="{{ old('table_no') }}" required>
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
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
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
                                value="{{ old('table_class') }}"
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
                        <small class="text-muted">Anda bisa upload hingga 3 gambar.</small>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('partner.store.tables.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableClassSelect = document.getElementById("table_class");
        const newTableClassInput = document.getElementById("new_table_class");

        if (tableClassSelect) {
            tableClassSelect.addEventListener("change", function () {
                if (this.value === "__new") {
                    newTableClassInput.classList.remove("d-none");
                    newTableClassInput.required = true;
                } else {
                    newTableClassInput.classList.add("d-none");
                    newTableClassInput.required = false;
                }
            });
        }
    });
</script>
@endpush
