@php
  use Illuminate\Support\Str;
@endphp

<div class="modern-card outlet-responsive">

  {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
  <div class="data-table-wrapper only-desktop">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.owner.outlet.all_outlets.outlet_name') }}</th>
          <th>{{ __('messages.owner.outlet.all_outlets.username') }}</th>
          <th>{{ __('messages.owner.outlet.all_outlets.email') }}</th>
          <th>Address</th>
          <th class="text-center">Status</th>
          <th class="text-center" style="width: 180px;">
            {{ __('messages.owner.outlet.all_outlets.actions') }}
          </th>
        </tr>
      </thead>

      <tbody id="outletTableBody">
        {{-- ISI AWAL AKAN DI-RENDER JS (renderTable()) --}}
        @forelse ($outlets as $index => $outlet)
          @php
            $img = $outlet->logo
              ? (Str::startsWith($outlet->logo, ['http://', 'https://'])
                  ? $outlet->logo
                  : asset('storage/' . $outlet->logo))
              : null;
            $isActive = (int) $outlet->is_active === 1;
          @endphp

          <tr data-status="{{ $isActive ? 'active' : 'inactive' }}" class="table-row">
            <td class="text-center text-muted">{{ $outlets->firstItem() + $index }}</td>

            <td>
              <div class="user-info-cell">
                @if($img)
                  <img src="{{ $img }}" alt="{{ $outlet->name }}" class="user-avatar" loading="lazy">
                @else
                  <div class="user-avatar-placeholder">
                    <span class="material-symbols-outlined">store</span>
                  </div>
                @endif
                <span class="data-name">{{ $outlet->name }}</span>
              </div>
            </td>

            <td><span class="text-secondary">{{ $outlet->username }}</span></td>

            <td>
              <a href="mailto:{{ $outlet->email }}" class="table-link">
                {{ $outlet->email }}
              </a>
            </td>

            <td><span class="text-secondary">{{ $outlet->city }}</span></td>

            <td class="text-center">
              @if($isActive)
                <span class="badge-modern badge-success badge-sm">
                  {{ __('messages.owner.outlet.all_outlets.active') }}
                </span>
              @else
                <span class="badge-modern badge-danger badge-sm">
                  {{ __('messages.owner.outlet.all_outlets.inactive') }}
                </span>
              @endif
            </td>

            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('owner.user-owner.outlets.show', $outlet->id) }}"
                   class="btn-table-action view"
                   title="{{ __('messages.owner.outlet.all_outlets.view_details') ?? 'View Details' }}">
                  <span class="material-symbols-outlined">visibility</span>
                </a>

                <a href="{{ route('owner.user-owner.outlets.edit', $outlet->id) }}"
                   class="btn-table-action edit"
                   title="{{ __('messages.owner.outlet.all_outlets.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>

                <button type="button"
                        onclick="deleteOutlet({{ $outlet->id }})"
                        class="btn-table-action delete"
                        title="{{ __('messages.owner.outlet.all_outlets.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">store_off</span>
                <h4>{{ __('messages.owner.outlet.all_outlets.no_outlets') ?? 'No outlets found' }}</h4>
                <p>{{ __('messages.owner.outlet.all_outlets.add_first_outlet') ?? 'Add your first outlet to get started' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- =======================
    MOBILE: CARDS (akan diisi JS juga)
  ======================= --}}
  <div class="only-mobile mobile-outlet-list" id="outletCardList">
  </div>

  <div class="table-pagination"></div>
</div>

<style>
  .outlet-responsive .only-desktop { display: block !important; }
  .outlet-responsive .only-mobile  { display: none !important; }

  @media (max-width: 768px) {
    .outlet-responsive .only-desktop { display: none !important; }
    .outlet-responsive .only-mobile  { display: block !important; }
  }

  .mobile-outlet-list{
    padding: 14px;
    display: grid;
    gap: 12px;
  }

  .outlet-card{
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 16px;
        background: #fff;
        padding: 14px;
        box-shadow: 0 8px 22px rgba(0,0,0,.06);
        margin-bottom: 5px;
    }


  .outlet-card__top{
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }

  .outlet-card__avatar img{
    width: 46px;
    height: 46px;
    border-radius: 12px;
    object-fit: cover;
  }
  .outlet-card__avatar .user-avatar-placeholder{
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: rgba(0,0,0,.05);
    }

    .outlet-card__chips{
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .outlet-chip{
        max-width: 100%;
    }
    .outlet-chip .chip-text{
        display: inline-block;
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .outlet-card__status{
        flex: 0 0 auto;
        margin-left: 8px;
    }

  .outlet-card__meta{ flex: 1; min-width: 0; }

  .outlet-card__name{
    font-weight: 800;
    font-size: 15px;
    line-height: 1.2;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .outlet-chip{
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.04);
    font-size: 12px;
    color: #555;
    margin-right: 8px;
    margin-bottom: 8px;
  }

  .outlet-chip .material-symbols-outlined{ font-size: 16px; opacity: .8; }

  .outlet-card__info{
    margin-top: 10px;
    border-top: 1px dashed rgba(0,0,0,.08);
    padding-top: 10px;
    display: grid;
    gap: 8px;
  }

  .outlet-info-row{
    display:flex;
    justify-content:space-between;
    gap:12px;
  }

  .outlet-info-row .label{ color:#888; font-size:12px; }
  .outlet-info-row .value{ color:#333; font-size:12px; text-align:right; word-break: break-word; }
  .outlet-info-row .value.link{ text-decoration: underline; color: inherit; }

  .outlet-card__actions{
    margin-top: 12px;
    display:flex;
    gap:8px;
  }

  .btn-card-action{
    flex:1;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    padding:10px;
    border-radius:12px;
    border:1px solid rgba(0,0,0,.10);
    background:#fff;
    font-size:12px;
    font-weight:700;
  }

  .btn-card-action .material-symbols-outlined{ font-size:18px; }
  .btn-card-action.danger{ border-color: rgba(174,21,4,.25); color:#ae1504; }
</style>
