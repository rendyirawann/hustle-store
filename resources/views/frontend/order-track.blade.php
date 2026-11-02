@extends('frontend.shop')
@section('title', 'Lacak Pesanan — ' . config('seo.name', 'HustleStore'))
@section('robots', 'noindex, nofollow')

@section('content')
    <div class="crumb"><a href="{{ url('/') }}">Beranda</a> › <span>Lacak Pesanan</span></div>
    <h1 class="page">Lacak Pesanan</h1>
    <p style="color:var(--muted);margin-bottom:8px">Masukkan nomor pesanan (contoh: HS-260708-ABC123) yang Anda terima saat checkout.</p>

    @if(session('order_error'))<div class="alert alert-danger">{{ session('order_error') }}</div>@endif

    <div class="card" style="max-width:480px">
        <form method="POST" action="{{ route('order.track') }}">
            @csrf
            <label class="fld">Nomor Pesanan</label>
            <input class="inp" name="order_number" placeholder="HS-XXXXXX-XXXXXX" required autofocus>
            <button type="submit" class="btn btn-primary" style="margin-top:16px;width:100%">Cek Status</button>
        </form>
    </div>
@endsection
