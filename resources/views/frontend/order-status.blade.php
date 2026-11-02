@extends('frontend.shop')
@section('title', 'Status Pesanan ' . $order->order_number . ' — HustleStore')
@section('robots', 'noindex, nofollow')

@section('content')
    @php
        $map = [
            'pending'    => ['b-amber', '⏳', 'Menunggu Pembayaran'],
            'paid'       => ['b-blue',  '💰', 'Lunas — Menunggu Diproses'],
            'processing' => ['b-blue',  '⚙️', 'Sedang Diproses'],
            'success'    => ['b-green', '✅', 'Berhasil'],
            'failed'     => ['b-red',   '⚠️', 'Gagal'],
            'expired'    => ['b-red',   '⌛', 'Kedaluwarsa'],
            'cancelled'  => ['b-red',   '✖️', 'Dibatalkan'],
        ];
        $s = $map[$order->status] ?? ['b-amber', 'ℹ️', $order->status_label];
    @endphp

    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <span>Status Pesanan</span></div>
    <h1 class="page">Status Pesanan</h1>

    <div class="card" style="margin-top:10px">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:16px">
            <div style="font-size:40px">{{ $s[1] }}</div>
            <div style="flex:1;min-width:160px">
                <div style="font-size:13px;color:var(--dim)">No. Pesanan</div>
                <div style="font-weight:800;font-size:18px">{{ $order->order_number }}</div>
            </div>
            <span class="badge {{ $s[0] }}" style="font-size:13px;padding:8px 16px">{{ $s[2] }}</span>
        </div>

        <div class="row" style="display:flex;justify-content:space-between;padding:9px 0;border-top:1px solid var(--line)"><span style="color:var(--muted)">Produk</span><b>{{ $order->brand_name }} — {{ $order->product_name }}</b></div>
        <div class="row" style="display:flex;justify-content:space-between;padding:9px 0;border-top:1px solid var(--line)"><span style="color:var(--muted)">{{ $order->target_label ?: 'Tujuan' }}</span><b>{{ $order->target }}</b></div>
        <div class="row" style="display:flex;justify-content:space-between;padding:9px 0;border-top:1px solid var(--line)"><span style="color:var(--muted)">Total</span><b style="color:#ffd77a">Rp {{ number_format($order->amount, 0, ',', '.') }}</b></div>
        <div class="row" style="display:flex;justify-content:space-between;padding:9px 0;border-top:1px solid var(--line)"><span style="color:var(--muted)">Email</span><b>{{ $order->customer_email }}</b></div>
        <div class="row" style="display:flex;justify-content:space-between;padding:9px 0;border-top:1px solid var(--line)"><span style="color:var(--muted)">Tanggal</span><b>{{ $order->created_at->format('d M Y H:i') }}</b></div>

        @if($order->status === 'success' && $order->serial_number)
            <div style="margin-top:16px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.4);border-radius:12px;padding:14px">
                <div style="font-size:12px;color:var(--dim);margin-bottom:4px">Serial Number / Catatan</div>
                <div style="font-weight:800;color:#7ef0a6;word-break:break-word">{{ $order->serial_number }}</div>
            </div>
        @endif

        @if($order->status === 'failed')
            <div class="alert alert-danger" style="margin-top:16px">Pesanan gagal diproses. {{ $order->admin_note }} Tim kami akan menghubungi Anda untuk pengembalian dana.</div>
        @endif

        <div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
            @if($order->status === 'pending' && $order->snap_token)
                <a href="{{ route('order.pay', $order->order_number) }}" class="btn btn-primary">Lanjutkan Pembayaran</a>
            @endif
            <a href="{{ route('catalog') }}" class="btn btn-ghost">Belanja Lagi</a>
            <a href="https://wa.me/{{ config('seo.whatsapp','6281234567890') }}?text={{ urlencode('Halo HustleStore, saya mau tanya pesanan '.$order->order_number) }}" target="_blank" rel="noopener" class="btn btn-ghost">Hubungi CS</a>
        </div>
    </div>

    @if(in_array($order->status, ['pending','paid','processing']))
        <p style="text-align:center;color:var(--dim);font-size:12.5px;margin-top:14px">Halaman ini menyegar otomatis untuk memperbarui status.</p>
        <script>setTimeout(function(){ location.reload(); }, 20000);</script>
    @endif
@endsection
