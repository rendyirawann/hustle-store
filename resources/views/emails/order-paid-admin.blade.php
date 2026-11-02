@php $rp = fn($v) => 'Rp ' . number_format((int)$v, 0, ',', '.'); @endphp
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:linear-gradient(135deg,#7C3AED,#2563EB);color:#fff;padding:20px 24px;border-radius:14px 14px 0 0;">
            <h2 style="margin:0;font-size:18px;">🔔 Pesanan LUNAS — Siap Diproses</h2>
            <p style="margin:6px 0 0;opacity:.9;font-size:13px;">Segera lakukan top up manual di VIP Reseller.</p>
        </div>
        <div style="background:#fff;padding:24px;border-radius:0 0 14px 14px;box-shadow:0 4px 16px rgba(0,0,0,.06);">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr><td style="padding:8px 0;color:#64748b;">No. Pesanan</td><td style="padding:8px 0;font-weight:bold;text-align:right;">{{ $order->order_number }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Produk</td><td style="padding:8px 0;text-align:right;">{{ $order->brand_name }} — {{ $order->product_name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Kode</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $order->product_code }}</td></tr>
                <tr style="background:#faf5ff;"><td style="padding:10px 8px;color:#64748b;">{{ $order->target_label ?: 'Tujuan' }}</td><td style="padding:10px 8px;text-align:right;font-weight:bold;font-size:16px;color:#7C3AED;">{{ $order->target }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Dibayar</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $rp($order->amount) }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Modal</td><td style="padding:8px 0;text-align:right;">{{ $rp($order->cost_price) }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Estimasi Untung</td><td style="padding:8px 0;text-align:right;font-weight:bold;color:#16a34a;">{{ $rp($order->profit) }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Pembayaran</td><td style="padding:8px 0;text-align:right;">{{ strtoupper($order->payment_type ?? '-') }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Email Pembeli</td><td style="padding:8px 0;text-align:right;">{{ $order->customer_email }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">WhatsApp</td><td style="padding:8px 0;text-align:right;">{{ $order->customer_whatsapp ?: '-' }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Waktu Bayar</td><td style="padding:8px 0;text-align:right;">{{ optional($order->paid_at)->format('d M Y H:i') }}</td></tr>
            </table>
            <div style="margin-top:20px;text-align:center;">
                <a href="{{ url('/admin/orders/' . $order->id) }}" style="display:inline-block;background:#7C3AED;color:#fff;text-decoration:none;padding:12px 26px;border-radius:999px;font-weight:bold;">Proses Pesanan di Admin</a>
            </div>
        </div>
        <p style="text-align:center;color:#94a3b8;font-size:12px;margin-top:16px;">HustleStore — notifikasi otomatis.</p>
    </div>
</body>
</html>
