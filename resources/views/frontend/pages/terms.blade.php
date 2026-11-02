@extends('frontend.shop')
@section('title', 'Syarat & Ketentuan — ' . config('seo.name', 'HustleStore'))

@section('styles')
    .legal h2{font-size:16px;font-weight:800;color:#fff;margin:26px 0 8px}
    .legal p,.legal li{font-size:14px;color:var(--muted);line-height:1.75}
    .legal ul{padding-left:20px;margin:6px 0}
    .legal .upd{font-size:12.5px;color:var(--dim)}
@endsection

@section('content')
    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <span>Syarat &amp; Ketentuan</span></div>
    <h1 class="page">Syarat &amp; Ketentuan</h1>
    <p class="upd" style="color:var(--dim);font-size:12.5px">Terakhir diperbarui: {{ config('app.terms_updated_at', '11 Juli 2026') }}</p>

    <div class="card legal" style="margin-top:14px;max-width:860px">
        <p>Dengan mengakses dan bertransaksi di <b>HustleStore</b> ("kami"), Anda dianggap telah membaca, memahami, dan menyetujui seluruh syarat &amp; ketentuan di bawah ini.</p>

        <h2>1. Layanan</h2>
        <ul>
            <li>HustleStore menyediakan layanan pembelian produk digital: top up game, pulsa, paket data, token PLN, e-money, voucher, dan langganan streaming.</li>
            <li>Pesanan diproses setelah pembayaran <b>terkonfirmasi lunas</b>. Estimasi proses umumnya 1–15 menit dan dapat lebih lama saat gangguan pihak penyedia.</li>
            <li>Kami berhak menolak / membatalkan pesanan yang terindikasi penipuan atau penyalahgunaan.</li>
        </ul>

        <h2>2. Kewajiban Pembeli</h2>
        <ul>
            <li>Anda wajib mengisi data tujuan (User ID, nomor HP, ID pelanggan, email, dsb.) dengan <b>benar dan teliti</b>. Kesalahan pengisian data tujuan berada di luar tanggung jawab kami dan dana tidak dapat dikembalikan apabila produk telah terkirim ke tujuan yang Anda isi.</li>
            <li>Anda wajib menggunakan email aktif untuk menerima status dan bukti pesanan.</li>
            <li>Dilarang menggunakan layanan untuk aktivitas yang melanggar hukum.</li>
        </ul>

        <h2>3. Pembayaran</h2>
        <ul>
            <li>Pembayaran diproses melalui payment gateway <b>Midtrans</b> dengan berbagai metode (QRIS, e-wallet, virtual account, dan lainnya).</li>
            <li>Harga yang berlaku adalah harga yang tercantum pada saat checkout.</li>
            <li>Pesanan yang tidak dibayar hingga batas waktu akan kedaluwarsa otomatis.</li>
        </ul>

        <h2>4. Pembatalan &amp; Pengembalian Dana (Refund)</h2>
        <ul>
            <li>Pesanan yang sudah lunas dan <b>berhasil diproses</b> tidak dapat dibatalkan/dikembalikan.</li>
            <li>Apabila pesanan gagal diproses dari sisi kami/penyedia, dana akan dikembalikan penuh melalui metode yang disepakati bersama CS.</li>
            <li>Klaim kendala pesanan wajib disampaikan maksimal 1×24 jam melalui WhatsApp CS dengan menyertakan nomor pesanan.</li>
        </ul>

        <h2>5. Batasan Tanggung Jawab</h2>
        <ul>
            <li>HustleStore bukan afiliasi dari penerbit game/operator/penyedia layanan mana pun. Seluruh merek dagang milik pemiliknya masing-masing.</li>
            <li>Kami tidak bertanggung jawab atas sanksi/banned akun yang timbul dari kebijakan penerbit game.</li>
        </ul>

        <h2>6. Perubahan Ketentuan</h2>
        <p>Kami dapat mengubah syarat &amp; ketentuan ini sewaktu-waktu tanpa pemberitahuan terlebih dahulu. Versi terbaru selalu tersedia di halaman ini.</p>

        <h2>7. Kontak</h2>
        <p>Pertanyaan mengenai ketentuan ini dapat dikirim ke <a href="mailto:{{ config('seo.email', 'support@hustlestore.id') }}" style="color:#c4b5fd">{{ config('seo.email', 'support@hustlestore.id') }}</a> atau WhatsApp CS kami.</p>
    </div>
@endsection
