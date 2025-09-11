@extends('layouts.admin')

@section('title', 'Create Product')
@section('page_title', 'Create New Product')

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
                        <div class="card-tools">

                        </div>
                    </div>

                    <form action="{{ route('admin.products.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <!-- Basic Product Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Slug</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ url('/price/') }}/</span>
                                            </div>
                                            <input type="text" name="slug" id="slug-input" class="form-control" placeholder="product-slug" required>
                                        </div>
                                        <small class="form-text text-muted">Only lowercase letters, numbers, and hyphens (-)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control summernote" rows="3" placeholder="Enter detailed product description"></textarea>
                            </div>

                            <hr>

                            <!-- Packages Grid -->
                            <h4 class="mb-3">Packages</h4>
                            <div class="row">
                                @foreach (['Lite', 'Premium', 'Business', 'Custom'] as $type)
                                <div class="col-md-6 col-lg-3">
                                    <div class="card card-outline card-info h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ $type }} Package</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="packages[{{ $loop->index }}][name]" value="{{ $type }}">

                                            <div class="form-group">
                                                <label>Monthly Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="number" name="packages[{{ $loop->index }}][monthly_price]"
                                                           class="form-control" placeholder="0.00" min="0" step="0.01">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Annual Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp.</span>
                                                    </div>
                                                    <input type="number" name="packages[{{ $loop->index }}][annual_price]"
                                                           class="form-control" placeholder="0.00" min="0" step="0.01">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="packages[{{ $loop->index }}][description]"
                                                          class="form-control" rows="2" placeholder="Package features"></textarea>
                                            </div>

                                            <div class="specifications mb-3">
                                                <h6 class="text-muted">Specifications</h6>
                                                <div class="spec-list" data-package-index="{{ $loop->index }}"></div>
                                                <button type="button" class="btn btn-sm btn-outline-primary add-spec" data-package-index="{{ $loop->index }}">
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
                            <button type="reset" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Create Product
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
        // Initialize Summernote editor
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

        // Auto-generate slug from product name
        $('input[name="name"]').on('keyup', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove non-word chars
                .replace(/\s+/g, '-')      // Replace spaces with -
                .replace(/--+/g, '-');     // Replace multiple - with single -

            $('#slug-input').val(slug);
        });

        // Make package cards collapsible
        $('.card-outline').on('click', '[data-card-widget="collapse"]', function() {
            $(this).find('i').toggleClass('fa-minus fa-plus');
        });
    });
</script>
<script>
$(document).ready(function () {
    // ...summernote dan slug sudah ada...

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

    // Remove row
    $(document).on('click', '.remove-spec', function () {
        $(this).closest('.form-row').remove();
    });
});
</script>

<script>
    const specifications = @json($specifications);
</script>

@endsection
