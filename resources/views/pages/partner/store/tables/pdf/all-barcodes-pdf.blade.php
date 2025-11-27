<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcodes</title>
    <style>
        @page {
            size: A6 portrait;
            margin: 0; /* NO MARGIN */
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            height: 100%;
        }

        .page-break {
            page-break-after: always;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* atau cover */
            display: block;
        }
    </style>
</head>
<body>
@foreach($barcodes as $barcode)
    <div class="page">
        <img src="{{ $barcode['src'] }}" alt="">
    </div>

    {{-- Tambahkan page-break hanya jika BUKAN halaman terakhir --}}
    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach
</body>
</html>
