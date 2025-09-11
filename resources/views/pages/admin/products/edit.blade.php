@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Products
                </a>

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Product Information</h3>
                    </div>

                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <!-- Basic Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name', $product->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Slug</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ url('/price/') }}/</span>
                                            </div>
                                            <input type="text" name="slug" id="slug-input" class="form-control"
                                                   value="{{ old('slug', $product->slug) }}" required>
                                        </div>
                                        <small class="form-text text-muted">Only lowercase letters, numbers, and hyphens (-)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control summernote" rows="3">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <hr>

                            <!-- Packages -->
                            <h4 class="mb-3">Packages</h4>
                            <div class="row">
                                @foreach ($product->packages as $index => $package)
                                <div class="col-md-6 col-lg-3">
                                    <div class="card card-outline card-info h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ $package->name }} Package</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="packages[{{ $index }}][name]" value="{{ $package->name }}">

                                            <div class="form-group">
                                                <label>Monthly Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="number" name="packages[{{ $index }}][monthly_price]"
                                                           class="form-control" placeholder="0.00" min="0" step="0.01"
                                                           value="{{ old("packages.$index.monthly_price", $package->monthly_price) }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Annual Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="number" name="packages[{{ $index }}][annual_price]"
                                                           class="form-control" placeholder="0.00" min="0" step="0.01"
                                                           value="{{ old("packages.$index.annual_price", $package->annual_price) }}">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="packages[{{ $index }}][description]"
                                                          class="form-control" rows="2" placeholder="Package features">{{ old("packages.$index.description", $package->description) }}</textarea>
                                            </div>

                                            <div class="specifications mb-3">
                                                <h6 class="text-muted">Specifications</h6>
                                                <div class="spec-list" data-package-index="{{ $index }}">
                                                    @foreach ($package->specificationValues as $sIndex => $specVal)
                                                    <div class="form-row align-items-center mb-2">
                                                        <div class="col-6">
                                                            <select class="form-control form-control-sm" name="packages[{{ $index }}][specifications][{{ $sIndex }}][id]" required>
                                                                <option value="">-- Select Specification --</option>
                                                                @foreach ($specifications as $spec)
                                                                <option value="{{ $spec->id }}"
                                                                    {{ $spec->id == $specVal->specification_id ? 'selected' : '' }}>
                                                                    {{ $spec->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-5">
                                                            <input type="text" name="packages[{{ $index }}][specifications][{{ $sIndex }}][value]"
                                                                   class="form-control form-control-sm"
                                                                   value="{{ $specVal->value }}" placeholder="Value" required>
                                                        </div>
                                                        <div class="col-1">
                                                            <button type="button" class="btn btn-sm btn-danger remove-spec">&times;</button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-spec" data-package-index="{{ $index }}">
                                                    <i class="fas fa-plus mr-1"></i>Add Specification
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('input[name="name"]').on('keyup', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-');
            $('#slug-input').val(slug);
        });

        $('.card-outline').on('click', '[data-card-widget="collapse"]', function() {
            $(this).find('i').toggleClass('fa-minus fa-plus');
        });
    });
</script>

<script>
    const specifications = @json($specifications);

    $(document).ready(function () {
        $('.add-spec').on('click', function () {
            const index = $(this).data('package-index');
            const container = $(`.spec-list[data-package-index="${index}"]`);
            const specIndex = container.children().length;

            const specSelectName = `packages[${index}][specifications][${specIndex}][id]`;
            const specValueName = `packages[${index}][specifications][${specIndex}][value]`;

            let options = '<option value="">-- Select Specification --</option>';
            specifications.forEach(spec => {
                options += `<option value="${spec.id}">${spec.name}</option>`;
            });

            const specRow = `
                <div class="form-row align-items-center mb-2">
                    <div class="col-6">
                        <select class="form-control form-control-sm" name="${specSelectName}" required>
                            ${options}
                        </select>
                    </div>
                    <div class="col-5">
                        <input type="text" name="${specValueName}" class="form-control form-control-sm" placeholder="Value" required>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-sm btn-danger remove-spec">&times;</button>
                    </div>
                </div>
            `;
            container.append(specRow);
        });

        $(document).on('click', '.remove-spec', function () {
            $(this).closest('.form-row').remove();
        });
    });
</script>
@endsection
