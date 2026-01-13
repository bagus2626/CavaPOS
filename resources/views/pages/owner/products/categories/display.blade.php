<!-- Table Card -->
<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('messages.owner.products.categories.category_name') }}</th>
                    <th class="text-center" style="width: 150px;">{{ __('messages.owner.products.categories.picture') }}
                    </th>
                    <th>{{ __('messages.owner.products.categories.description') }}</th>
                    <th class="text-center" style="width: 180px;">{{ __('messages.owner.products.categories.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                @forelse($categories as $index => $category)
                    <tr class="table-row">
                        <!-- Number -->
                        <td class="text-center text-muted">
                            {{ $categories->firstItem() + $index }}
                        </td>

                        <!-- Category Name -->
                        <td>
                            <span class="fw-600">{{ $category->category_name }}</span>
                        </td>

                        <!-- Picture -->
                        <td class="text-center">
                            @if($category->images && isset($category->images['path']))
                                <a href="#" data-toggle="modal" data-target="#imageModal{{ $category->id }}">
                                    <img src="{{ asset($category->images['path']) }}" alt="{{ $category->category_name }}"
                                        class="table-image" loading="lazy">
                                </a>
                                @include('pages.owner.products.categories.modal')
                            @else
                                <span class="text-muted" style="font-size: 0.875rem;">
                                    {{ __('messages.owner.products.categories.no_pictures_yet') }}
                                </span>
                            @endif
                        </td>

                        <!-- Description -->
                        <td>
                            <span class="text-secondary">{{ $category->description }}</span>
                        </td>


                        <!-- Actions -->
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.categories.edit', $category) }}"
                                    class="btn-table-action edit"
                                    title="{{ __('messages.owner.products.categories.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('owner.user-owner.categories.destroy', $category) }}" method="POST"
                                    class="d-inline js-delete-form" data-name="{{ $category->category_name }}"
                                    style="display: inline;">
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
                                <span class="material-symbols-outlined">category_off</span>
                                <h4>{{ __('messages.owner.products.categories.no_categories') ?? 'No categories found' }}
                                </h4>
                                <p>{{ __('messages.owner.products.categories.add_first_category') ?? 'Add your first category to get started' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="table-pagination">
            {{ $categories->links() }}
        </div>
    @endif
</div>