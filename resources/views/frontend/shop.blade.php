<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('seo.name', 'HustleStore'))</title>
    <meta name="description" content="{{ config('seo.description') }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="theme-color" content="{{ config('seo.theme_color', '#7C3AED') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" href="{{ asset(config('seo.favicon', 'assets/media/logos/favicon.png')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--bg:#26093f;--bg-top:#1c0733;--line:rgba(255,255,255,.08);--purple:#8a3ffc;--purple2:#b14aed;--pink:#e84393;--text:#f4eeff;--muted:#c3aee0;--dim:#9c86bf;--gold:#f6b21b;--green:#22c55e;--red:#ff4d6d}
        html{scroll-behavior:smooth}
        body{font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;color:var(--text);background:radial-gradient(1100px 500px at 50% -120px,rgba(138,63,252,.35),transparent 70%),linear-gradient(180deg,var(--bg-top),var(--bg) 40%,var(--bg));background-attachment:fixed;min-height:100vh;line-height:1.5;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}img{max-width:100%;display:block}
        .container{width:100%;max-width:100%;margin:0 auto;padding:0 clamp(16px,4vw,64px)}
        header.site{position:sticky;top:0;z-index:50;background:rgba(24,7,45,.94);backdrop-filter:blur(10px);border-bottom:1px solid var(--line)}
        .hrow{display:flex;align-items:center;gap:14px;padding:12px 0}
        .brand{display:flex;align-items:center;gap:10px}
        .brand img{height:34px}.brand b{font-weight:800;font-size:17px}
        .hrow .spacer{flex:1}
        .lnk{color:var(--muted);font-weight:600;font-size:13.5px;padding:8px 12px;border-radius:10px}
        .lnk:hover{color:#fff;background:rgba(255,255,255,.07)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font-weight:700;border-radius:999px;cursor:pointer;border:0;transition:transform .15s,box-shadow .15s;font-size:14px}
        .btn:hover{transform:translateY(-1px)}
        .btn-primary{background:linear-gradient(135deg,var(--purple),var(--purple2));color:#fff;padding:12px 24px;box-shadow:0 8px 20px rgba(138,63,252,.4)}
        .btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
        .btn-ghost{background:rgba(255,255,255,.08);color:var(--text);padding:10px 18px;border:1px solid var(--line)}
        .card{background:rgba(255,255,255,.05);border:1px solid var(--line);border-radius:18px;padding:22px}
        .crumb{font-size:13px;color:var(--dim);padding:16px 0 2px}
        .crumb a:hover{color:#fff}
        h1.page{font-size:clamp(22px,4vw,30px);font-weight:800;margin:6px 0 4px}
        .alert{border-radius:12px;padding:12px 16px;margin:14px 0;font-weight:600;font-size:14px}
        .alert-danger{background:rgba(255,77,109,.15);border:1px solid rgba(255,77,109,.4);color:#ffbccb}
        .alert-info{background:rgba(37,99,235,.15);border:1px solid rgba(37,99,235,.4);color:#cfe0ff}
        label.fld{display:block;font-size:13px;font-weight:600;color:var(--muted);margin:14px 0 6px}
        .inp{width:100%;background:rgba(255,255,255,.07);border:1px solid var(--line);border-radius:12px;padding:13px 15px;color:#fff;font-size:14px;outline:0}
        .inp:focus{border-color:var(--purple)}
        .inp::placeholder{color:var(--dim)}
        footer{margin-top:50px;border-top:1px solid var(--line);padding:22px 0;color:var(--dim);font-size:12.5px;text-align:center}
        footer a{color:var(--muted)}footer a:hover{color:#fff}
        .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11.5px;font-weight:700}
        .b-green{background:rgba(34,197,94,.18);color:#7ef0a6;border:1px solid rgba(34,197,94,.4)}
        .b-amber{background:rgba(246,178,27,.18);color:#ffd77a;border:1px solid rgba(246,178,27,.4)}
        .b-red{background:rgba(255,77,109,.18);color:#ffbccb;border:1px solid rgba(255,77,109,.4)}
        .b-blue{background:rgba(37,99,235,.2);color:#cfe0ff;border:1px solid rgba(37,99,235,.45)}
        @yield('styles')
    </style>
    @include('frontend.partials.cursor')
</head>
<body>
    <header class="site">
        <div class="container">
            <div class="hrow">
                <a href="{{ url('/') }}" class="brand"><img src="{{ asset('assets/media/logos/logo_store.png') }}" alt="HustleStore"><b>HustleStore</b></a>
                <div class="spacer"></div>
                <a href="{{ route('catalog') }}" class="lnk">Semua Produk</a>
                <a href="{{ route('order.track') }}" class="btn btn-ghost">Lacak Pesanan</a>
            </div>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        <div class="container">
            © {{ date('Y') }} HustleStore — Top up game, pulsa &amp; voucher streaming.<br>
            <a href="{{ route('catalog') }}">Katalog</a> · <a href="{{ route('order.track') }}">Lacak Pesanan</a> · <a href="{{ route('page.terms') }}">Syarat &amp; Ketentuan</a> · <a href="{{ route('page.privacy') }}">Kebijakan Privasi</a> · <a href="https://wa.me/{{ config('seo.whatsapp','6281234567890') }}" target="_blank" rel="noopener">WhatsApp CS</a>
        </div>
    </footer>
    @yield('scripts')
</body>
</html>
