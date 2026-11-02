@extends('frontend.shop')
@section('title', 'Semua Produk — ' . config('seo.name', 'HustleStore'))

@section('styles')
    .catsec{margin-top:26px}
    .catsec h2{font-size:16px;font-weight:800;margin-bottom:12px}
    .bgrid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
    @media(min-width:640px){.bgrid{grid-template-columns:repeat(4,1fr)}}
    .bcard{display:flex;flex-direction:column;gap:8px;padding:16px;border-radius:16px;background:rgba(255,255,255,.05);border:1px solid var(--line);transition:.15s}
    .bcard:hover{transform:translateY(-3px);border-color:var(--purple2)}
    .bcard .ic{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;background:linear-gradient(135deg,var(--purple),var(--pink))}
    .bcard .bn{font-weight:700;font-size:13.5px;line-height:1.2}
    .bcard .bp{font-size:12px;color:#ffd77a;font-weight:700}
@endsection

@section('content')
    @php
        $catEmoji = ['game'=>'🎮','pulsa'=>'📱','data'=>'🌐','pln'=>'⚡','ewallet'=>'💳','voucher'=>'🎟️','streaming'=>'🎬','sosmed'=>'📣'];
        $catLabel = ['game'=>'Top Up Game','pulsa'=>'Pulsa','data'=>'Paket Data','pln'=>'Token PLN','ewallet'=>'E-Money','voucher'=>'Voucher','streaming'=>'Streaming','sosmed'=>'Sosmed'];
    @endphp
    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <span>Semua Produk</span></div>
    <h1 class="page">Semua Produk</h1>

    @forelse($brandsByCategory as $cat => $list)
        <div class="catsec">
            <h2>{{ $catEmoji[$cat] ?? '🎮' }} {{ $catLabel[$cat] ?? ucfirst($cat) }}</h2>
            <div class="bgrid">
                @foreach($list as $b)
                    <a class="bcard" href="{{ route('brand.show', $b->slug) }}">
                        <span class="ic">{{ $catEmoji[$b->category] ?? '🎮' }}</span>
                        <span class="bn">{{ $b->name }}</span>
                        <span class="bp">{{ ($b->min_price ?? 0) > 0 ? 'Mulai Rp ' . number_format($b->min_price, 0, ',', '.') : 'Cek harga' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @empty
        <p style="color:var(--muted);padding:30px 0">Katalog sedang disiapkan.</p>
    @endforelse
@endsection
