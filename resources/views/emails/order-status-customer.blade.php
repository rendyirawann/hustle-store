@php
    $rp = fn($v) => 'Rp ' . number_format((int)$v, 0, ',', '.');
    $conf = [
        'received' => ['title' => '🧾 Pembayaran Diterima', 'msg' => 'Terima kasih! Pembayaran Anda sudah kami terima dan pesanan sedang kami proses. Anda akan menerima email lagi saat pesanan selesai.', 'color' => '#2563EB'],
        'success'  => ['title' => '✅ Pesanan Berhasil', 'msg' => 'Pesanan Anda telah berhasil diproses. Detail/serial number (jika ada) tercantum di bawah. Terima kasih telah berbelanja di HustleStore!', 'color' => '#16a34a'],
        'failed'   => ['title' => '⚠️ Pesanan Gagal', 'msg' => 'Mohon maaf, pesanan Anda tidak dapat diproses. Tim kami akan menghubungi Anda terkait pengembalian dana. Silakan hubungi CS bila ada pertanyaan.', 'color' => '#dc2626'],
    ][$type] ?? ['title' => 'Update Pesanan', 'msg' => '', 'color' => '#7C3AED'];
@endphp
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:{{ $conf['color'] }};color:#fff;padding:20px 24px;border-radius:14px 14px 0 0;">
            <h2 style="margin:0;font-size:18px;">{{ $conf['title'] }}</h2>
            <p style="margin:6px 0 0;opacity:.92;font-size:13px;">Pesanan {{ $order->order_number }}</p>
        </div>
        <div style="background:#fff;padding:24px;border-radius:0 0 14px 14px;box-shadow:0 4px 16px rgba(0,0,0,.06);">
            <p style="font-size:14px;line-height:1.6;margin-top:0;">{{ $conf['msg'] }}</p>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr><td style="padding:8px 0;color:#64748b;">Produk</td><td style="padding:8px 0;text-align:right;">{{ $order->brand_name }} — {{ $order->product_name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">{{ $order->target_label ?: 'Tujuan' }}</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $order->target }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Total</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $rp($order->amount) }}</td></tr>
                @if($type === 'success' && $order->serial_number)
                    <tr style="background:#f0fdf4;"><td style="padding:10px 8px;color:#64748b;">Serial / Catatan</td><td style="padding:10px 8px;text-align:right;font-weight:bold;color:#16a34a;">{{ $order->serial_number }}</td></tr>
                @endif
            </table>
            <div style="margin-top:20px;text-align:center;">
                <a href="{{ url('/order/' . $order->order_number) }}" style="display:inline-block;background:{{ $conf['color'] }};color:#fff;text-decoration:none;padding:12px 26px;border-radius:999px;font-weight:bold;">Lihat Status Pesanan</a>
            </div>
        </div>
        <p style="text-align:center;color:#94a3b8;font-size:12px;margin-top:16px;">HustleStore — {{ config('seo.email', 'support@hustlestore.id') }}</p>
    </div>
</body>
</html>
