@extends('frontend.shop')
@section('title', 'Top Up ' . $brand->name . ' — ' . config('seo.name', 'HustleStore'))

@section('styles')
    .ptiles{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:6px}
    @media(min-width:640px){.ptiles{grid-template-columns:repeat(3,1fr)}}
    .ptile{position:relative;text-align:left;background:rgba(255,255,255,.05);border:1.5px solid var(--line);border-radius:14px;padding:14px 14px 12px;cursor:pointer;color:var(--text);transition:.15s}
    .ptile:hover{border-color:var(--purple2);transform:translateY(-2px)}
    .ptile.sel{border-color:var(--purple);background:rgba(138,63,252,.18);box-shadow:0 8px 22px rgba(138,63,252,.35)}
    .ptile.disabled{opacity:.45;cursor:not-allowed}
    .ptile .pn{font-weight:700;font-size:13.5px;line-height:1.25;margin-bottom:8px;min-height:34px}
    .ptile .pp{font-weight:800;color:#ffd77a;font-size:14px}
    .ptile .chk{position:absolute;top:8px;right:8px;width:20px;height:20px;border-radius:50%;background:var(--purple);color:#fff;display:none;align-items:center;justify-content:center;font-size:12px}
    .ptile.sel .chk{display:flex}
    .summary{position:sticky;bottom:0;margin-top:22px;background:rgba(28,7,51,.96);backdrop-filter:blur(8px);border:1px solid var(--line);border-radius:16px;padding:16px;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
    .summary .info{flex:1;min-width:180px}
    .summary .t{font-size:12px;color:var(--dim)}
    .summary .v{font-size:20px;font-weight:800}
@endsection

@section('content')
    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <a href="{{ route('catalog') }}">Produk</a> › <span>{{ $brand->name }}</span></div>
    <h1 class="page">Top Up {{ $brand->name }}</h1>
    <div style="margin-bottom:14px;display:flex;gap:8px;flex-wrap:wrap">
        <span class="badge b-blue">{{ $brand->category_label }}</span>
        <span class="badge b-green">{{ $products->count() }} produk</span>
    </div>

    @if(session('order_error'))<div class="alert alert-danger">{{ session('order_error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ route('order.store') }}" id="orderForm">
        @csrf
        <input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}" required>

        <div class="card">
            <div style="font-weight:800;font-size:15px;margin-bottom:2px">1. Pilih Nominal</div>
            <div class="ptiles">
                @foreach($products as $p)
                    @php $avail = $p->status === 'available'; @endphp
                    <button type="button"
                        class="ptile {{ $avail ? '' : 'disabled' }} {{ (string)old('product_id') === (string)$p->id ? 'sel' : '' }}"
                        @if($avail) onclick="selectProduct(this)" @else disabled title="{{ $p->status_label }}" @endif
                        data-id="{{ $p->id }}" data-price="{{ $p->price }}" data-name="{{ $p->name }}">
                        <span class="chk">✓</span>
                        <div class="pn">{{ $p->name }}</div>
                        <div class="pp">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        @unless($avail)<div style="font-size:11px;color:var(--red);margin-top:4px">{{ $p->status_label }}</div>@endunless
                    </button>
                @endforeach
            </div>
        </div>

        <div class="card" style="margin-top:16px">
            <div style="font-weight:800;font-size:15px;margin-bottom:2px">2. Data Tujuan &amp; Kontak</div>
            <label class="fld">{{ $brand->input_label ?: 'Nomor Tujuan' }} <span style="color:var(--red)">*</span></label>
            <input class="inp" name="target" value="{{ old('target') }}" placeholder="{{ $brand->input_label ?: 'Masukkan tujuan' }}" required>

            <label class="fld">Nama (opsional)</label>
            <input class="inp" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nama Anda">

            <div style="display:grid;grid-template-columns:1fr;gap:0">
                <label class="fld">Email <span style="color:var(--red)">*</span> <span style="color:var(--dim);font-weight:400">(bukti & status pesanan dikirim ke email ini)</span></label>
                <input class="inp" type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="email@contoh.com" required>
            </div>

            <label class="fld">No. WhatsApp (opsional)</label>
            <input class="inp" name="customer_whatsapp" value="{{ old('customer_whatsapp') }}" placeholder="08xxxxxxxxxx">
        </div>

        {{-- Persetujuan (wajib dicentang setiap kali top up) --}}
        <div class="card" style="margin-top:16px">
            <label style="display:flex;gap:12px;align-items:flex-start;cursor:pointer">
                <input type="checkbox" id="agreeChk" name="agree" value="1" {{ old('agree') ? 'checked' : '' }}
                    style="margin-top:3px;width:18px;height:18px;accent-color:#8a3ffc;flex-shrink:0">
                <span style="font-size:13px;color:var(--muted);line-height:1.6">
                    Saya telah membaca dan menyetujui
                    <a href="{{ route('page.terms') }}" target="_blank" rel="noopener" style="color:#c4b5fd;font-weight:700;text-decoration:underline">Syarat &amp; Ketentuan</a>
                    serta
                    <a href="{{ route('page.privacy') }}" target="_blank" rel="noopener" style="color:#c4b5fd;font-weight:700;text-decoration:underline">Kebijakan Privasi</a>
                    HustleStore, dan memastikan data tujuan yang saya isi sudah <b style="color:#fff">benar</b>.
                </span>
            </label>
        </div>

        <div class="summary">
            <div class="info">
                <div class="t">Total Pembayaran</div>
                <div class="v" id="totalText">Rp 0</div>
                <div class="t" id="selText">Pilih nominal dulu</div>
            </div>
            <button type="submit" class="btn btn-primary" id="payBtn" disabled>Bayar Sekarang</button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    let selected = null;
    const agreeChk = document.getElementById('agreeChk');
    function rupiah(n){ return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    // Tombol bayar hanya aktif jika produk dipilih DAN persetujuan dicentang.
    function updatePayState(){
        document.getElementById('payBtn').disabled = !(document.getElementById('product_id').value && agreeChk.checked);
    }
    function selectProduct(el){
        document.querySelectorAll('.ptile').forEach(t => t.classList.remove('sel'));
        el.classList.add('sel');
        selected = el.dataset;
        document.getElementById('product_id').value = selected.id;
        document.getElementById('totalText').innerText = rupiah(selected.price);
        document.getElementById('selText').innerText = selected.name;
        updatePayState();
    }
    agreeChk.addEventListener('change', updatePayState);
    // Restore selection after validation error
    (function(){
        const pre = document.getElementById('product_id').value;
        if(pre){ const el = document.querySelector('.ptile[data-id="'+pre+'"]'); if(el && !el.classList.contains('disabled')) selectProduct(el); }
        updatePayState();
    })();
    document.getElementById('orderForm').addEventListener('submit', function(e){
        if(!document.getElementById('product_id').value){ e.preventDefault(); alert('Silakan pilih nominal produk dulu.'); return; }
        if(!agreeChk.checked){ e.preventDefault(); alert('Centang persetujuan Syarat & Ketentuan dulu ya.'); return; }
        document.getElementById('payBtn').disabled = true;
        document.getElementById('payBtn').innerText = 'Memproses...';
    });
</script>
@endsection
