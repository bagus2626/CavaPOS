@extends('layouts.owner')

@section('title',  __('messages.owner.products.promotions.promotion_detail'))
@section('page_title',  __('messages.owner.products.promotions.promotion_detail'))

@section('content')
<section class="content">
    <div class="container-fluid owner-promo-show"> {{-- PAGE SCOPE --}}
        <div class="row">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.owner.products.promotions.back_to_promotions') }}
                    </a>

                    <div>
                        <a href="{{ route('owner.user-owner.promotions.edit', $data->id) }}" class="btn btn-warning mr-2">
                            <i class="fas fa-edit mr-1"></i>{{ __('messages.owner.products.promotions.edit') }}
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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $data->promotion_name }}</h3>
                        <div>
                            @if ($data->is_active)
                                <span class="badge badge-success px-3 py-2">{{ __('messages.owner.products.promotions.active') }}</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">{{ __('messages.owner.products.promotions.inactive') }}</span>
                            @endif
                        </div>
                    </div>

                    @php
                        $daysMap = [
                            'sun' => __('messages.owner.products.promotions.sunday'),
                            'mon' => __('messages.owner.products.promotions.monday'),
                            'tue' => __('messages.owner.products.promotions.tuesday'),
                            'wed' => __('messages.owner.products.promotions.wednesday'),
                            'thu' => __('messages.owner.products.promotions.thursday'),
                            'fri' => __('messages.owner.products.promotions.friday'),
                            'sat' => __('messages.owner.products.promotions.saturday'),
                        ];
                        $days = is_array($data->active_days) ? $data->active_days : [];
                        $isEveryDay = count($days) === 7;

                        $startLabel = optional($data->start_date)->translatedFormat('d F Y H:i');
                        $endLabel   = optional($data->end_date)->translatedFormat('d F Y H:i');

                        $typeLabel = $data->promotion_type === 'percentage' ? 'Percentage' : 'Amount';
                        $valueLabel = $data->promotion_type === 'percentage'
                            ? ($data->promotion_value . '%')
                            : ('Rp. ' . number_format((float) $data->promotion_value));
                    @endphp

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <dl class="row mb-0 info-dl">
                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.promo_code') }}</dt>
                                    <dd class="col-sm-7">{{ $data->promotion_code ?? '-' }}</dd>

                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.promo_name') }}</dt>
                                    <dd class="col-sm-7">{{ $data->promotion_name ?? '-' }}</dd>

                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.promo_type') }}</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge badge-info">{{ $typeLabel }}</span>
                                    </dd>

                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.value') }}</dt>
                                    <dd class="col-sm-7"><strong>{{ $valueLabel }}</strong></dd>
                                </dl>
                            </div>

                            <div class="col-lg-6">
                                <dl class="row mb-0 info-dl">
                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.is_use_expiry') }}</dt>
                                    <dd class="col-sm-7">
                                        @if((int)($data->uses_expiry ?? 0) === 1)
                                            <span class="badge badge-primary">{{ __('messages.owner.products.promotions.yes') }}</span>
                                        @else
                                            <span class="badge badge-light border">{{ __('messages.owner.products.promotions.no') }}</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.valid_period') }}</dt>
                                    <dd class="col-sm-7">
                                        @if((int)($data->uses_expiry ?? 0) === 1 && $data->start_date && $data->end_date)
                                            {{ $startLabel }} &mdash; {{ $endLabel }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5">{{ __('messages.owner.products.promotions.active_day') }}</dt>
                                    <dd class="col-sm-7">
                                        @if($isEveryDay)
                                            <span class="badge badge-success">{{ __('messages.owner.products.promotions.every_day') }}</span>
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
                            <label class="mb-1">{{ __('messages.owner.products.promotions.description') }}</label>
                            <div class="p-3 border rounded desc-box">
                                {!! nl2br(e($data->description ?? '-')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('owner.user-owner.promotions.index') }}" class="btn btn-outline-secondary mr-2">
                            <i class="fas fa-list mr-1"></i>{{ __('messages.owner.products.promotions.back_to_list') }}
                        </a>
                        <a href="{{ route('owner.user-owner.promotions.edit', $data->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i>{{ __('messages.owner.products.promotions.edit_promotion') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
/* ===== Owner › Promotion Show (page scope) ===== */
.owner-promo-show{
  --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#fff;
  --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
}

.btn-secondary{
  background:var(--choco); border-color:var(--choco);
}
.btn-secondary:hover{
  background:var(--soft-choco); border-color:var(--soft-choco);
}

.btn-outline-secondary{
  color:var(--choco); border-color:var(--choco);
}
.btn-outline-secondary:hover{
  background:var(--choco); border-color:var(--choco); color:#fff;
}

/* Card */
.owner-promo-show .card{
  border:0; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; background:var(--paper);
}
.owner-promo-show .card-header{ background:#fff; border-bottom:1px solid #eef1f4; }
.owner-promo-show .card-title{ color:var(--ink); font-weight:700; }

/* Buttons */
.owner-promo-show .btn-primary{ background:var(--choco); border-color:var(--choco); }
.owner-promo-show .btn-primary:hover{ background:var(--soft-choco); border-color:var(--soft-choco); }

/* Badges (soft) */
.owner-promo-show .badge{ border-radius:999px; font-weight:600; }
.owner-promo-show .badge.badge-success{
  background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;
}
.owner-promo-show .badge.badge-secondary{
  background:#f3f4f6; color:#374151; border:1px solid #e5e7eb;
}
.owner-promo-show .badge.badge-primary{
  background:rgba(140,16,0,.1); color:var(--choco); border:1px solid rgba(140,16,0,.25);
}
.owner-promo-show .badge.badge-info{
  background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
}
.owner-promo-show .badge.badge-light{
  background:#fff; color:#374151; border:1px solid #e5e7eb;
}

/* Definition list */
.owner-promo-show .info-dl dt{ color:#6b7280; font-weight:600; }
.owner-promo-show .info-dl dd{ margin-bottom:.5rem; }

/* Description box */
.owner-promo-show .desc-box{
  background:#fcfcfc; border-color:#eef1f4;
}

/* Small text overrides */
.owner-promo-show .text-muted{ color:#6b7280 !important; }

/* Responsive tweak */
@media (max-width: 576px){
  .owner-promo-show .card-title{ font-size:1.05rem; }
}
</style>
@endsection
