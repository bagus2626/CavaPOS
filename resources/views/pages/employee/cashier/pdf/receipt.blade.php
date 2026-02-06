<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt {{ $data->booking_order_code }}</title>
    <style>
        /* ====== Dasar layout thermal ====== */
        @page {
            margin: 10pt 10pt 12pt 10pt;
        }

        body {
            font-family: "Courier New", monospace;
            font-size: 16px;
            color: #111;
        }

        * {
            box-sizing: border-box;
        }

        /* Lebar konten mengikuti lebar kertas (80mm~227pt / 58mm~163pt) */
        .wrap {
            width: 100%;
        }

        /* ====== Header ====== */
        .title {
            text-align: center;
            /* font-weight: 700; */
            font-size: 16px; /* test */
            margin: 0 0 2px;
            line-height: 1.1;
        }

        .subtitle {
            text-align: center;
            color: #000000;
            font-size: 12px;
            margin: 0 0 6px;
        }

        /* ====== Separator ====== */
        .sep {
            border: 0;
            border-top: 1px dashed #000000;
            margin: 6px 0;
            height: 0;
        }

        /* ====== Meta info ====== */
        .meta {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta .label {
            color: #000000;
            width: 34%;
        }

        .meta .value {
            text-align: right;
            /* font-weight: 600; */
        }

        /* ====== Items ====== */
        table.items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* penting: cegah kolom berubah-ubah */
        }

        table.items th,
        table.items td {
            padding: 3px 0;
            vertical-align: top;
        }
        .items td.col-name {
            word-break: break-word;      /* aman untuk nama panjang */
            overflow-wrap: anywhere;     /* lebih kuat untuk thermal */
        }

        .items .col-qty { width: 12%; }
        .items .col-name { width: 58%; }
        .items .col-sub { width: 30%; }

        .items td.col-sub, .items th.col-sub {
            text-align: right;
            white-space: nowrap;
        }

        table.items thead th {
            text-align: left;
            color: #000000;
            /* font-weight: 700; */
            border-bottom: 1px solid #000000;
        }

        table.items .right {
            text-align: right;
            white-space: nowrap;
        }

        table.items .name {
            /* font-weight: 600; */
            padding-left: 0px;
            font-size: 16px; /* test */
            color: #000000;
        }

        /* Jarak antar item (baris utama) */
        .items tr.item-row td {
            padding-top: 10px;
            line-height: 1.35;
            /* padding-bottom: 6px; */
        }

        /* Opsi tetap rapat tapi tidak mepet */
        .items tr.opt-row td {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        table.items .hint {
            color: #000000;
            font-size: 14px;
        }

        .opt {
            padding-left: 14px;
            font-size: 16px;
            color: #000000;
        }

        .opt .bullet {
            margin-right: 6px;
        }

        .opt .price {
            float: right;
            white-space: nowrap;
            padding-left: 14px;
        }

        /* ====== Totals ====== */
        table.totals {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.totals td {
            padding: 3px 0;
        }

        table.totals .label {
            color: #000000;
        }

        table.totals .val {
            text-align: right;
            white-space: nowrap;
        }

        .grand {
            /* font-weight: 800; */
            font-size: 16px; /* test */
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 4px 0;
        }

        /* ====== WiFi Info ====== */
.wifi-box {
    border-radius: 6px;
    padding: 10px;
    margin: 6px 0;
    text-align: center;
}

.wifi-title {
    /* font-weight: 700; */
    font-size: 14px;
    color: #000000;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.wifi-title i {
    color: #000000;
    margin-right: 3px;
}

.wifi-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.wifi-item {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.wifi-label {
    color: #000000;
    /* font-weight: 600; */
    min-width: 50px;
    text-align: right;
}

.wifi-value {
    color: #111;
    /* font-weight: 700; */
    font-family: 'Courier New', monospace;
    letter-spacing: 0.3px;
    text-align: left;
    flex: 1;
    max-width: 120px;
}

/* ====== Thank You Message ====== */
.thank-you {
    text-align: center;
    color: #000000;
    font-size: 13px;
    /* font-weight: 600; */
    margin-top: 8px;
    line-height: 1.5;
    padding-bottom: 100px;
}

.come-again {
    font-size: 13px;
    color: #000000;
    /* font-weight: 500; */
    font-style: italic;
}

        /* ====== Footer ====== */
        .foot {
            text-align: center;
            color: #000000;
            font-size: 14px;
            margin-top: 6px;
            line-height: 1.3;
        }
        table.items thead {
            display: table-row-group;
        }
    </style>
</head>

<body>
    <div class="wrap">

        <div class="wrap">
            {{-- Logo --}}
            @if (!empty($partner->logo_grayscale))
                <div style="text-align: center; margin-bottom: 6px;">
                    <img src="{{ $partner->logo_grayscale }}"
                        style="max-width: 50px; max-height: 50px; display: inline-block; border-radius: 11%;">
                </div>
            @endif
            {{-- Header --}}
            <div class="title">{{ $partner->name ?? '' }}</div>
            @if ($partner?->address || $partner?->urban_village || $partner?->subdistrict || $partner?->city)
                <div class="subtitle">
                    {{ $partner->address ?? '' }}
                    {{ $partner->urban_village ? ', ' . $partner->urban_village : '' }}
                    {{ $partner->subdistrict ? ', ' . $partner->subdistrict : '' }}
                    {{ $partner->city ? ', ' . $partner->city : '' }}
                </div>
            @endif

            <hr class="sep">

            {{-- Meta Info --}}
            <table class="meta">
                <tr>
                    <td class="label">Kode</td>
                    <td class="value">{{ $data->booking_order_code }}</td>
                </tr>
                <tr>
                    <td class="label">Nama</td>
                    <td class="value">{{ $data->customer_name ?? '—' }}</td>
                </tr>
                @if (!empty($data->table?->table_no))
                    <tr>
                        <td class="label">Meja</td>
                        <td class="value">{{ $data->table->table_no }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Waktu</td>
                    <td class="value">{{ $data->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
                @if ($cashier)
                    <tr>
                        <td class="label">Kasir</td>
                        <td class="value">{{ $cashier->name ?? '-' }}</td>
                    </tr>
                @endif
            </table>

            <hr class="sep">

            {{-- Items --}}
            <table class="items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->order_details as $item)
                        @php
                            $qty = (int) $item->quantity;
                            $base = (int) $item->base_price;
                            $withPromo = $base - $item->promo_amount;
                            $optSum = (int) $item->options_price; // total harga opsi per 1 qty
                            $unit = $withPromo + $optSum; // harga per item termasuk opsi
                            $line = $qty * $unit; // subtotal line
                        @endphp

                        {{-- Baris produk (nama + subtotal kanan) --}}
                        <tr class="item-row">
                            <td class="name">
                                {{ $qty }} &times; {{ $item->partnerProduct->name ?? '-' }}
                                <span class="price">Rp {{ number_format($withPromo, 0, ',', '.') }}</span>
                            </td>
                            <td class="right">Rp {{ number_format($line, 0, ',', '.') }}</td>
                        </tr>

                        {{-- Opsi-opsi (jika ada) --}}
                        @foreach ($item->order_detail_options as $option)
                            <tr class="opt-row">
                                <td class="opt">
                                    <span class="bullet">•</span>{{ $option->option->name ?? '' }} Rp {{ number_format($option->price, 0, ',', '.') }}
                                    {{-- <span class="price">Rp {{ number_format($option->price, 0, ',', '.') }}</span> --}}
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                        {{-- <hr class="sep"> --}}
                    @endforeach
                </tbody>
            </table>

            <hr class="sep">

            {{-- Totals --}}
            @php
                $grandTotal = (int) $data->total_order_value;
            @endphp

            <table class="totals">
                <tr class="grand">
                    <td class="label">TOTAL</td>
                    <td class="val">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Metode Pembayaran</td>
                    <td class="val">
                        @if ($data->payment_method === 'manual_tf')
                            Transfer
                        @elseif ($data->payment_method === 'manual_ewallet')
                            E-wallet
                        @elseif ($data->payment_method === 'manual_qris')
                            QR Statis
                        @else
                            {{ $data->payment_method ?? '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Jumlah Dibayarkan</td>
                    <td class="val">Rp {{ number_format($payment->paid_amount ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Kembalian</td>
                    <td class="val">Rp {{ number_format($payment->change_amount ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            {{-- WiFi Information (only if enabled) --}}
@if ($partner->is_wifi_shown && ($partner->user_wifi || $partner->pass_wifi))
    <hr class="sep">
    
    <div class="wifi-box">
        <div class="wifi-title">
            <i class="fas fa-wifi"></i> WiFi
        </div>
        <div class="wifi-content">
            @if ($partner->user_wifi)
                <div class="wifi-item">
                    <span class="wifi-label">Username:</span>
                    <span class="wifi-value">{{ $partner->user_wifi }}</span>
                </div>
            @endif
            @if ($partner->pass_wifi)
                <div class="wifi-item">
                    <span class="wifi-label">Password:</span>
                    <span class="wifi-value">{{ $partner->pass_wifi }}</span>
                </div>
            @endif
        </div>
    </div>
@endif

<hr class="sep">

{{-- Thank You Message --}}
<div class="thank-you">
    Terima kasih atas kunjungan Anda!
    <br>
    <span class="come-again">Sampai jumpa kembali</span>
</div>
<hr class="sep">