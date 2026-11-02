@extends('backend.layout.app')
@section('title', 'Pesanan ' . $order->order_number)

@section('content')
@php $rp = fn($v) => 'Rp ' . number_format((int)$v, 0, ',', '.'); @endphp
<div class="mt-5 mb-10">
    @if(session('success'))<div class="alert alert-success p-5 mb-6">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger p-5 mb-6">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger p-5 mb-6"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light mb-4"><i class="ki-outline ki-arrow-left fs-4"></i>Kembali</a>

    <div class="row g-6">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-6 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold fs-3 m-0">{{ $order->order_number }}</h3>
                    @php $badge = ['pending'=>'warning','paid'=>'info','processing'=>'info','success'=>'success','failed'=>'danger','expired'=>'danger','cancelled'=>'danger'][$order->status] ?? 'light'; @endphp
                    <span class="badge badge-light-{{ $badge }} fs-6">{{ $order->status_label }}</span>
                </div>
                <div class="card-body pt-2">
                    <table class="table table-row-dashed align-middle">
                        <tr><td class="text-muted">Produk</td><td class="text-end fw-bold">{{ $order->brand_name }} — {{ $order->product_name }}</td></tr>
                        <tr><td class="text-muted">Kode Produk</td><td class="text-end fw-bold">{{ $order->product_code }}</td></tr>
                        <tr class="bg-light-primary"><td class="text-muted">{{ $order->target_label ?: 'Tujuan' }}</td><td class="text-end fw-bold fs-4 text-primary">{{ $order->target }}</td></tr>
                        <tr><td class="text-muted">Harga Jual (dibayar)</td><td class="text-end fw-bold">{{ $rp($order->amount) }}</td></tr>
                        <tr><td class="text-muted">Modal</td><td class="text-end">{{ $rp($order->cost_price) }}</td></tr>
                        <tr><td class="text-muted">Estimasi Untung</td><td class="text-end fw-bold text-success">{{ $rp($order->profit) }}</td></tr>
                        <tr><td class="text-muted">Status Pembayaran</td><td class="text-end">{{ strtoupper($order->payment_status) }} @if($order->payment_type)· {{ strtoupper($order->payment_type) }}@endif</td></tr>
                        @if($order->va_number)<tr><td class="text-muted">VA Number</td><td class="text-end">{{ $order->va_number }}</td></tr>@endif
                        <tr><td class="text-muted">Pembeli</td><td class="text-end">{{ $order->customer_name ?: '-' }}<br>{{ $order->customer_email }}<br>{{ $order->customer_whatsapp }}</td></tr>
                        <tr><td class="text-muted">Dibuat</td><td class="text-end">{{ $order->created_at->format('d M Y H:i') }}</td></tr>
                        @if($order->paid_at)<tr><td class="text-muted">Dibayar</td><td class="text-end">{{ $order->paid_at->format('d M Y H:i') }}</td></tr>@endif
                        @if($order->completed_at)<tr><td class="text-muted">Selesai</td><td class="text-end">{{ $order->completed_at->format('d M Y H:i') }}</td></tr>@endif
                        @if($order->serial_number)<tr class="bg-light-success"><td class="text-muted">Serial / Catatan</td><td class="text-end fw-bold">{{ $order->serial_number }}</td></tr>@endif
                        @if($order->admin_note)<tr><td class="text-muted">Catatan Admin</td><td class="text-end">{{ $order->admin_note }}</td></tr>@endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-6"><h3 class="card-title fw-bold fs-4 m-0">Aksi Fulfillment</h3></div>
                <div class="card-body pt-2">
                    @if(! $order->isPaid())
                        <div class="alert alert-warning">Pesanan belum lunas. Tidak ada aksi hingga pembayaran diterima.</div>
                    @elseif($order->status === 'success')
                        <div class="alert alert-success">Pesanan sudah <b>BERHASIL</b> diselesaikan.</div>
                    @elseif($order->status === 'failed')
                        <div class="alert alert-danger">Pesanan ditandai <b>GAGAL</b>.</div>
                    @else
                        <div class="notice bg-light-primary rounded border-primary border border-dashed p-4 mb-5 fs-7">
                            <b>Alur:</b> Lakukan top up manual di VIP Reseller untuk <b>{{ $order->product_code }}</b> ke <b>{{ $order->target }}</b>, lalu tandai <b>Berhasil</b> (isi SN bila ada). Pelanggan otomatis dapat email.
                        </div>

                        @if($order->status === 'paid')
                            <form action="{{ route('orders.process', $order->id) }}" method="POST" class="mb-4">
                                @csrf
                                <button class="btn btn-light-primary w-100"><i class="ki-outline ki-time fs-3"></i>Tandai Sedang Diproses</button>
                            </form>
                        @endif

                        <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="mb-5"
                              onsubmit="return confirm('Tandai pesanan BERHASIL & kirim email ke pelanggan?');">
                            @csrf
                            <label class="form-label fw-semibold">Serial Number / Catatan (opsional)</label>
                            <textarea name="serial_number" class="form-control form-control-solid mb-2" rows="2" placeholder="SN / voucher code / catatan hasil top up"></textarea>
                            <textarea name="admin_note" class="form-control form-control-solid mb-3" rows="1" placeholder="Catatan internal (opsional)"></textarea>
                            <button class="btn btn-success w-100"><i class="ki-outline ki-check-circle fs-3"></i>Tandai Berhasil</button>
                        </form>

                        <form action="{{ route('orders.fail', $order->id) }}" method="POST"
                              onsubmit="return confirm('Tandai pesanan GAGAL & kirim email ke pelanggan?');">
                            @csrf
                            <label class="form-label fw-semibold">Alasan Gagal (wajib)</label>
                            <textarea name="admin_note" class="form-control form-control-solid mb-3" rows="2" placeholder="cth: ID tujuan salah / stok kosong — dana akan direfund" required></textarea>
                            <button class="btn btn-light-danger w-100"><i class="ki-outline ki-cross-circle fs-3"></i>Tandai Gagal</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
