@php use Illuminate\Support\Str; @endphp

<div class="modern-card">
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:60px;">#</th>
                    <th>Table No</th>
                    <th>Outlet</th>
                    <th>Class Type</th>
                    <th>Description</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Picture</th>
                    <th class="text-center">
                        Barcode
                        <a href="{{ route('owner.user-owner.tables.generate-all-barcode') }}" target="_blank"
                            class="table-link" title="Download All Barcodes">
                            <span class="material-symbols-outlined"
                                style="font-size:1.25rem;vertical-align:middle;">picture_as_pdf</span>
                        </a>
                    </th>
                    <th class="text-center" style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody id="tableTableBody">
                @forelse ($tables as $index => $table)
                    <tr data-category="{{ $table->table_class }}" class="table-row">
                        <td class="text-center text-muted">{{ $tables->firstItem() + $index }}</td>
                        <td><span class="fw-600">{{ $table->table_no }}</span></td>
                        <td><span class="text-secondary">{{ $table->partner->name ?? '-' }}</span></td>
                        <td><span class="text-secondary">{{ $table->table_class }}</span></td>
                        <td><span class="text-secondary">{{ $table->description ?: '-' }}</span></td>
                        <td class="text-center">
                            @if ($table->status === 'available')
                                <span class="badge-modern badge-success">Available</span>
                            @elseif ($table->status === 'occupied')
                                <span class="badge-modern badge-warning">Occupied</span>
                            @elseif ($table->status === 'reserved')
                                <span class="badge-modern badge-info">Reserved</span>
                            @elseif ($table->status === 'not_available')
                                <span class="badge-modern badge-danger">Not Available</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $images = is_array($table->images) ? $table->images : [];
                                $images = array_filter($images, fn($img) => is_array($img) && isset($img['path']));
                            @endphp
                            @if (count($images) > 0)
                                <div class="table-images-cell">
                                    @foreach ($images as $image)
                                        <a href="{{ asset($image['path']) }}" target="_blank" class="table-image-link">
                                            <img src="{{ asset($image['path']) }}" class="table-thumbnail" loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button onclick="generateBarcode({{ $table->id }})" class="btn-table-action primary"
                                title="Barcode">
                                <span class="material-symbols-outlined">qr_code</span>
                            </button>
                        </td>
                        <td class="text-center">
                            <div class="table-actions">
                                <a href="{{ route('owner.user-owner.tables.show', $table->id) }}"
                                    class="btn-table-action view" title="View">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="{{ route('owner.user-owner.tables.edit', $table->id) }}"
                                    class="btn-table-action edit" title="Edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <button onclick="deleteTable({{ $table->id }})" class="btn-table-action delete"
                                    title="Delete">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="table-empty-state">
                                <span class="material-symbols-outlined">table_restaurant</span>
                                <h4>No tables found</h4>
                                <p>Add your first table to get started</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tables->hasPages())
        <div class="table-pagination">{{ $tables->withQueryString()->links() }}</div>
    @endif
</div>

<style>
    .table-images-cell {
        display: flex;
        gap: .5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .table-image-link {
        display: block;
        transition: transform .15s ease;
    }

    .table-image-link:hover {
        transform: scale(1.05);
    }

    .table-thumbnail {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
    }

    .btn-table-action.primary {
        background: rgba(140, 16, 0, .1);
        color: #8c1000;
    }

    .btn-table-action.primary:hover {
        background: #8c1000;
        color: #fff;
    }
</style>