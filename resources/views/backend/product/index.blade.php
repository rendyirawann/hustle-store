@extends('backend.layout.app')
@section('title', 'Daftar Harga')

@section('content')
<div class="mt-5 mb-10">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-6">
            <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
            <div class="d-flex flex-column"><span>{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center p-5 mb-6">
            <i class="ki-outline ki-information-5 fs-2hx text-danger me-4"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-danger">Periksa kembali input Anda</h4>
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 d-flex flex-wrap align-items-center justify-content-between">
            <h3 class="card-title fw-bold fs-2 m-0">
                <i class="ki-outline ki-tag fs-2 me-2 text-primary"></i>Daftar Harga
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('brands.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-abstract-26 fs-4"></i>Kelola Brand
                </a>
                <form action="{{ route('products.regenerate') }}" method="POST" onsubmit="return confirm('Regenerasi harga jual SEMUA produk dari harga modal sesuai skema margin? Harga manual yang sudah Anda set akan ditimpa.');" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light-warning">
                        <i class="ki-outline ki-arrows-circle fs-4"></i>Regenerasi Harga
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-primary" onclick="openAdd()" @if(!$currentBrand) disabled @endif>
                    <i class="ki-outline ki-plus fs-4"></i>Tambah Produk
                </button>
            </div>
        </div>

        <div class="card-body pt-2">

            {{-- Filter: brand + search --}}
            <div class="row g-4 mb-6">
                <div class="col-lg-7">
                    <label class="form-label fw-semibold text-muted fs-7">Pilih Brand / Layanan</label>
                    <select class="form-select form-select-solid" onchange="if(this.value) window.location.href='{{ route('products.index') }}?brand='+this.value">
                        @foreach($tabs as $tabKey => $tabLabel)
                            @if(isset($brandsByTab[$tabKey]))
                                <optgroup label="{{ $tabLabel }}">
                                    @foreach($brandsByTab[$tabKey] as $b)
                                        <option value="{{ $b->slug }}" {{ $currentBrand && $currentBrand->id === $b->id ? 'selected' : '' }}>
                                            {{ $b->name }} @if(!$b->is_active) (nonaktif) @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-5">
                    <label class="form-label fw-semibold text-muted fs-7">Cari (kode / nama produk)</label>
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="brand" value="{{ $currentBrand->slug ?? '' }}">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid" placeholder="cari...">
                        <button class="btn btn-light-primary"><i class="ki-outline ki-magnifier fs-3"></i></button>
                    </form>
                </div>
            </div>

            @if($currentBrand)
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <span class="badge badge-light-primary fs-7">{{ $currentBrand->tab_label }}</span>
                    <span class="badge badge-light-info fs-7">Kategori: {{ $currentBrand->category_label }}</span>
                    <span class="badge badge-light fs-7">Input: {{ $currentBrand->input_label }}</span>
                    <span class="badge badge-light-dark fs-7">{{ $products->count() }} produk</span>
                    @if(!$currentBrand->is_active)<span class="badge badge-danger fs-7">Brand Nonaktif</span>@endif
                </div>

                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle gs-4 gy-3">
                        <thead>
                            <tr class="fw-bold fs-7 text-uppercase text-gray-600 bg-light-primary">
                                <th>Kode</th>
                                <th>Produk</th>
                                <th>Catatan</th>
                                <th class="text-end">Modal</th>
                                <th class="text-end">Member</th>
                                <th class="text-end">Reseller</th>
                                <th class="text-end">H2H</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aktif</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $p)
                                <tr>
                                    <td class="fw-bold text-gray-800">{{ $p->code }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td class="text-muted fs-7">{{ $p->note }}</td>
                                    <td class="text-end text-muted">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-primary">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                                        <div class="text-success fs-8 mt-1">+Rp {{ number_format($p->profit, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-end"><span class="badge badge-success">Rp {{ number_format($p->reseller_price, 0, ',', '.') }}</span></td>
                                    <td class="text-end"><span class="badge badge-info">{{ $p->h2h_price ? 'Rp '.number_format($p->h2h_price, 0, ',', '.') : '-' }}</span></td>
                                    <td class="text-center">
                                        @if($p->status === 'available')
                                            <span class="badge badge-light-success"><i class="ki-outline ki-check-circle fs-6 me-1"></i>Tersedia</span>
                                        @elseif($p->status === 'cutoff')
                                            <span class="badge badge-light-warning">Cut Off</span>
                                        @else
                                            <span class="badge badge-light-danger">Empty</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('products.toggle', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon {{ $p->is_active ? 'btn-light-success' : 'btn-light-danger' }}"
                                                title="{{ $p->is_active ? 'Aktif — klik untuk nonaktifkan' : 'Nonaktif — klik untuk aktifkan' }}">
                                                <i class="ki-outline {{ $p->is_active ? 'ki-toggle-on-circle' : 'ki-toggle-off-circle' }} fs-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-icon btn-light-primary me-1" title="Edit"
                                            data-id="{{ $p->id }}" data-brand="{{ $p->brand_id }}" data-code="{{ $p->code }}"
                                            data-name="{{ $p->name }}" data-note="{{ $p->note }}" data-cost="{{ $p->cost_price }}"
                                            data-price="{{ $p->price }}" data-reseller="{{ $p->reseller_price }}" data-h2h="{{ $p->h2h_price }}"
                                            data-status="{{ $p->status }}" data-active="{{ $p->is_active ? 1 : 0 }}" data-sort="{{ $p->sort_order }}"
                                            onclick="openEdit(this)"><i class="ki-outline ki-pencil fs-4"></i></button>
                                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk {{ $p->code }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Hapus"><i class="ki-outline ki-trash fs-4"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted py-10">Belum ada produk untuk brand ini. Klik <b>Tambah Produk</b>.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-10">
                    Belum ada brand. Silakan buat brand dulu di <a href="{{ route('brands.index') }}">Kelola Brand</a>.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ============ MODAL TAMBAH / EDIT PRODUK ============ --}}
<div class="modal fade" id="modalProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form id="productForm" method="POST" action="{{ route('products.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h2 class="fw-bold" id="modalTitle">Tambah Produk</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body scroll-y mx-2 my-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label required fw-semibold">Brand / Layanan</label>
                            <select name="brand_id" id="f_brand_id" class="form-select form-select-solid" required>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" data-category="{{ $b->category }}"
                                        {{ $currentBrand && $currentBrand->id === $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Kode</label>
                            <input type="text" name="code" id="f_code" class="form-control form-control-solid" placeholder="cth: SM10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Ketersediaan</label>
                            <select name="status" id="f_status" class="form-select form-select-solid">
                                @foreach($statuses as $sk => $sl)<option value="{{ $sk }}">{{ $sl }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label required fw-semibold">Nama Produk</label>
                            <input type="text" name="name" id="f_name" class="form-control form-control-solid" placeholder="cth: Telkomsel 10.000" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <input type="text" name="note" id="f_note" class="form-control form-control-solid" placeholder="opsional, cth: Reguler">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Harga Modal (Rp)</label>
                            <input type="number" name="cost_price" id="f_cost" class="form-control form-control-solid" min="0" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-text text-muted">Kosongkan harga jual di bawah untuk <b>hitung otomatis</b> dari margin.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Member (Rp)</label>
                            <input type="number" name="price" id="f_price" class="form-control form-control-solid" min="0" placeholder="otomatis">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Reseller (Rp)</label>
                            <input type="number" name="reseller_price" id="f_reseller" class="form-control form-control-solid" min="0" placeholder="otomatis">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga H2H (Rp)</label>
                            <input type="number" name="h2h_price" id="f_h2h" class="form-control form-control-solid" min="0" placeholder="otomatis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan</label>
                            <input type="number" name="sort_order" id="f_sort" class="form-control form-control-solid" min="0" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" id="f_active" value="1" checked>
                                <span class="form-check-label fw-semibold ms-2">Produk Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ki-outline ki-check fs-3"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productModal = new bootstrap.Modal(document.getElementById('modalProduct'));
    const form = document.getElementById('productForm');
    const storeUrl = "{{ route('products.store') }}";
    const updateBase = "{{ url('/admin/products') }}";

    function openAdd() {
        form.action = storeUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Produk';
        form.reset();
        document.getElementById('f_active').checked = true;
        document.getElementById('f_sort').value = 0;
        productModal.show();
    }

    function openEdit(btn) {
        const d = btn.dataset;
        form.action = updateBase + '/' + d.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Produk — ' + d.code;
        document.getElementById('f_brand_id').value = d.brand;
        document.getElementById('f_code').value = d.code;
        document.getElementById('f_name').value = d.name;
        document.getElementById('f_note').value = d.note || '';
        document.getElementById('f_cost').value = d.cost;
        document.getElementById('f_price').value = d.price;
        document.getElementById('f_reseller').value = d.reseller;
        document.getElementById('f_h2h').value = d.h2h || '';
        document.getElementById('f_status').value = d.status;
        document.getElementById('f_sort').value = d.sort;
        document.getElementById('f_active').checked = d.active === '1';
        productModal.show();
    }
</script>
@endpush
