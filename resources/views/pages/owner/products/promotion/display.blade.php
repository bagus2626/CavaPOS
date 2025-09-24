<div class="table-responsive rounded-xl">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Kode Promo</th>
                <th>Nama Promo</th>
                <th>Tipe Promo</th>
                <th>Nilai</th>
                <th>Tanggal Aktif</th>
                <th>Hari Aktif</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($promotions as $index => $promotion)
                @php
                    $daysMap = [
                        'sun' => 'Minggu',
                        'mon' => 'Senin',
                        'tue' => 'Selasa',
                        'wed' => 'Rabu',
                        'thu' => 'Kamis',
                        'fri' => 'Jumat',
                        'sat' => 'Sabtu',
                    ];

                    $activeDaysArr = is_array($promotion->active_days) ? $promotion->active_days : [];
                    $activeDays = count($activeDaysArr) === 7
                        ? 'Setiap Hari'
                        : collect($activeDaysArr)->map(fn($day) => $daysMap[$day] ?? $day)->join(', ');
                @endphp

                <tr data-category="{{ $promotion->promotion_type }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $promotion->promotion_code }}</td>
                    <td>{{ $promotion->promotion_name }}</td>
                    <td>{{ $promotion->promotion_type }}</td>
                    @if($promotion->promotion_type == 'percentage')
                        <td>{{ $promotion->promotion_value }}%</td>
                    @else
                        <td>Rp. {{ number_format($promotion->promotion_value) }}</td>
                    @endif
                    <td>
                        @if($promotion->start_date && $promotion->end_date)
                            {{ $promotion->start_date->translatedFormat('d F Y') }} -
                            {{ $promotion->end_date->translatedFormat('d F Y') }}
                        @elseif($promotion->start_date)
                            Mulai {{ $promotion->start_date->translatedFormat('d F Y') }}
                        @elseif($promotion->end_date)
                            Sampai {{ $promotion->end_date->translatedFormat('d F Y') }}
                        @else
                            <span class="text-muted">Tidak terbatas</span>
                        @endif
                    </td>

                    <td>{{ $activeDays }}</td>
                    <td>
                        @if ($promotion->is_active)
                            <span class="badge badge-success px-3 py-2">Aktif</span>
                        @else
                            <span class="badge badge-secondary px-3 py-2">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="deletePromo({{ $promotion->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
