@php
  use Illuminate\Support\Str;
@endphp

<div class="modern-card">

  {{-- =======================
    DESKTOP: TABLE
  ======================= --}}
  <div class="data-table-wrapper only-desktop">
    <table class="data-table">
      <thead>
        <tr>
          <th class="text-center" style="width: 60px;">#</th>
          <th>{{ __('messages.owner.user_management.employees.employee_name') }}</th>
          <th>{{ __('messages.owner.user_management.employees.outlet') }}</th>
          <th>{{ __('messages.owner.user_management.employees.username') }}</th>
          <th>{{ __('messages.owner.user_management.employees.email') }}</th>
          <th>{{ __('messages.owner.user_management.employees.role') }}</th>
          <th class="text-center">{{ __('messages.owner.user_management.employees.status') }}</th>
          <th class="text-center" style="width: 180px;">
            {{ __('messages.owner.user_management.employees.actions') }}
          </th>
        </tr>
      </thead>

      <tbody id="employeeTableBody">
        @forelse ($employees as $index => $employee)
          @php
            $img = $employee->image
              ? (Str::startsWith($employee->image, ['http://', 'https://'])
                  ? $employee->image
                  : asset('storage/' . $employee->image))
              : null;
          @endphp

          <tr data-outlet="{{ $employee->partner_id }}" class="table-row">
            <td class="text-center text-muted">{{ $employees->firstItem() + $index }}</td>

            <td>
              <div class="user-info-cell">
                @if($img)
                  <img src="{{ $img }}" alt="{{ $employee->name }}" class="user-avatar" loading="lazy">
                @else
                  <div class="user-avatar-placeholder">
                    <span class="material-symbols-outlined">person</span>
                  </div>
                @endif
                <span class="data-name">{{ $employee->name }}</span>
              </div>
            </td>

            <td>
              <div class="cell-with-icon">
                <span class="fw-600">{{ $employee->partner->name ?? '-' }}</span>
              </div>
            </td>

            <td><span class="text-secondary">{{ $employee->user_name }}</span></td>

            <td>
              <a href="mailto:{{ $employee->email }}" class="table-link">
                {{ $employee->email }}
              </a>
            </td>

            <td>
              <span class="badge-modern badge-info">{{ $employee->role }}</span>
            </td>

            <td class="text-center">
              @if((int) $employee->is_active === 1)
                <span class="badge-modern badge-success">
                  {{ __('messages.owner.user_management.employees.active') }}
                </span>
              @else
                <span class="badge-modern badge-danger">
                  {{ __('messages.owner.user_management.employees.non_active') }}
                </span>
              @endif
            </td>

            <td class="text-center">
              <div class="table-actions">
                <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}"
                   class="btn-table-action view"
                   title="{{ __('messages.owner.user_management.employees.view_details') ?? 'View Details' }}">
                  <span class="material-symbols-outlined">visibility</span>
                </a>

                <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}"
                   class="btn-table-action edit"
                   title="{{ __('messages.owner.user_management.employees.edit') }}">
                  <span class="material-symbols-outlined">edit</span>
                </a>

                <button type="button"
                        onclick="deleteEmployee({{ $employee->id }})"
                        class="btn-table-action delete"
                        title="{{ __('messages.owner.user_management.employees.delete') }}">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">
              <div class="table-empty-state">
                <span class="material-symbols-outlined">person_off</span>
                <h4>{{ __('messages.owner.user_management.employees.no_employees') ?? 'No employees found' }}</h4>
                <p>{{ __('messages.owner.user_management.employees.add_first_employee') ?? 'Add your first employee to get started' }}</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- =======================
    MOBILE: CARDS
  ======================= --}}
  <div class="only-mobile mobile-employee-list">
    @forelse ($employees as $employee)
      @php
        $img = $employee->image
          ? (Str::startsWith($employee->image, ['http://', 'https://'])
              ? $employee->image
              : asset('storage/' . $employee->image))
          : null;
      @endphp

      <div class="employee-card">
        <div class="employee-card__top">
          <div class="employee-card__avatar">
            @if($img)
              <img src="{{ $img }}" alt="{{ $employee->name }}" loading="lazy">
            @else
              <div class="user-avatar-placeholder">
                <span class="material-symbols-outlined">person</span>
              </div>
            @endif
          </div>

          <div class="employee-card__meta">
            <div class="employee-card__name">{{ $employee->name }}</div>

            <div class="employee-card__sub">
              <span class="employee-chip">
                <span class="material-symbols-outlined">store</span>
                {{ $employee->partner->name ?? '-' }}
              </span>

              <span class="employee-chip">
                <span class="material-symbols-outlined">badge</span>
                {{ $employee->role ?? '-' }}
              </span>
            </div>
          </div>

          <div class="employee-card__status">
            @if((int) $employee->is_active === 1)
              <span class="badge-modern badge-success">{{ __('messages.owner.user_management.employees.active') }}</span>
            @else
              <span class="badge-modern badge-danger">{{ __('messages.owner.user_management.employees.non_active') }}</span>
            @endif
          </div>
        </div>

        <div class="employee-card__info">
          <div class="employee-info-row">
            <span class="label">{{ __('messages.owner.user_management.employees.username') }}</span>
            <span class="value">{{ $employee->user_name ?? '-' }}</span>
          </div>

          <div class="employee-info-row">
            <span class="label">{{ __('messages.owner.user_management.employees.email') }}</span>
            <a class="value link" href="mailto:{{ $employee->email }}">{{ $employee->email ?? '-' }}</a>
          </div>
        </div>

        <div class="employee-card__actions">
          <a href="{{ route('owner.user-owner.employees.show', $employee->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">visibility</span>
            <span>{{ __('messages.owner.user_management.employees.view_details') ?? 'View' }}</span>
          </a>

          <a href="{{ route('owner.user-owner.employees.edit', $employee->id) }}" class="btn-card-action">
            <span class="material-symbols-outlined">edit</span>
            <span>{{ __('messages.owner.user_management.employees.edit') }}</span>
          </a>

          <button type="button" class="btn-card-action danger" onclick="deleteEmployee({{ $employee->id }})">
            <span class="material-symbols-outlined">delete</span>
            <span>{{ __('messages.owner.user_management.employees.delete') }}</span>
          </button>
        </div>
      </div>
    @empty
      <div class="table-empty-state" style="padding: 24px;">
        <span class="material-symbols-outlined">person_off</span>
        <h4>{{ __('messages.owner.user_management.employees.no_employees') ?? 'No employees found' }}</h4>
        <p>{{ __('messages.owner.user_management.employees.add_first_employee') ?? 'Add your first employee to get started' }}</p>
      </div>
    @endforelse
  </div>

  {{-- =======================
    PAGINATION: tetap PAGE
  ======================= --}}
  @if($employees->hasPages())
    <div class="table-pagination">
      {{ $employees->links() }}
    </div>
  @endif

