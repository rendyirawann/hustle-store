<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran {{ $order->order_number }} — HustleStore</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset(config('seo.favicon', 'assets/media/logos/favicon.png')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:radial-gradient(900px 400px at 50% -120px,rgba(138,63,252,.35),transparent 70%),linear-gradient(180deg,#1c0733,#26093f);color:#f4eeff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .box{max-width:440px;width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:28px;text-align:center}
        img{height:44px;margin:0 auto 16px}
        h1{font-size:19px;margin-bottom:6px}
        p{color:#c3aee0;font-size:14px;margin-bottom:18px}
        .row{display:flex;justify-content:space-between;font-size:14px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.08)}
        .row b{font-weight:700}
        .total{font-size:22px;font-weight:800;color:#ffd77a;margin:14px 0 20px}
        .btn{display:inline-block;width:100%;background:linear-gradient(135deg,#8a3ffc,#b14aed);color:#fff;border:0;border-radius:999px;padding:14px;font-weight:800;font-size:15px;cursor:pointer;box-shadow:0 10px 24px rgba(138,63,252,.45)}
        .muted{color:#9c86bf;font-size:12px;margin-top:14px}
        a.link{color:#cbb2ff}
    </style>
    @include('frontend.partials.cursor')
</head>
<body>
    <div class="box">
        <img src="{{ asset('assets/media/logos/logo_store.png') }}" alt="HustleStore">
        <h1>Selesaikan Pembayaran</h1>
        <p>Pesanan <b>{{ $order->order_number }}</b></p>
        <div class="row"><span>Produk</span><b>{{ $order->brand_name }}</b></div>
        <div class="row"><span>Item</span><b>{{ $order->product_name }}</b></div>
        <div class="row"><span>{{ $order->target_label ?: 'Tujuan' }}</span><b>{{ $order->target }}</b></div>
        <div class="total">Rp {{ number_format($order->amount, 0, ',', '.') }}</div>
        <button class="btn" id="payBtn">Bayar Sekarang</button>
        <div class="muted">Setelah bayar, status &amp; bukti dikirim ke <b>{{ $order->customer_email }}</b>.<br>
            <a class="link" href="{{ route('order.status', $order->order_number) }}">Lihat status pesanan</a>
        </div>
    </div>

    <script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        const statusUrl = "{{ route('order.status', $order->order_number) }}";
        function goStatus(){ window.location.href = statusUrl; }
        function openSnap(){
            snap.pay("{{ $snapToken }}", {
                onSuccess: goStatus,
                onPending: goStatus,
                onError: function(){ alert('Pembayaran gagal / dibatalkan.'); },
                onClose: function(){ /* biarkan di halaman ini */ }
            });
        }
        document.getElementById('payBtn').addEventListener('click', openSnap);
        // Otomatis buka popup pembayaran saat halaman dibuka
        window.addEventListener('load', function(){ setTimeout(openSnap, 400); });
    </script>
</body>
</html>
