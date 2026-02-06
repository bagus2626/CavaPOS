<!-- Table Card -->
<div class="modern-card category-responsive">

    {{-- =======================
      DESKTOP: TABLE (Tetap Blade biar modal gambar aman)
    ======================= --}}
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
                {{-- ✅ KODE LAMA TETAP (jangan dihapus) --}}
                @forelse($categories as $index => $category)
                    <tr class="table-row">
                        <td class="text-center text-muted">
                            {{ $categories->firstItem() + $index }}
                        </td>

                        <td>
                            <span class="fw-600">{{ $category->category_name }}</span>
                        </td>

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

                        <td>
                            <span class="text-secondary">{{ $category->description }}</span>
                        </td>

                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.categories.edit', $category) }}"
                                   class="btn-table-action edit"
                                   title="{{ __('messages.owner.products.categories.edit') }}">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>

                                <form action="{{ route('owner.user-owner.categories.destroy', $category) }}" method="POST"
                                      class="d-inline js-delete-form"
                                      data-name="{{ $category->category_name }}"
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
                                <h4>{{ __('messages.owner.products.categories.no_categories') ?? 'No categories found' }}</h4>
                                <p>{{ __('messages.owner.products.categories.add_first_category') ?? 'Add your first category to get started' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- =======================
      MOBILE: CARDS (JS render)
    ======================= --}}
    <div class="only-mobile mobile-category-list" id="categoryMobileList">
        {{-- di-render via JS --}}
    </div>

    {{-- Pagination (Laravel) - tetap ada tapi akan disembunyikan saat JS aktif --}}
    @if($categories->hasPages())
        <div class="table-pagination" id="categoryLaravelPagination">
            {{ $categories->links() }}
        </div>
    @endif

    {{-- Pagination (JS) --}}
    <div class="table-pagination" id="categoryPagination"></div>
</div>

<style>
  .category-responsive .only-desktop{ display:block; }
  .category-responsive .only-mobile{ display:none; }

  @media (max-width: 768px){
    .category-responsive .only-desktop{ display:none; }
    .category-responsive .only-mobile{ display:block; }
  }

  /* MOBILE CARDS */
  .mobile-category-list{
    padding: 14px;
    display: grid;
    gap: 12px;
  }
  .category-card{
    border: 1px solid rgba(0,0,0,.08);
    background: #fff;
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
    margin-bottom: 5px;
  }
  .category-card__top{
    display:flex;
    gap: 12px;
    align-items:flex-start;
  }
  .category-card__thumb{
    width: 54px;
    height: 54px;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    flex: 0 0 auto;
    background: rgba(0,0,0,.04);
    display:flex;
    align-items:center;
    justify-content:center;
  }
  .category-card__thumb img{
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .category-card__meta{ flex:1; min-width:0; }
  .category-card__name{
    font-weight: 900;
    font-size: 14px;
    line-height: 1.25;
  }
  .category-card__desc{
    margin-top: 6px;
    font-size: 12px;
    color: #666;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .category-card__bottom{
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed rgba(0,0,0,.10);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap: 10px;
  }
  .category-card__badge{
    display:inline-flex;
    align-items:center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.04);
    font-size: 12px;
    color: #555;
  }
  .category-card__actions{
    display:flex;
    gap: 8px;
  }
  .btn-card-action{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 6px;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.10);
    background:#fff;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
  }
  .btn-card-action.danger{
    border-color: rgba(174,21,4,.25);
    color: #ae1504;
  }

  /* pagination center */
  #categoryPagination{
    display:flex;
    justify-content:center;
    padding: 14px 10px;
  }
  #categoryPagination .pagination{ margin: 0; }
</style>
