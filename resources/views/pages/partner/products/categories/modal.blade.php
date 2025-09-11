<!-- preview image Modal -->
<div class="modal fade" id="imageModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <button type="button" class="btn-close ms-auto me-2 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="{{ asset($category->images['path']) }}"
                alt="{{ $category->category_name }}"
                class="img-fluid rounded shadow">
        </div>
    </div>
</div>
