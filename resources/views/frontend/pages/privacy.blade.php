@extends('frontend.shop')
@section('title', 'Kebijakan Privasi — ' . config('seo.name', 'HustleStore'))

@section('styles')
    .legal h2{font-size:16px;font-weight:800;color:#fff;margin:26px 0 8px}
    .legal p,.legal li{font-size:14px;color:var(--muted);line-height:1.75}
    .legal ul{padding-left:20px;margin:6px 0}
@endsection

@section('content')
    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <span>Kebijakan Privasi</span></div>
    <h1 class="page">Kebijakan Privasi</h1>
    <p style="color:var(--dim);font-size:12.5px">Terakhir diperbarui: {{ config('app.terms_updated_at', '11 Juli 2026') }}</p>

    <div class="card legal" style="margin-top:14px;max-width:860px">
        <p><b>HustleStore</b> menghargai privasi Anda. Kebijakan ini menjelaskan data apa yang kami kumpulkan, bagaimana digunakan, dan hak Anda atasnya.</p>

        <h2>1. Data yang Kami Kumpulkan</h2>
        <ul>
            <li><b>Data pesanan:</b> nama (opsional), alamat email, nomor WhatsApp (opsional), serta data tujuan top up (User ID game / nomor HP / ID pelanggan).</li>
            <li><b>Data pembayaran:</b> diproses oleh Midtrans. Kami <b>tidak</b> menyimpan nomor kartu atau kredensial pembayaran Anda.</li>
            <li><b>Data teknis:</b> alamat IP dan informasi peramban untuk keamanan dan pencegahan penipuan.</li>
        </ul>

        <h2>2. Penggunaan Data</h2>
        <ul>
            <li>Memproses dan mengirimkan pesanan Anda.</li>
            <li>Mengirim notifikasi status pesanan melalui email.</li>
            <li>Menghubungi Anda bila ada kendala pesanan (via email/WhatsApp).</li>
            <li>Keamanan, audit, dan pemenuhan kewajiban hukum.</li>
        </ul>

        <h2>3. Pembagian Data</h2>
        <ul>
            <li>Data pembayaran dibagikan ke <b>Midtrans</b> sebatas untuk memproses transaksi.</li>
            <li>Data tujuan top up diteruskan ke penyedia produk sebatas untuk memenuhi pesanan.</li>
            <li>Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak mana pun.</li>
        </ul>

        <h2>4. Penyimpanan &amp; Keamanan</h2>
        <p>Data disimpan di server kami dengan pembatasan akses. Riwayat pesanan disimpan untuk keperluan pembukuan dan layanan purnajual.</p>

        <h2>5. Hak Anda</h2>
        <p>Anda berhak meminta salinan, koreksi, atau penghapusan data pribadi Anda (sepanjang tidak bertentangan dengan kewajiban pencatatan transaksi) dengan menghubungi <a href="mailto:{{ config('seo.email', 'support@hustlestore.id') }}" style="color:#c4b5fd">{{ config('seo.email', 'support@hustlestore.id') }}</a>.</p>

        <h2>6. Cookie</h2>
        <p>Kami menggunakan cookie sesi yang diperlukan untuk keamanan formulir (CSRF) dan kelancaran transaksi. Tidak ada cookie iklan pihak ketiga.</p>

        <h2>7. Perubahan Kebijakan</h2>
        <p>Kebijakan ini dapat diperbarui sewaktu-waktu. Perubahan berlaku sejak dipublikasikan di halaman ini.</p>
    </div>
@endsection
