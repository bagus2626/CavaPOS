@extends('layouts.staff')
@section('title', 'Employee Detail')

@section('content')
    @php
        use Illuminate\Support\Str;
        $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager');
        $emp = $employee ?? null;
        $img =
            $emp && $emp->image
                ? (Str::startsWith($emp->image, ['http://', 'https://'])
                    ? $emp->image
                    : asset('storage/' . $emp->image))
                : null;
        $isActive = (int) ($emp->is_active ?? 0) === 1;
    @endphp

    <div class="modern-container">
        <div class="container-modern">

            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Employee Detail</h1>
                    <p class="page-subtitle">View complete employee information</p>
                </div>
                <a href="{{ route('employee.' . $empRole . '.employees.index') }}" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-modern">
                    <div class="alert-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
            @endif

            {{-- Hero Card --}}
            <div class="modern-card">
                <div class="detail-hero-header">
                    <div class="detail-avatar">
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $emp->name }}" class="detail-avatar-image">
                        @else
                            <div class="detail-avatar-placeholder">
                                {{ Str::upper(Str::substr($emp->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="detail-hero-info">
                        <h3 class="detail-hero-name">{{ $emp->name }}</h3>
                        <p class="detail-hero-subtitle">{{ optional($emp->partner)->name ?? 'No outlet assigned' }}</p>
                        <div class="detail-hero-badges">
                            <span class="badge-modern badge-info">{{ $emp->role ?? '—' }}</span>
                            @if ($isActive)
                                <span class="badge-modern badge-success">Active</span>
                            @else
                                <span class="badge-modern badge-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Body --}}
            <div class="modern-card">
                <div class="card-body-modern">

                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <h3 class="section-title">Personal Information</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Full Name</div>
                                <div class="detail-info-value">{{ $emp->name ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Email</div>
                                <div class="detail-info-value">
                                    @if (!empty($emp->email))
                                        <a href="mailto:{{ $emp->email }}">{{ $emp->email }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Outlet</div>
                                <div class="detail-info-value">{{ optional($emp->partner)->name ?? '—' }}</div>
                            </div>
                            <div class="detail-info-item">
                                <div class="detail-info-label">Username</div>
                                <div class="detail-info-value">{{ $emp->user_name ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    <div class="section-header">
                        <div class="section-icon section-icon-red">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <h3 class="section-title">System Information</h3>
                    </div>

                    <div class="detail-info-grid">
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Created At</div>
                                <div class="detail-info-value">
                                    {{ optional($emp->created_at)->format('d M Y, H:i') ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="detail-info-group">
                            <div class="detail-info-item">
                                <div class="detail-info-label">Updated At</div>
                                <div class="detail-info-value">
                                    {{ optional($emp->updated_at)->format('d M Y, H:i') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($empRole === 'manager')
                        <div class="action-buttons-group">
                            <a href="{{ route('employee.' . $empRole . '.employees.edit', $employee->id) }}"
                                class="btn-action btn-action-edit">
                                <span class="material-symbols-outlined">edit</span>
                                Edit
                            </a>
                            <form action="{{ route('employee.' . $empRole . '.employees.destroy', $employee->id) }}"
                                method="POST" class="d-inline-block" id="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-action-delete" onclick="confirmDelete()">
                                    <span class="material-symbols-outlined">delete</span>
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <style>
        .action-buttons-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            border: 1px solid rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: .95rem;
            background: #fff;
        }

        .btn-action .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .btn-action-edit {
            color: #333;
        }

        .btn-action-edit:hover {
            background: #f8f9fa;
            color: #333;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .btn-action-delete {
            border-color: rgba(174, 21, 4, .25);
            color: #ae1504;
        }

        .btn-action-delete:hover {
            background: rgba(174, 21, 4, .05);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(174, 21, 4, .15);
        }

        @media (max-width: 576px) {
            .action-buttons-group {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete() {
                Swal.fire({
                    title: 'Delete this employee?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b3311d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('deleteForm').submit();
                });
            }
        </script>
    @endpush
@endsection
```

---

**Ringkasan struktur folder:**
```
resources/views/pages/employee/human-resource/employee/
├── index.blade.php
├── display.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
