@extends('layouts.owner')

@section('title', 'Promotion Detail')
@section('page_title', 'Promotion Detail')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Promotions
                    </a>

                    <div>
                        <a href="{{ route('owner.user-owner.promotions.edit', $data->id) }}" class="btn btn-warning mr-2">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        {{-- contoh tombol hapus (opsional)
                        <form action="{{ route('owner.user-owner.promotions.destroy', $data->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin hapus promo ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                        --}}
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $data->promotion_name }}</h3>
                        <div>
                            @if ($data->is_active)
                                <span class="badge badge-success px-3 py-2">Aktif</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    @php
                        // Map hari
                        $daysMap = [
                            'mon' => 'Senin',
                            'tue' => 'Selasa',
                            'wed' => 'Rabu',
                            'thu' => 'Kamis',
                            'fri' => 'Jumat',
                            'sat' => 'Sabtu',
                            'sun' => 'Minggu',
                        ];

                        // Pastikan $data->active_days berupa array (gunakan casts di Model: protected $casts = ['active_days' => 'array'];)
                        $days = is_array($data->active_days) ? $data->active_days : [];
                        $isEveryDay = count($days) === 7;

                        // Format tanggal (handle null)
                        $startLabel = optional($data->start_date)->translatedFormat('d F Y H:i');
                        $endLabel   = optional($data->end_date)->translatedFormat('d F Y H:i');

                        // Teks tipe & nilai
                        $typeLabel = $data->promotion_type === 'percentage' ? 'Percentage' : 'Amount';
                        $valueLabel = $data->promotion_type === 'percentage'
                            ? ($data->promotion_value . '%')
                            : ('Rp. ' . number_format((float) $data->promotion_value));
                    @endphp

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Kode Promo</dt>
                                    <dd class="col-sm-7">{{ $data->promotion_code ?? '-' }}</dd>

                                    <dt class="col-sm-5">Nama Promo</dt>
                                    <dd class="col-sm-7">{{ $data->promotion_name ?? '-' }}</dd>

                                    <dt class="col-sm-5">Tipe Promo</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge badge-info">{{ $typeLabel }}</span>
                                    </dd>

                                    <dt class="col-sm-5">Nilai</dt>
                                    <dd class="col-sm-7"><strong>{{ $valueLabel }}</strong></dd>
                                </dl>
                            </div>

                            <div class="col-lg-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Gunakan Periode (Expired)?</dt>
                                    <dd class="col-sm-7">
                                        @if((int)($data->uses_expiry ?? 0) === 1)
                                            <span class="badge badge-primary">Ya</span>
                                        @else
                                            <span class="badge badge-light border">Tidak</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5">Periode Berlaku</dt>
                                    <dd class="col-sm-7">
                                        @if((int)($data->uses_expiry ?? 0) === 1 && $data->start_date && $data->end_date)
                                            {{ $startLabel }} &mdash; {{ $endLabel }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5">Hari Aktif</dt>
                                    <dd class="col-sm-7">
                                        @if($isEveryDay)
                                            <span class="badge badge-success">Setiap Hari</span>
                                        @elseif(!empty($days))
                                            <div class="d-flex flex-wrap">
                                                @foreach($days as $d)
                                                    <span class="badge badge-pill badge-secondary mr-1 mb-1">
                                                        {{ $daysMap[$d] ?? $d }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <label class="mb-1">Description</label>
                            <div class="p-3 border rounded bg-light">
                                {!! nl2br(e($data->description ?? '-')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-list mr-1"></i>Back to List
                        </a>
                        <a href="{{ route('owner.user-owner.promotions.edit', $data->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit Promotion
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
