<!-- Modal: Category Order -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.owner.products.categories.reorder_categories') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <p class="text-muted mb-2">{{ __('messages.owner.products.categories.drag_drop_instruction') }}</p>

                <ul id="sortableCategoryList" class="list-group">
                    @foreach(($allCategories ?? collect())->sortBy('category_order') as $c)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            data-id="{{ $c->id }}">
                        <span>{{ $c->category_name }}</span>
                        <i class="fas fa-bars text-secondary sort-handle"></i>
                        </li>
                    @endforeach
                </ul>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">{{ __('messages.owner.products.categories.close') }}</button>

                <button id="saveOrderBtn" class="btn btn-primary">
                    {{ __('messages.owner.products.categories.save_order') }}
                </button>
            </div>

        </div>
    </div>
</div>
