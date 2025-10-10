{{-- resources/views/errors/404.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>404 Not Found</title>
  <style>
    :root{
      --choco:#8c1000; --soft-choco:#c12814; --ink:#22272b; --paper:#f7f7f8;
      --radius:12px; --shadow:0 6px 20px rgba(0,0,0,.08);
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;background:var(--paper);color:var(--ink);}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
    .card{width:100%;max-width:720px;background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:32px;text-align:center}
    .title{font-size:56px;line-height:1;margin:0 0 8px}
    .lead{margin:0 0 20px;font-size:18px;color:#4b5563}
    .btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
    .btn{appearance:none;border:1px solid transparent;border-radius:10px;padding:10px 16px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block}
    .btn-primary{background:var(--choco);color:#fff;border-color:var(--choco)}
    .btn-primary:hover{background:var(--soft-choco);border-color:var(--soft-choco)}
    .btn-outline{background:#fff;color:var(--choco);border-color:var(--choco)}
    .btn-outline:hover{background:var(--choco);color:#fff}
    .muted{color:#6b7280;font-size:12px;margin-top:14px}
  </style>
</head>
<body>
  <main class="wrap">
    <div class="card" role="alert" aria-live="polite">
      <h1 class="title">404</h1>
      <p class="lead">Sorry, the page you are looking for was not found.</p>
      <div class="btns">
        <a href="{{ url()->previous() }}" class="btn btn-outline">Back</a>
        {{-- <a href="{{ url('/') }}" class="btn btn-primary">Ke Beranda</a> --}}
      </div>
      <p class="muted">{{ config('app.name') }}</p>
    </div>
  </main>
</body>
</html>
