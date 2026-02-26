@php
    use Illuminate\Support\Str;
    $empRole = strtolower(auth('employee')->user()->role ?? 'manager');
@endphp

<link rel="stylesheet" href="{{ asset('css/mobile-owner.css') }}">

{{-- Mobile Header Section - Mobile Only --}}
<div class="only-mobile mobile-header-section">
    <div class="mobile-header-card">
        <div class="mobile-header-content">
            <div class="mobile-header-left">
                <h2 class="mobile-header-title">{{ __('messages.owner.products.categories.categories') }}</h2>
                <p class="mobile-header-subtitle">{{ $categories->total() }} Total Categories</p>
            </div>
            <div class="mobile-header-right">
                <div class="mobile-header-avatar-placeholder">
                    <span class="material-symbols-outlined">category</span>
                </div>
            </div>
        </div>

        <div class="mobile-search-wrapper">
            <div class="mobile-search-box">
                <span class="mobile-search-icon">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input type="text" id="searchInputMobile" class="mobile-search-input" value="{{ request('q') }}"
                    placeholder="{{ __('messages.owner.products.categories.search_placeholder') }}">
                <button class="mobile-filter-btn" data-toggle="modal" data-target="#orderModal">
                    <span class="material-symbols-outlined">swap_vert</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="modern-card category-responsive">

    {{-- DESKTOP: TABLE --}}
    <div class="data-table-wrapper only-desktop">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.products.categories.category_name') }}</th>
                    <th class="text-center" style="width: 150px;">{{ __('messages.owner.products.categories.picture') }}</th>
                    <th>{{ __('messages.owner.products.categories.description') }}</th>
                    <th class="text-center" style="width: 180px;">{{ __('messages.owner.products.categories.actions') }}</th>
                </tr>
            </thead>

            <tbody id="categoryTableBody">
                @forelse($categories as $index => $category)
                    <tr class="table-row">
                        <td class="text-center text-muted">
                            {{ $categories->firstItem() + $index }}
                        </td>
                        <td>
                            <span class="fw-600">{{ $category->category_name }}</span>
                        </td>
                        <td class="text-center">
                            @if ($category->images && isset($category->images['path']))
                                <a href="#" data-toggle="modal" data-target="#imageModal{{ $category->id }}">
                                    <img src="{{ asset($category->images['path']) }}"
                                        alt="{{ $category->category_name }}" class="table-image" loading="lazy">
                                </a>
                                @include('pages.employee.staff.products.categories.modal')
                            @else
                                <span class="text-muted" style="font-size: 0.875rem;">
                                    {{ __('messages.owner.products.categories.no_pictures_yet') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-secondary">{{ $category->description }}</span>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route("employee.{$empRole}.categories.edit", $category) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.products.categories.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route("employee.{$empRole}.categories.destroy", $category) }}"
                                    method="POST" class="d-inline js-delete-form"
                                    data-name="{{ $category->category_name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-table-action delete"
                                        title="{{ __('messages.owner.products.categories.delete') }}">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">category</span>
                                <h4>{{ __('messages.owner.products.categories.no_categories') ?? 'No categories found' }}</h4>
                                <p>{{ __('messages.owner.products.categories.add_first_category') ?? 'Add your first category to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE: 2-COLUMN GRID --}}
    <div class="only-mobile mobile-category-list-v2">
        <div class="category-grid-v2">
            @forelse ($categories as $category)
                <div class="category-card-v2">
                    <div class="card-image-header">
                        @if ($category->images && isset($category->images['path']))
                            <img src="{{ asset($category->images['path']) }}" alt="{{ $category->category_name }}" loading="lazy">
                        @else
                            <div class="image-placeholder-v2">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body-v2">
                        <h3 class="category-title-v2">{{ $category->category_name }}</h3>
                        @if ($category->description)
                            <div class="description-v2">
                                <p>{{ $category->description }}</p>
                            </div>
                        @endif
                        <div class="action-buttons-v2">
                            <a href="{{ route("employee.{$empRole}.categories.edit", $category) }}" class="btn-v2 btn-edit">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <form action="{{ route("employee.{$empRole}.categories.destroy", $category) }}" method="POST"
                                class="js-delete-form" data-name="{{ $category->category_name }}"
                                style="display: inline; width: 100%;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-v2 btn-delete">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="table-empty-state" style="padding: 24px; grid-column: 1 / -1;">
                    <span class="material-symbols-outlined">category</span>
                    <h4>{{ __('messages.owner.products.categories.no_categories') ?? 'No categories found' }}</h4>
                    <p>{{ __('messages.owner.products.categories.add_first_category') ?? 'Add your first category to get started' }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($categories->hasPages())
        <div class="table-pagination">
            {{ $categories->links() }}
        </div>
    @endif
</div>

{{-- Floating Add Button (Mobile Only) --}}
<a href="{{ route("employee.{$empRole}.categories.create") }}" class="btn-add-employee-mobile">
    <span class="material-symbols-outlined">add</span>
</a>

<style>
    .category-responsive .only-desktop { display: block; }
    .category-responsive .only-mobile  { display: none; }

    @media (max-width: 768px) {
        .category-responsive .only-desktop { display: none; }
        .category-responsive .only-mobile  { display: block; }
    }

    .mobile-category-list-v2 { padding: 12px; }
    .category-grid-v2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .category-card-v2 { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); transition: transform .2s; }
    .category-card-v2:active { transform: scale(.98); }
    .card-image-header { position: relative; width: 100%; height: 120px; background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); overflow: hidden; }
    .card-image-header img { width: 100%; height: 100%; object-fit: cover; }
    .image-placeholder-v2 { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; }
    .image-placeholder-v2 .material-symbols-outlined { font-size: 48px; }
    .card-body-v2 { padding: 10px; }
    .category-title-v2 { font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 8px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px; }
    .description-v2 { margin-bottom: 10px; }
    .description-v2 p { font-size: 11px; color: #6b7280; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .action-buttons-v2 { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; padding-top: 8px; border-top: 1px solid #f3f4f6; }
    .btn-v2 { display: flex; align-items: center; justify-content: center; padding: 8px; border-radius: 8px; border: none; cursor: pointer; transition: all .2s; text-decoration: none; width: 100%; }
    .btn-v2 .material-symbols-outlined { font-size: 18px; }
    .btn-edit { background: #eff6ff; color: #1e40af; }
    .btn-edit:active { background: #dbeafe; }
    .btn-delete { background: #fef2f2; color: #991b1b; }
    .btn-delete:active { background: #fee2e2; }
</style>