@extends('backend.layout.app')
@section('title', 'Brand & Layanan')

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
        <div class="alert alert-danger p-5 mb-6">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 d-flex align-items-center justify-content-between">
            <h3 class="card-title fw-bold fs-2 m-0"><i class="ki-outline ki-abstract-26 fs-2 me-2 text-primary"></i>Brand & Layanan</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-tag fs-4"></i>Daftar Harga</a>
                <button type="button" class="btn btn-sm btn-primary" onclick="openAddBrand()"><i class="ki-outline ki-plus fs-4"></i>Tambah Brand</button>
            </div>
        </div>
        <div class="card-body pt-2">
            @foreach($tabs as $tabKey => $tabLabel)
                <h4 class="fw-bold text-gray-700 mt-6 mb-3">{{ $tabLabel }}</h4>
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle gs-4 gy-3">
                        <thead>
                            <tr class="fw-bold fs-7 text-uppercase text-gray-600 bg-light">
                                <th>Nama</th><th>Kategori</th><th>Label Input</th>
                                <th class="text-center">Produk</th><th class="text-center">Aktif</th><th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brandsByTab[$tabKey] ?? [] as $b)
                                <tr>
                                    <td class="fw-bold text-gray-800">{{ $b->name }}</td>
                                    <td><span class="badge badge-light-info">{{ $b->category_label }}</span></td>
                                    <td class="text-muted fs-7">{{ $b->input_label }}</td>
                                    <td class="text-center"><span class="badge badge-light-dark">{{ $b->products_count }}</span></td>
                                    <td class="text-center">
                                        <form action="{{ route('brands.toggle', $b->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-icon {{ $b->is_active ? 'btn-light-success' : 'btn-light-danger' }}" title="{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}">
                                                <i class="ki-outline {{ $b->is_active ? 'ki-toggle-on-circle' : 'ki-toggle-off-circle' }} fs-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('products.index', ['brand' => $b->slug]) }}" class="btn btn-sm btn-icon btn-light me-1" title="Lihat produk"><i class="ki-outline ki-eye fs-4"></i></a>
                                        <button class="btn btn-sm btn-icon btn-light-primary me-1" title="Edit"
                                            data-id="{{ $b->id }}" data-name="{{ $b->name }}" data-tab="{{ $b->tab }}"
                                            data-category="{{ $b->category }}" data-input="{{ $b->input_label }}" data-note="{{ $b->note }}"
                                            data-logo="{{ $b->logo }}" data-sort="{{ $b->sort_order }}" data-active="{{ $b->is_active ? 1 : 0 }}"
                                            onclick="openEditBrand(this)"><i class="ki-outline ki-pencil fs-4"></i></button>
                                        <form action="{{ route('brands.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus brand {{ $b->name }} beserta SEMUA produknya?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-light-danger" title="Hapus"><i class="ki-outline ki-trash fs-4"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-6">Belum ada brand di kategori ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- MODAL BRAND --}}
<div class="modal fade" id="modalBrand" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <form id="brandForm" method="POST" action="{{ route('brands.store') }}">
                @csrf
                <input type="hidden" name="_method" id="brandMethod" value="POST">
                <div class="modal-header">
                    <h2 class="fw-bold" id="brandTitle">Tambah Brand</h2>
                    <div class="btn btn-icon btn-sm" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body mx-2 my-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label required fw-semibold">Nama Brand / Layanan</label>
                            <input type="text" name="name" id="b_name" class="form-control form-control-solid" placeholder="cth: Telkomsel / Mobile Legends / Netflix" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Tab</label>
                            <select name="tab" id="b_tab" class="form-select form-select-solid">
                                @foreach($tabs as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Kategori (skema margin)</label>
                            <select name="category" id="b_category" class="form-select form-select-solid">
                                @foreach($categories as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Label Input Pelanggan</label>
                            <input type="text" name="input_label" id="b_input" class="form-control form-control-solid" placeholder="cth: Nomor HP / User ID (Zone ID) / Email">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Gambar / Thumbnail</label>
                            <input type="text" name="logo" id="b_logo" class="form-control form-control-solid" placeholder="URL gambar (https://...) atau nama file di /assets/media/brands/">
                            <div class="form-text text-muted">Tampil sebagai thumbnail di landing & katalog. Kosongkan untuk pakai ikon default.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <input type="text" name="note" id="b_note" class="form-control form-control-solid" placeholder="opsional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan</label>
                            <input type="number" name="sort_order" id="b_sort" class="form-control form-control-solid" min="0" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" id="b_active" value="1" checked>
                                <span class="form-check-label fw-semibold ms-2">Aktif</span>
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
    const brandModal = new bootstrap.Modal(document.getElementById('modalBrand'));
    const bForm = document.getElementById('brandForm');
    const bStoreUrl = "{{ route('brands.store') }}";
    const bUpdateBase = "{{ url('/admin/brands') }}";

    function openAddBrand() {
        bForm.action = bStoreUrl;
        document.getElementById('brandMethod').value = 'POST';
        document.getElementById('brandTitle').innerText = 'Tambah Brand';
        bForm.reset();
        document.getElementById('b_active').checked = true;
        brandModal.show();
    }
    function openEditBrand(btn) {
        const d = btn.dataset;
        bForm.action = bUpdateBase + '/' + d.id;
        document.getElementById('brandMethod').value = 'PUT';
        document.getElementById('brandTitle').innerText = 'Edit Brand — ' + d.name;
        document.getElementById('b_name').value = d.name;
        document.getElementById('b_tab').value = d.tab;
        document.getElementById('b_category').value = d.category;
        document.getElementById('b_input').value = d.input || '';
        document.getElementById('b_logo').value = d.logo || '';
        document.getElementById('b_note').value = d.note || '';
        document.getElementById('b_sort').value = d.sort;
        document.getElementById('b_active').checked = d.active === '1';
        brandModal.show();
    }
</script>
@endpush
