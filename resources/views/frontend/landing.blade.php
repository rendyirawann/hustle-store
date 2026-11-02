@php
    use Illuminate\Support\Str;
    $waNumber = config('seo.whatsapp', '6281234567890');
    $rupiah = fn ($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
    $brandsByCategory = $brandsByCategory ?? collect();

    // Helper gambar brand: URL penuh atau file di assets/media/brands.
    $brandImg = function ($b) {
        if (! $b->logo) return null;
        return Str::startsWith($b->logo, ['http://', 'https://']) ? $b->logo : asset('assets/media/brands/' . ltrim($b->logo, '/'));
    };

    $grads = [
        ['#7b2ff7', '#e84393'], ['#2563eb', '#22d3ee'], ['#f7971e', '#ffcf3d'],
        ['#11998e', '#38ef7d'], ['#8e2de2', '#4a00e0'], ['#ff512f', '#dd2476'],
        ['#396afc', '#2948ff'], ['#e100ff', '#7f00ff'],
    ];

    $games = collect($brandsByCategory['game'] ?? []);
    $rail = $games->take(8);

    // Widget hero: tab kategori -> daftar brand.
    $tabDefs = [
        ['key' => 'game', 'label' => 'Top Up Game'],
        ['key' => 'pulsa', 'label' => 'Pulsa'],
        ['key' => 'data', 'label' => 'Paket Data'],
        ['key' => 'pln', 'label' => 'Token PLN'],
        ['key' => 'ewallet', 'label' => 'E-Wallet'],
        ['key' => 'streaming', 'label' => 'Streaming'],
        ['key' => 'voucher', 'label' => 'Voucher'],
    ];
    $tabDefs = array_values(array_filter($tabDefs, fn ($t) => isset($brandsByCategory[$t['key']]) && count($brandsByCategory[$t['key']])));
    $tabData = [];
    foreach ($tabDefs as $t) {
        $tabData[$t['key']] = collect($brandsByCategory[$t['key']])->map(fn ($b) => ['s' => $b->slug, 'n' => $b->name])->values();
    }

    // Bento kategori.
    $catCards = [
        ['icon' => 'game', 'title' => 'Top Up Game', 'sub' => 'ML, FF, PUBG & lainnya'],
        ['icon' => 'phone', 'title' => 'Pulsa', 'sub' => 'Semua operator'],
        ['icon' => 'wifi', 'title' => 'Paket Data', 'sub' => 'Internet lancar'],
        ['icon' => 'bolt', 'title' => 'Token PLN', 'sub' => 'Semua nominal'],
        ['icon' => 'wallet', 'title' => 'E-Wallet', 'sub' => 'DANA, OVO, GoPay'],
        ['icon' => 'play', 'title' => 'Streaming', 'sub' => 'Netflix, Vidio, dll.'],
        ['icon' => 'ticket', 'title' => 'Voucher', 'sub' => 'Steam, Google Play'],
        ['icon' => 'grid', 'title' => 'Lainnya', 'sub' => 'Produk digital lain'],
    ];

    $marquee = ['QRIS', 'DANA', 'OVO', 'GoPay', 'ShopeePay', 'BCA', 'Mandiri', 'BRI', 'BNI', 'Alfamart', 'Indomaret', 'LinkAja'];

    $ld = [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', 'name' => config('seo.name', 'HustleStore'), 'url' => url('/'), 'logo' => asset(config('seo.logo', 'assets/media/logos/logo_store.png')), 'sameAs' => ['https://instagram.com/hustlestore', 'https://facebook.com/hustlestore', 'https://tiktok.com/@hustlestore']],
            ['@type' => 'WebSite', 'name' => config('seo.name', 'HustleStore'), 'url' => url('/'), 'potentialAction' => ['@type' => 'SearchAction', 'target' => url('/produk') . '?q={search_term_string}', 'query-input' => 'required name=search_term_string']],
            ['@type' => 'OnlineStore', 'name' => config('seo.name', 'HustleStore'), 'image' => asset(config('seo.og_image', 'assets/media/hustle/og-image.png')), 'url' => url('/'), 'description' => config('seo.description'), 'priceRange' => 'Rp1.000 - Rp5.000.000', 'areaServed' => 'Indonesia'],
        ],
    ];
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('seo.name', 'HustleStore') }} — {{ config('seo.tagline', 'Top Up Game, Pulsa & Voucher Streaming Termurah') }}</title>
    <meta name="description" content="{{ config('seo.description') }}">
    <meta name="keywords" content="{{ config('seo.keywords') }}">
    <meta name="author" content="{{ config('seo.author', 'HustleStore') }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0b0a18">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" href="{{ asset(config('seo.favicon', 'assets/media/logos/favicon.png')) }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/media/logos/apple-touch-icon.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('seo.name', 'HustleStore') }}">
    <meta property="og:title" content="{{ config('seo.name', 'HustleStore') }} — {{ config('seo.tagline') }}">
    <meta property="og:description" content="{{ config('seo.description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset(config('seo.og_image', 'assets/media/hustle/og-image.png')) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ config('seo.locale', 'id_ID') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('seo.name', 'HustleStore') }} — {{ config('seo.tagline') }}">
    <meta name="twitter:description" content="{{ config('seo.description') }}">
    <meta name="twitter:image" content="{{ asset(config('seo.og_image', 'assets/media/hustle/og-image.png')) }}">
    <script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    {{-- Percepat LCP: preload gambar hero & preconnect font --}}
    <link rel="preload" as="image" href="{{ asset('assets/media/hustle/hero.jpg') }}" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --bg:#0b0a18;--bg2:#12102a;--panel:rgba(255,255,255,.045);--panel2:rgba(255,255,255,.07);
            --line:rgba(255,255,255,.09);--line2:rgba(255,255,255,.14);
            --purple:#8b5cf6;--purple2:#a78bfa;--violet:#c4b5fd;--gold:#fbbf24;
            --text:#eeeaff;--muted:#a49dc6;--dim:#7d76a3;
        }
        html{scroll-behavior:smooth}
        body{font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;color:var(--text);background:var(--bg);line-height:1.5;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}img{max-width:100%;display:block}
        .container{width:100%;max-width:100%;margin:0 auto;padding:0 clamp(16px,4vw,64px)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font-weight:700;border-radius:14px;cursor:pointer;border:0;transition:.16s;font-size:14px}
        .btn:hover{transform:translateY(-1px)}
        .btn-primary{background:linear-gradient(135deg,#7c3aed,#9d5cf5);color:#fff;padding:13px 26px;box-shadow:0 10px 26px rgba(124,58,237,.42)}
        .btn-line{background:transparent;border:1px solid var(--line2);color:var(--text);padding:12px 22px}
        .btn-line:hover{background:var(--panel2)}
        .sec{padding:54px 0 0}
        .kicker{display:inline-flex;align-items:center;gap:8px;font-size:11.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--violet);margin-bottom:10px}
        .kicker::before{content:'';width:26px;height:2px;background:linear-gradient(90deg,var(--purple),transparent)}
        .sec h2.t{font-size:clamp(21px,2.6vw,28px);font-weight:800;color:#fff}
        .sec .head{display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:24px}
        .link-all{color:var(--violet);font-weight:700;font-size:13.5px;white-space:nowrap}
        .link-all:hover{color:#fff}

        /* ===== Header: pill nav mengambang ===== */
        header.site{position:fixed;top:0;left:0;right:0;z-index:70;padding:14px 0;transition:.25s}
        header.site .bar{display:flex;align-items:center;gap:16px;background:rgba(15,13,32,.55);border:1px solid var(--line);border-radius:18px;padding:9px 16px;backdrop-filter:blur(14px)}
        header.site.solid .bar{background:rgba(13,11,28,.92);border-color:var(--line2);box-shadow:0 14px 34px rgba(0,0,0,.45)}
        .brand{display:flex;align-items:center;gap:10px}
        .brand img{height:32px}
        .brand b{font-weight:800;font-size:17.5px;color:#fff}.brand b span{color:var(--purple2)}
        nav.desk{display:flex;gap:2px;margin-left:8px}
        nav.desk a{padding:8px 13px;border-radius:10px;font-weight:600;font-size:13.5px;color:var(--muted)}
        nav.desk a:hover{color:#fff;background:var(--panel2)}
        .spacer{flex:1}
        .hamburger{display:none;background:var(--panel2);border:1px solid var(--line);width:40px;height:40px;border-radius:11px;cursor:pointer;color:#fff;align-items:center;justify-content:center}
        .mnav{display:none;flex-direction:column;gap:2px;background:rgba(13,11,28,.96);border:1px solid var(--line);border-radius:16px;margin-top:8px;padding:10px;backdrop-filter:blur(14px)}
        .mnav a{padding:11px 13px;border-radius:10px;font-weight:600;color:var(--muted);font-size:14px}
        .mnav a:hover{color:#fff;background:var(--panel2)}
        .mnav.open{display:flex}

        /* ===== Hero: full-viewport + background hidup (Ken Burns, orbs, sparks) ===== */
        .hero{position:relative;min-height:100svh;display:flex;align-items:center;padding:110px 0 60px;overflow:hidden;background:var(--bg)}
        .hero-bg{position:absolute;inset:-5%;background:url('{{ asset('assets/media/hustle/hero.jpg') }}') center right/cover no-repeat;
            animation:kenburns 26s ease-in-out infinite alternate;will-change:transform}
        @keyframes kenburns{from{transform:scale(1) translate(0,0)}to{transform:scale(1.1) translate(-1.6%,1.4%)}}
        .hero-shade{position:absolute;inset:0;background:linear-gradient(100deg,rgba(11,10,24,.97) 6%,rgba(11,10,24,.82) 38%,rgba(11,10,24,.3) 66%,rgba(11,10,24,.05) 88%)}
        @media(max-width:900px){
            .hero-bg{background-position:center}
            .hero-shade{background:linear-gradient(180deg,rgba(11,10,24,.88),rgba(11,10,24,.96))}
        }
        .hero::after{content:'';position:absolute;left:0;right:0;bottom:0;height:140px;background:linear-gradient(180deg,transparent,var(--bg));pointer-events:none;z-index:3}
        /* Orb cahaya melayang */
        .orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:.42;pointer-events:none;animation:drift 16s ease-in-out infinite alternate;will-change:transform}
        .orb.o1{width:420px;height:420px;left:-90px;top:6%;background:radial-gradient(circle,#7c3aed,transparent 70%)}
        .orb.o2{width:340px;height:340px;right:5%;bottom:-8%;background:radial-gradient(circle,#e879f9,transparent 70%);animation-duration:22s;animation-delay:-7s}
        .orb.o3{width:280px;height:280px;left:36%;top:-12%;background:radial-gradient(circle,#2563eb,transparent 70%);animation-duration:19s;animation-delay:-12s}
        @keyframes drift{from{transform:translate(0,0) scale(1)}to{transform:translate(56px,36px) scale(1.18)}}
        @media(max-width:900px){.orb.o3{display:none}.orb{filter:blur(50px)}}
        /* Bintang kelap-kelip */
        .spark{position:absolute;width:5px;height:5px;border-radius:50%;background:#c4b5fd;box-shadow:0 0 12px 3px rgba(167,139,250,.75);opacity:0;animation:twinkle 5s ease-in-out infinite;pointer-events:none}
        @keyframes twinkle{0%,100%{opacity:0;transform:scale(.4)}50%{opacity:.95;transform:scale(1)}}
        @media (prefers-reduced-motion: reduce){.hero-bg,.orb,.spark{animation:none}.spark{opacity:.35}}
        .hgrid{display:grid;grid-template-columns:1fr;gap:34px;width:100%;position:relative;z-index:2}
        @media(min-width:960px){.hgrid{grid-template-columns:1.05fr .95fr;align-items:center}}
        .hero .chip{display:inline-flex;align-items:center;gap:8px;background:rgba(139,92,246,.14);border:1px solid rgba(139,92,246,.4);color:var(--violet);font-size:12.5px;font-weight:700;padding:8px 15px;border-radius:999px;margin-bottom:20px}
        .hero h1{font-size:clamp(30px,4.8vw,52px);font-weight:800;line-height:1.08;letter-spacing:-.5px;margin-bottom:16px;max-width:560px}
        .hero h1 em{font-style:normal;background:linear-gradient(90deg,#a78bfa,#e879f9 60%,#fbbf24);-webkit-background-clip:text;background-clip:text;color:transparent}
        .hero p.sub{color:var(--muted);font-size:15.5px;max-width:440px;margin-bottom:24px}
        .trust{display:flex;flex-wrap:wrap;gap:10px}
        .trust span{display:inline-flex;align-items:center;gap:8px;background:var(--panel);border:1px solid var(--line);border-radius:999px;padding:8px 15px;color:var(--muted);font-size:12.5px;font-weight:600}

        /* Widget kartu kaca */
        .widget{background:rgba(17,14,36,.72);border:1px solid var(--line2);border-radius:24px;padding:24px;backdrop-filter:blur(18px);box-shadow:0 30px 70px rgba(0,0,0,.5);max-width:480px;justify-self:end;width:100%}
        @media(max-width:959px){.widget{justify-self:stretch;max-width:none}}
        .widget .wtag{display:inline-flex;align-items:center;gap:7px;background:linear-gradient(90deg,rgba(251,191,36,.16),transparent);border:1px solid rgba(251,191,36,.35);color:var(--gold);font-size:11px;font-weight:800;letter-spacing:1.2px;padding:6px 13px;border-radius:999px;margin-bottom:12px}
        .widget h3{font-size:20px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:16px}
        .wtabs{display:flex;gap:6px;overflow-x:auto;padding-bottom:4px;margin-bottom:14px;scrollbar-width:none}
        .wtabs::-webkit-scrollbar{display:none}
        .wtabs button{white-space:nowrap;border:1px solid var(--line);background:var(--panel);font-weight:700;font-size:12.5px;color:var(--muted);padding:9px 15px;border-radius:999px;cursor:pointer;transition:.15s;font-family:inherit}
        .wtabs button.on{background:linear-gradient(135deg,#7c3aed,#9d5cf5);border-color:transparent;color:#fff;box-shadow:0 6px 16px rgba(124,58,237,.4)}
        .wfield label{display:block;font-size:11.5px;font-weight:700;color:var(--dim);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px}
        .wfield .ctrl{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.05);border:1px solid var(--line2);border-radius:14px;padding:13px 15px;transition:.15s}
        .wfield .ctrl:focus-within{border-color:var(--purple)}
        .wfield select{border:0;background:transparent;outline:0;width:100%;font-size:14.5px;font-weight:600;color:var(--text);font-family:inherit;cursor:pointer}
        .wfield select option{background:#171430;color:#fff}
        .widget .btn-primary{width:100%;margin-top:16px;padding:15px;font-size:15px;border-radius:14px}
        .widget .wnote{text-align:center;color:var(--dim);font-size:11.5px;margin-top:12px}

        /* ===== Marquee ===== */
        .marq{border-top:1px solid var(--line);border-bottom:1px solid var(--line);background:var(--bg2);overflow:hidden;padding:15px 0;margin-top:-1px}
        .marq .track{display:flex;gap:44px;width:max-content;animation:marq 30s linear infinite}
        .marq span{display:inline-flex;align-items:center;gap:10px;color:var(--dim);font-weight:800;font-size:13.5px;letter-spacing:1px;white-space:nowrap}
        .marq span::before{content:'✦';color:var(--purple);font-size:11px}
        @keyframes marq{to{transform:translateX(-50%)}}
        @media (prefers-reduced-motion: reduce){.marq .track{animation:none}}

        /* ===== Bento kategori ===== */
        .bento{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
        @media(min-width:760px){.bento{grid-template-columns:repeat(4,1fr)}.bfeat{grid-column:span 2;grid-row:span 2}}
        .bcell{background:var(--panel);border:1px solid var(--line);border-radius:20px;padding:20px;transition:.18s;position:relative;overflow:hidden;display:flex;flex-direction:column;gap:10px}
        .bcell:hover{border-color:rgba(139,92,246,.5);transform:translateY(-3px);background:var(--panel2)}
        .bcell .ic{width:44px;height:44px;border-radius:13px;background:rgba(139,92,246,.14);border:1px solid rgba(139,92,246,.3);color:var(--purple2);display:flex;align-items:center;justify-content:center}
        .bcell h4{font-size:15px;font-weight:800;color:#fff}
        .bcell p{font-size:12.5px;color:var(--dim)}
        .bcell .go{margin-top:auto;font-size:12.5px;font-weight:700;color:var(--violet)}
        .bfeat{grid-column:span 2;background:linear-gradient(160deg,rgba(19,15,40,.5),rgba(11,10,24,.86)),url('{{ asset('assets/media/hustle/background.jpg') }}') center/cover no-repeat;border-color:var(--line2);justify-content:flex-end;min-height:220px}
        .bfeat h3{font-size:clamp(20px,2.6vw,28px);font-weight:800;color:#fff;line-height:1.2}
        .bfeat p{color:var(--muted);font-size:14px}
        .bfeat .btn-primary{align-self:flex-start;margin-top:8px;padding:11px 22px}

        /* ===== Rail game (scroll-snap + ranking) ===== */
        .rail{display:flex;gap:16px;overflow-x:auto;scroll-snap-type:x mandatory;padding:8px 4px 18px;scrollbar-width:none}
        .rail::-webkit-scrollbar{display:none}
        .rcard{scroll-snap-align:start;flex:0 0 210px;background:var(--panel);border:1px solid var(--line);border-radius:20px;padding:16px;position:relative;transition:.18s}
        .rcard:hover{transform:translateY(-4px);border-color:rgba(139,92,246,.5);box-shadow:0 18px 40px rgba(124,58,237,.22)}
        .rcard .rank{position:absolute;top:6px;right:12px;font-size:52px;font-weight:800;line-height:1;color:transparent;-webkit-text-stroke:1.5px rgba(167,139,250,.35);pointer-events:none}
        .rcard .art{width:74px;height:74px;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:34px;box-shadow:0 12px 26px rgba(0,0,0,.4);margin-bottom:14px;overflow:hidden}
        .rcard .art img{width:100%;height:100%;object-fit:cover}
        .rcard h4{font-size:14.5px;font-weight:800;color:#fff;line-height:1.25;min-height:36px}
        .rcard .price{font-size:12px;color:var(--dim);margin-top:6px}
        .rcard .price b{color:var(--gold);font-size:13px}
        .rcard .cta{display:inline-flex;margin-top:12px;font-size:12.5px;font-weight:700;color:var(--violet)}

        /* ===== Band statistik (konten baru pengganti banner promo) ===== */
        .band{border-radius:26px;border:1px solid var(--line2);overflow:hidden;position:relative;
            background:linear-gradient(120deg,rgba(13,11,28,.9),rgba(19,15,40,.68)),url('{{ asset('assets/media/hustle/background.jpg') }}') center/cover no-repeat;
            padding:clamp(28px,4.5vw,52px)}
        .band .kicker{color:var(--gold)}
        .band h3{font-size:clamp(22px,3.2vw,32px);font-weight:800;color:#fff;max-width:520px;line-height:1.2;margin-bottom:8px}
        .band p{color:var(--muted);font-size:14.5px;max-width:460px}
        .statgrid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:26px}
        @media(min-width:760px){.statgrid{grid-template-columns:repeat(4,1fr)}}
        .stat{background:rgba(255,255,255,.05);border:1px solid var(--line);border-radius:18px;padding:18px 20px}
        .stat b{display:block;font-size:clamp(22px,2.8vw,30px);font-weight:800;background:linear-gradient(90deg,#fff,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent}
        .stat span{font-size:12.5px;color:var(--dim);font-weight:600}

        /* ===== Timeline cara top up ===== */
        .tline{display:grid;grid-template-columns:1fr;gap:18px;position:relative}
        @media(min-width:820px){.tline{grid-template-columns:repeat(4,1fr)}
            .tline::before{content:'';position:absolute;top:26px;left:12%;right:12%;height:2px;background:linear-gradient(90deg,transparent,rgba(139,92,246,.5),rgba(139,92,246,.5),transparent)}}
        .tstep{text-align:center;padding:0 10px;position:relative}
        .tstep .dot{width:52px;height:52px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:17px;color:#fff;background:linear-gradient(135deg,#7c3aed,#9d5cf5);box-shadow:0 0 0 7px rgba(124,58,237,.14),0 10px 24px rgba(124,58,237,.4);position:relative;z-index:2}
        .tstep h4{font-size:15px;font-weight:800;color:#fff;margin-bottom:6px}
        .tstep p{font-size:13px;color:var(--muted)}

        /* ===== Kenapa ===== */
        .why{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
        @media(min-width:840px){.why{grid-template-columns:repeat(4,1fr)}}
        .wcard{background:var(--panel);border:1px solid var(--line);border-radius:20px;padding:24px 20px;transition:.18s}
        .wcard:hover{border-color:rgba(139,92,246,.45);background:var(--panel2)}
        .wcard .wi{width:46px;height:46px;border-radius:13px;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.3);color:var(--gold);display:flex;align-items:center;justify-content:center;margin-bottom:14px}
        .wcard h4{font-size:14.5px;font-weight:800;color:#fff;margin-bottom:6px}
        .wcard p{font-size:12.5px;color:var(--dim);line-height:1.55}

        /* ===== Band CTA (konten baru pengganti newsletter) ===== */
        .cta-band{border-radius:26px;border:1px solid var(--line2);overflow:hidden;
            background:linear-gradient(100deg,rgba(13,11,28,.92) 30%,rgba(19,15,40,.7)),url('{{ asset('assets/media/hustle/background.jpg') }}') center/cover no-repeat;
            padding:clamp(28px,4.5vw,50px);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:22px}
        .cta-band h3{font-size:clamp(21px,3vw,30px);font-weight:800;color:#fff;line-height:1.25;max-width:480px}
        .cta-band p{color:var(--muted);font-size:14px;margin-top:8px;max-width:440px}
        .cta-band .acts{display:flex;gap:12px;flex-wrap:wrap}

        /* ===== Footer ===== */
        footer.site2{border-top:1px solid var(--line);margin-top:64px;padding:46px 0 26px;background:linear-gradient(180deg,var(--bg),#080714)}
        .fgrid2{display:grid;grid-template-columns:1fr;gap:28px}
        @media(min-width:760px){.fgrid2{grid-template-columns:2fr 1fr 1fr 1.3fr}}
        footer p.desc{font-size:13px;color:var(--dim);margin-top:12px;max-width:280px}
        footer h6{color:#fff;font-size:12.5px;font-weight:800;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px}
        footer a.fl{display:block;font-size:13.5px;color:var(--muted);margin-bottom:9px}
        footer a.fl:hover{color:#fff}
        .csbtn{display:flex;align-items:center;gap:9px;background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:10px 14px;font-size:13px;font-weight:600;color:#fff;margin-bottom:8px;transition:.15s}
        .csbtn:hover{border-color:rgba(139,92,246,.5)}
        .fbottom{border-top:1px solid var(--line);margin-top:32px;padding-top:20px;text-align:center;font-size:12px;color:var(--dim)}
        .socials{display:flex;gap:10px;margin-top:14px}
        .socials a{width:36px;height:36px;border-radius:11px;background:var(--panel);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:#fff;transition:.15s}
        .socials a:hover{background:var(--purple);border-color:transparent}

        .fab-wa{position:fixed;right:18px;bottom:18px;z-index:60;width:54px;height:54px;border-radius:50%;background:#25d366;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 26px rgba(37,211,102,.5)}
        @media(max-width:820px){nav.desk{display:none}.hamburger{display:flex}}
    </style>
    @include('frontend.partials.cursor')
</head>
<body id="top">

    {{-- ===== Header pill mengambang ===== --}}
    <header class="site" id="siteHeader">
        <div class="container">
            <div class="bar">
                <a href="{{ url('/') }}" class="brand"><img src="{{ asset('assets/media/logos/logo_store.png') }}" alt="HustleStore"><b>Hustle<span>Store</span></b></a>
                <nav class="desk">
                    <a href="#top">Beranda</a>
                    <a href="{{ route('catalog') }}">Produk</a>
                    <a href="#kategori">Kategori</a>
                    <a href="#cara">Cara Top Up</a>
                    <a href="{{ route('page.terms') }}">Ketentuan</a>
                </nav>
                <div class="spacer"></div>
                <a href="{{ route('order.track') }}" class="btn btn-primary" style="padding:10px 18px;font-size:13px">Lacak Pesanan</a>
                <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
            </div>
            <nav class="mnav" id="mnav">
                <a href="{{ route('catalog') }}">Produk</a>
                <a href="#kategori">Kategori</a>
                <a href="#cara">Cara Top Up</a>
                <a href="{{ route('page.terms') }}">Syarat &amp; Ketentuan</a>
                <a href="{{ route('page.privacy') }}">Kebijakan Privasi</a>
                <a href="{{ route('order.track') }}">Lacak Pesanan</a>
            </nav>
        </div>
    </header>

    {{-- ===== HERO full-viewport (background hidup) ===== --}}
    <section class="hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="hero-shade" aria-hidden="true"></div>
        <div class="orb o1" aria-hidden="true"></div>
        <div class="orb o2" aria-hidden="true"></div>
        <div class="orb o3" aria-hidden="true"></div>
        <span class="spark" style="left:11%;top:22%"></span>
        <span class="spark" style="left:26%;top:64%;animation-delay:1.3s"></span>
        <span class="spark" style="left:46%;top:16%;animation-delay:2.4s"></span>
        <span class="spark" style="left:62%;top:70%;animation-delay:.7s"></span>
        <span class="spark" style="left:78%;top:26%;animation-delay:3.2s"></span>
        <span class="spark" style="left:90%;top:56%;animation-delay:1.9s"></span>
        <div class="container">
            <div class="hgrid">
                <div>
                    <span class="chip">⚡ Top Up Cepat, Aman &amp; Terpercaya</span>
                    <h1>Level up tanpa nunggu. <em>Top up instan</em> semua kebutuhan digitalmu.</h1>
                    <p class="sub">Game, pulsa, paket data, token PLN, e-wallet, sampai streaming — satu tempat, proses 24/7, pembayaran lengkap.</p>
                    <div class="trust">
                        <span>⚡ Proses Instan</span>
                        <span>🛡️ Pembayaran Aman via Midtrans</span>
                        <span>🎧 CS 24/7</span>
                    </div>
                </div>

                {{-- Widget: tag + headline + tab kategori + pilihan produk + CTA (tanpa input tujuan) --}}
                <div class="widget">
                    <span class="wtag">🔥 TOP UP SEKARANG</span>
                    <h3>Mau top up apa hari ini? Pilih &amp; bayar dalam hitungan detik.</h3>
                    <div class="wtabs" id="wtabs">
                        @foreach($tabDefs as $i => $t)
                            <button type="button" class="{{ $i === 0 ? 'on' : '' }}" data-tab="{{ $t['key'] }}">{{ $t['label'] }}</button>
                        @endforeach
                    </div>
                    <div class="wfield">
                        <label>Pilih Produk</label>
                        <div class="ctrl">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="3"/><path d="M7 12h.01M11 12h.01"/></svg>
                            <select id="wSelect" aria-label="Pilih produk"></select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="wGo">Top Up Sekarang 🚀</button>
                    <div class="wnote">Tanpa daftar akun · Nominal &amp; data tujuan diisi di halaman berikutnya</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Marquee pembayaran ===== --}}
    <div class="marq" aria-hidden="true">
        <div class="track">
            @for($r = 0; $r < 2; $r++)
                @foreach($marquee as $mq)<span>{{ $mq }}</span>@endforeach
                <span>PROSES INSTAN 24/7</span>
            @endfor
        </div>
    </div>

    {{-- ===== Bento kategori ===== --}}
    <section class="sec" id="kategori">
        <div class="container">
            <span class="kicker">Kategori</span>
            <div class="head"><h2 class="t">Semua Kebutuhan Digital</h2><a class="link-all" href="{{ route('catalog') }}">Lihat Semua ›</a></div>
            <div class="bento">
                <a class="bcell bfeat" href="{{ route('catalog') }}">
                    <h3>{{ number_format($totalProducts ?? 0, 0, ',', '.') }}+ produk digital siap kirim instan.</h3>
                    <p>{{ $totalBrands ?? 0 }} layanan dari game favorit sampai token listrik.</p>
                    <span class="btn btn-primary">Jelajahi Katalog</span>
                </a>
                @foreach($catCards as $c)
                    <a class="bcell" href="{{ route('catalog') }}">
                        <span class="ic">@include('frontend.partials.icon', ['name' => $c['icon']])</span>
                        <h4>{{ $c['title'] }}</h4>
                        <p>{{ $c['sub'] }}</p>
                        <span class="go">Top up ›</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== Rail game favorit (ranking) ===== --}}
    @if($rail->isNotEmpty())
    <section class="sec" id="produk">
        <div class="container">
            <span class="kicker">Paling Laris</span>
            <div class="head"><h2 class="t">Game Favorit Minggu Ini</h2><a class="link-all" href="{{ route('catalog') }}">Lihat Semua ›</a></div>
        </div>
        <div class="container">
            <div class="rail">
                @foreach($rail as $i => $b)
                    @php $img = $brandImg($b); $g = $grads[$i % count($grads)]; @endphp
                    <a class="rcard" href="{{ route('brand.show', $b->slug) }}">
                        <span class="rank">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="art" style="{{ $img ? '' : 'background:linear-gradient(135deg,'.$g[0].','.$g[1].')' }}">
                            @if($img)<img src="{{ $img }}" alt="{{ $b->name }}" loading="lazy">@else 🎮 @endif
                        </div>
                        <h4>{{ Str::limit($b->name, 30) }}</h4>
                        <div class="price">Mulai <b>{{ ($b->min_price ?? 0) > 0 ? $rupiah($b->min_price) : '-' }}</b></div>
                        <span class="cta">Top Up Sekarang ›</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===== Band statistik (konten baru) ===== --}}
    <section class="sec">
        <div class="container">
            <div class="band">
                <span class="kicker">HustleStore dalam angka</span>
                <h3>Top up tanpa ribet — tanpa daftar akun, langsung proses.</h3>
                <p>Bayar pakai QRIS, e-wallet, atau transfer bank. Status pesanan terkirim otomatis ke email kamu.</p>
                <div class="statgrid">
                    <div class="stat"><b>{{ number_format($totalProducts ?? 0, 0, ',', '.') }}+</b><span>Produk Digital</span></div>
                    <div class="stat"><b>{{ $totalBrands ?? 0 }}+</b><span>Layanan &amp; Game</span></div>
                    <div class="stat"><b>15+</b><span>Metode Pembayaran</span></div>
                    <div class="stat"><b>24/7</b><span>Layanan Nonstop</span></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Cara top up (timeline) ===== --}}
    <section class="sec" id="cara">
        <div class="container">
            <span class="kicker">Semudah Itu</span>
            <div class="head"><h2 class="t">Cara Top Up di HustleStore</h2></div>
            <div class="tline">
                @foreach([['Pilih Produk','Pilih layanan & nominal yang kamu mau'],['Isi Data','Masukkan ID / nomor tujuan dengan benar'],['Bayar','QRIS, e-wallet, VA bank — pilih yang praktis'],['Selesai','Pesanan diproses & status masuk ke email']] as $i => $s)
                    <div class="tstep">
                        <span class="dot">{{ $i + 1 }}</span>
                        <h4>{{ $s[0] }}</h4>
                        <p>{{ $s[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== Kenapa ===== --}}
    <section class="sec">
        <div class="container">
            <span class="kicker">Keunggulan</span>
            <div class="head"><h2 class="t">Kenapa Pilih HustleStore?</h2></div>
            <div class="why">
                @foreach([['bolt','Proses Instan','Pesanan diproses cepat dalam hitungan menit, nonstop 24 jam.'],['tag','Harga Kompetitif','Harga bersahabat untuk semua produk, dari nominal kecil sampai besar.'],['shield','Pembayaran Aman','Transaksi diproses gateway resmi Midtrans — bukan transfer pribadi.'],['headset','CS Responsif','Ada kendala? Tim kami siap bantu lewat WhatsApp kapan saja.']] as $f)
                    <div class="wcard">
                        <div class="wi">@include('frontend.partials.icon', ['name' => $f[0]])</div>
                        <h4>{{ $f[1] }}</h4><p>{{ $f[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== Band CTA (konten baru) ===== --}}
    <section class="sec">
        <div class="container">
            <div class="cta-band">
                <div>
                    <h3>Sudah order? Cek status pesananmu kapan saja.</h3>
                    <p>Masukkan nomor pesanan dari email untuk melihat status real-time — atau tanya langsung ke CS kami.</p>
                </div>
                <div class="acts">
                    <a href="{{ route('order.track') }}" class="btn btn-primary">Lacak Pesanan</a>
                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo HustleStore, saya butuh bantuan') }}" target="_blank" rel="noopener" class="btn btn-line">💬 Chat CS WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Footer ===== --}}
    <footer class="site2" id="bantuan">
        <div class="container">
            <div class="fgrid2">
                <div>
                    <a href="{{ url('/') }}" class="brand" style="display:inline-flex"><img src="{{ asset('assets/media/logos/logo_store.png') }}" alt="HustleStore" style="height:32px"><b style="margin-left:8px">Hustle<span style="color:var(--purple2)">Store</span></b></a>
                    <p class="desc">Platform top up digital terpercaya untuk kebutuhan game, pulsa, streaming, dan produk digital lainnya.</p>
                    <div class="socials">
                        <a href="https://instagram.com/hustlestore" target="_blank" rel="noopener" aria-label="Instagram"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
                        <a href="https://facebook.com/hustlestore" target="_blank" rel="noopener" aria-label="Facebook"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V5h-3c-2.2 0-4 1.8-4 4v2H7v4h3v6h4v-6h3l1-4h-4V9c0-.6.4-1 1-1z"/></svg></a>
                        <a href="https://tiktok.com/@hustlestore" target="_blank" rel="noopener" aria-label="TikTok"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M16 3c.3 2 1.6 3.6 3.6 3.9V10c-1.4 0-2.7-.4-3.6-1.1V15a5.5 5.5 0 1 1-5.5-5.5c.3 0 .6 0 .9.1v3a2.5 2.5 0 1 0 1.7 2.4V3H16z"/></svg></a>
                        <a href="https://youtube.com/@hustlestore" target="_blank" rel="noopener" aria-label="YouTube"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12s0-3-.4-4.4a2.5 2.5 0 0 0-1.8-1.8C18.4 5.4 12 5.4 12 5.4s-6.4 0-7.8.4A2.5 2.5 0 0 0 2.4 7.6C2 9 2 12 2 12s0 3 .4 4.4a2.5 2.5 0 0 0 1.8 1.8c1.4.4 7.8.4 7.8.4s6.4 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8C22 15 22 12 22 12zM10 15V9l5 3z"/></svg></a>
                    </div>
                </div>
                <div>
                    <h6>Produk</h6>
                    <a class="fl" href="{{ route('catalog') }}">Top Up Game</a><a class="fl" href="{{ route('catalog') }}">Pulsa &amp; Data</a>
                    <a class="fl" href="{{ route('catalog') }}">Token PLN</a><a class="fl" href="{{ route('catalog') }}">E-Wallet</a>
                    <a class="fl" href="{{ route('catalog') }}">Streaming &amp; Voucher</a>
                </div>
                <div>
                    <h6>Informasi</h6>
                    <a class="fl" href="#cara">Cara Top Up</a>
                    <a class="fl" href="{{ route('page.terms') }}">Syarat &amp; Ketentuan</a>
                    <a class="fl" href="{{ route('page.privacy') }}">Kebijakan Privasi</a>
                    <a class="fl" href="{{ route('order.track') }}">Lacak Pesanan</a>
                </div>
                <div>
                    <h6>Customer Support 24/7</h6>
                    <a class="csbtn" href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"><span>🟢</span> WhatsApp CS</a>
                    <a class="csbtn" href="mailto:{{ config('seo.email', 'support@hustlestore.id') }}"><span>✉️</span> {{ config('seo.email', 'support@hustlestore.id') }}</a>
                </div>
            </div>
            <div class="fbottom">© {{ date('Y') }} HustleStore. All rights reserved. — HustleStore tidak berafiliasi dengan pihak game/penyedia layanan mana pun.</div>
        </div>
    </footer>

    <a class="fab-wa" href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo HustleStore, saya mau bertanya') }}" target="_blank" rel="noopener" aria-label="WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2z"/></svg>
    </a>

    <script>
        // Widget: tab kategori -> isi pilihan produk -> menuju halaman brand
        const CATBRANDS = @json($tabData);
        const wSelect = document.getElementById('wSelect');
        function fillSelect(key){
            const list = CATBRANDS[key] || [];
            wSelect.innerHTML = list.map(b => '<option value="'+b.s+'">'+b.n+'</option>').join('');
        }
        document.querySelectorAll('#wtabs button').forEach(btn => btn.addEventListener('click', function(){
            document.querySelectorAll('#wtabs button').forEach(b => b.classList.remove('on'));
            this.classList.add('on');
            fillSelect(this.dataset.tab);
        }));
        const firstTab = document.querySelector('#wtabs button');
        if(firstTab) fillSelect(firstTab.dataset.tab);
        document.getElementById('wGo').addEventListener('click', function(){
            if(!wSelect.value){ alert('Pilih produk dulu ya.'); return; }
            window.location.href = '{{ url('/produk') }}/' + wSelect.value;
        });

        // Nav mobile + header solid saat scroll
        const h = document.getElementById('hamburger'), m = document.getElementById('mnav');
        if(h) h.addEventListener('click', () => { const o = m.classList.toggle('open'); h.setAttribute('aria-expanded', o); });
        const hdr = document.getElementById('siteHeader');
        const onScroll = () => hdr.classList.toggle('solid', window.scrollY > 30);
        window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
    </script>
</body>
</html>
