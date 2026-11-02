@extends('backend.layout.app')
@section('title', 'Pesanan')

@section('content')
<div class="mt-5 mb-10">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-6">
            <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
            <div class="d-flex flex-column"><span>{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger p-5 mb-6">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 d-flex flex-wrap align-items-center justify-content-between">
            <h3 class="card-title fw-bold fs-2 m-0"><i class="ki-outline ki-handcart fs-2 me-2 text-primary"></i>Pesanan</h3>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm form-control-solid" placeholder="cari no/email/tujuan/produk" style="min-width:240px">
                <button class="btn btn-sm btn-light-primary"><i class="ki-outline ki-magnifier fs-4"></i></button>
            </form>
        </div>
        <div class="card-body pt-2">
            {{-- Filter status --}}
            <div class="d-flex flex-wrap gap-2 mb-6">
                <a href="{{ route('orders.index') }}" class="btn btn-sm {{ !$current ? 'btn-primary' : 'btn-light' }}">Semua</a>
                @foreach($statuses as $sk => $sl)
                    <a href="{{ route('orders.index', ['status' => $sk]) }}" class="btn btn-sm {{ $current === $sk ? 'btn-primary' : 'btn-light' }}">
                        {{ $sl }} <span class="badge badge-circle badge-secondary ms-1">{{ $counts[$sk] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-200 align-middle gs-4 gy-3">
                    <thead>
                        <tr class="fw-bold fs-7 text-uppercase text-gray-600 bg-light-primary">
                            <th>No. Pesanan</th><th>Produk</th><th>Tujuan</th><th>Pembeli</th>
                            <th class="text-end">Total</th><th class="text-center">Status</th><th>Tanggal</th><th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td class="fw-bold">{{ $o->order_number }}</td>
                                <td>{{ $o->brand_name }}<div class="text-muted fs-8">{{ $o->product_name }}</div></td>
                                <td class="fw-bold text-primary">{{ $o->target }}</td>
                                <td class="fs-7">{{ $o->customer_email }}<div class="text-muted fs-8">{{ $o->customer_whatsapp }}</div></td>
                                <td class="text-end fw-bold">Rp {{ number_format($o->amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php $badge = ['pending'=>'warning','paid'=>'info','processing'=>'info','success'=>'success','failed'=>'danger','expired'=>'danger','cancelled'=>'danger'][$o->status] ?? 'light'; @endphp
                                    <span class="badge badge-light-{{ $badge }}">{{ $o->status_label }}</span>
                                </td>
                                <td class="fs-8 text-muted">{{ $o->created_at->format('d M Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $o->id) }}" class="btn btn-sm btn-light-primary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-10">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection
