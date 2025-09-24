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
                            {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }}) -
                            {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
                        @elseif($promotion->start_date)
                            Mulai {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }})
                        @elseif($promotion->end_date)
                            Sampai {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
                        @else
                            <span class="text-muted">Tidak terbatas</span>
                        @endif
                    </td>
                    <td>{{ $activeDays }}</td>
                    <td>
                        @php
                            // Jika toggle nonaktif, langsung Nonaktif tanpa lihat tanggal
                            if (!$promotion->is_active) {
                                $label = 'Nonaktif';
                                $class = 'badge-secondary';
                            } else {
                                $now   = now();
                                $start = $promotion->start_date;
                                $end   = $promotion->end_date;

                                if ($start && $now->lt($start)) {
                                    // Belum masuk rentang waktu
                                    $label = 'Akan Aktif';
                                    $class = 'badge-warning';
                                } elseif ($end && $now->gt($end)) {
                                    // Sudah melewati rentang waktu
                                    $label = 'Kadaluarsa';
                                    $class = 'badge-danger';
                                } else {
                                    // Dalam rentang waktu (atau tanpa batas tanggal)
                                    $label = 'Aktif';
                                    $class = 'badge-success';
                                }
                            }
                        @endphp

                        <span class="badge {{ $class }} px-3 py-2">{{ $label }}</span>
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
