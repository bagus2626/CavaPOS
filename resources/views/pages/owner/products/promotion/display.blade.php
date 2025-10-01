<div class="table-responsive owner-promotions-table">
  <table class="table table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Kode Promo</th>
        <th>Nama Promo</th>
        <th>Tipe Promo</th>
        <th>Nilai</th>
        <th>Tanggal Aktif</th>
        <th>Hari Aktif</th>
        <th>Status</th>
        <th class="text-nowrap">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($promotions as $index => $promotion)
        @php
          $daysMap = [
            'sun' => 'Minggu','mon' => 'Senin','tue' => 'Selasa','wed' => 'Rabu',
            'thu' => 'Kamis','fri' => 'Jumat','sat' => 'Sabtu',
          ];
          $activeDaysArr = is_array($promotion->active_days) ? $promotion->active_days : [];
          $activeDays = count($activeDaysArr) === 7
              ? 'Setiap Hari'
              : collect($activeDaysArr)->map(fn($d) => $daysMap[$d] ?? $d)->join(', ');

          // Status badge class (akan di-soft oleh CSS)
          if (!$promotion->is_active) {
            $label = 'Nonaktif';   $class = 'badge-secondary';
          } else {
            $now = now(); $start = $promotion->start_date; $end = $promotion->end_date;
            if ($start && $now->lt($start)) { $label = 'Akan Aktif'; $class = 'badge-warning'; }
            elseif ($end && $now->gt($end)) { $label = 'Kadaluarsa'; $class = 'badge-danger'; }
            else { $label = 'Aktif'; $class = 'badge-success'; }
          }
        @endphp

        <tr data-category="{{ $promotion->promotion_type }}">
          <td class="text-muted">{{ $index + 1 }}</td>
          <td class="mono">{{ $promotion->promotion_code }}</td>
          <td class="fw-600">{{ $promotion->promotion_name }}</td>

          <td>
            @if($promotion->promotion_type === 'percentage')
              <span class="badge badge-type">Persentase</span>
            @else
              <span class="badge badge-type">Nominal</span>
            @endif
          </td>

          <td class="text-nowrap">
            @if($promotion->promotion_type == 'percentage')
              {{ number_format($promotion->promotion_value,0,',','.') }}%
            @else
              Rp {{ number_format($promotion->promotion_value,0,',','.') }}
            @endif
          </td>

          <td class="small">
            @if($promotion->start_date && $promotion->end_date)
              {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }}) – 
              {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
            @elseif($promotion->start_date)
              Mulai {{ $promotion->start_date->translatedFormat('d F Y') }} ({{ $promotion->start_date->format('H:i') }})
            @elseif($promotion->end_date)
              Sampai {{ $promotion->end_date->translatedFormat('d F Y') }} ({{ $promotion->end_date->format('H:i') }})
            @else
              <span class="text-muted">Tidak terbatas</span>
            @endif
          </td>

          <td class="small">{{ $activeDays }}</td>

          <td>
            <span class="badge {{ $class }} px-3 py-2">{{ $label }}</span>
          </td>

          <td class="text-nowrap">
            <a href="{{ route('owner.user-owner.promotions.show', $promotion->id) }}" class="btn btn-sm btn-outline-choco me-1">Detail</a>
            <a href="{{ route('owner.user-owner.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-outline-choco me-1">Edit</a>
            <button onclick="deletePromo({{ $promotion->id }})" class="btn btn-sm btn-soft-danger">Delete</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<style>
/* ===== Promotions table (scoped to the parent page .owner-promotions) ===== */
.owner-promotions .owner-promotions-table{
  border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,.08); overflow:hidden; background:#fff;
}
.owner-promotions .table{ margin-bottom:0; background:#fff; }
.owner-promotions thead th{
  background:#fff; border-bottom:2px solid #eef1f4 !important;
  color:#374151; font-weight:700; white-space:nowrap;
}
.owner-promotions tbody td{ vertical-align:middle; }
.owner-promotions tbody tr{ transition: background-color .12s ease; }
.owner-promotions tbody tr:hover{ background: rgba(140,16,0,.04); }

/* text utils */
.owner-promotions .fw-600{ font-weight:600; }
.owner-promotions .mono{
  font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;
  color:#374151;
}

/* Type badge */
.owner-promotions .badge.badge-type{
  background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe;
  border-radius:999px; padding:.28rem .55rem; font-weight:600;
}

/* Soft status badges (override bs contextual badges, only inside scope) */
.owner-promotions .badge-success{
  background:#ecfdf5 !important; color:#065f46 !important; border:1px solid #a7f3d0; border-radius:999px;
}
.owner-promotions .badge-secondary{
  background:#f3f4f6 !important; color:#374151 !important; border:1px solid #e5e7eb; border-radius:999px;
}
.owner-promotions .badge-warning{
  background:#fef3c7 !important; color:#92400e !important; border:1px solid #fde68a; border-radius:999px;
}
.owner-promotions .badge-danger{
  background:#fee2e2 !important; color:#991b1b !important; border:1px solid #fecaca; border-radius:999px;
}

/* Action buttons */
.owner-promotions .btn-outline-choco{
  color:#8c1000; border:1px solid #8c1000; background:#fff;
}
.owner-promotions .btn-outline-choco:hover{
  color:#fff; background:#8c1000; border-color:#8c1000;
}
.owner-promotions .btn-soft-danger{
  background:#fee2e2; color:#991b1b; border:1px solid #fecaca;
}
.owner-promotions .btn-soft-danger:hover{
  background:#fecaca; color:#7f1d1d; border-color:#fca5a5;
}
</style>