</div>

<style>
    /* Toggle desktop vs mobile */
.modern-card .only-desktop { display: block !important; }
.modern-card .only-mobile  { display: none !important; }

@media (max-width: 768px) {
  .modern-card .only-desktop { display: none !important; }
  .modern-card .only-mobile  { display: block !important; }
}

/* Mobile cards */
.mobile-employee-list {
  padding: 14px;
  display: grid;
  gap: 12px;
}

.employee-card {
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 14px;
  background: #fff;
  padding: 14px;
}

.employee-card__top {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

.employee-card__avatar img {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  object-fit: cover;
}

.employee-card__meta {
  flex: 1;
  min-width: 0;
}

.employee-card__name {
  font-weight: 700;
  font-size: 15px;
  line-height: 1.2;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.employee-card__sub {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.employee-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  border-radius: 999px;
  background: rgba(0,0,0,.04);
  font-size: 12px;
  color: #555;
}

.employee-chip .material-symbols-outlined {
  font-size: 16px;
  opacity: .8;
}

.employee-card__status {
  margin-left: auto;
}

.employee-card__info {
  margin-top: 12px;
  border-top: 1px dashed rgba(0,0,0,.08);
  padding-top: 12px;
  display: grid;
  gap: 8px;
}

.employee-info-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.employee-info-row .label {
  color: #888;
  font-size: 12px;
  flex: 0 0 auto;
}

.employee-info-row .value {
  font-size: 12px;
  color: #333;
  text-align: right;
  word-break: break-word;
}

.employee-info-row .value.link {
  color: inherit;
  text-decoration: underline;
}

.employee-card__actions {
  margin-top: 12px;
  display: flex;
  gap: 8px;
}

.btn-card-action {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 10px;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.10);
  background: #fff;
  font-size: 12px;
  font-weight: 600;
}

.btn-card-action .material-symbols-outlined {
  font-size: 18px;
}

.btn-card-action.danger {
  border-color: rgba(174,21,4,.25);
  color: #ae1504;
}

</style>