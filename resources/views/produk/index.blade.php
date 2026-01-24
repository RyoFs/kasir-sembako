@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Daftar Produk</h4>
        <a href="{{ route('produk.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('produk.index') }}" method="GET" class="row g-3 align-items-center">
                {{-- SEARCH PRODUK --}}
                <div class="col-md-4">
                    <input type="text" id="searchProduk" placeholder="Cari produk / barcode ..." class="form-control form-control-lg">
                </div>

                {{-- FILTER KATEGORI --}}
                <div class="col-md-3">
                    <select name="filter_kategori" class="form-select form-select-lg">
                        <option value="">-- Semua Kategori --</option>

                        @foreach($kategori as $k)
                            <option value="{{ $k->kategori }}"
                                {{ request('filter_kategori') == $k->kategori ? 'selected' : '' }}>
                                {{ $k->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTON FILTER --}}
                <div class="col-md-3 d-flex gap-2">
                    {{-- RESET / CLEAR FILTER --}}
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary btn-lg w-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Barcode</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Satuan Lain</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk as $i => $p)
                    <tr class="align-middle">
                        <td>{{ $i + 1 }}</td>
                        <td class="text-start">{{ $p->nama_produk }}</td>
                        <td>{{ $p->barcode }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $p->kategori }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $p->stok }} {{ $p->satuan_dasar }}</span>
                        </td>
                        <td>
                            @foreach($p->satuan as $s)
                                <span class="badge bg-secondary">{{ $s->nama_satuan }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('produk.edit', $p->id) }}" class="btn btn-warning btn-sm me-1" title="Edit Produk">
                                <i class="mdi mdi-pencil"></i>
                            </a>

                            <form action="{{ route('produk.destroy', $p->id) }}"
                                method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete" title="Hapus Produk">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $produk->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
.table-hover tbody tr:hover {
    background-color: #f0f8ff;
    transition: background-color 0.2s;
}

/* Font isi tabel lebih besar */
.table td, .table th {
    vertical-align: middle;
    padding: 0.75rem 0.9rem;
    font-size: 18px !important; /* paksa lebih besar */
}

/* Badge lebih besar */
.badge {
    font-size: 16px !important;
    padding: 0.55em 0.75em;
}

/* Tombol aksi lebih besar */
.btn-sm {
    font-size: 16px !important;
    padding: 0.45rem 0.8rem !important;
}

.btn-sm i {
    font-size: 1.3rem !important;
}

/* Style form filter */
.form-control-lg, .form-select-lg {
    font-size: 17px;
    padding: 10px 14px;
}

.btn-lg {
    font-size: 17px !important;
    padding: 10px 14px !important;
}

</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Fungsi bind SweetAlert untuk tombol delete
    function bindDeleteButtons(){
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                let form = this.closest('form');
                Swal.fire({
                    title: "Hapus Produk?",
                    text: "Data produk akan dihapus permanen.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

    bindDeleteButtons(); // initial bind

    const searchInput = document.getElementById('searchProduk');
    const filterKategori = document.querySelector('select[name="filter_kategori"]');
    const produkTbody = document.querySelector('table tbody');

    // Fungsi fetch AJAX produk
    function fetchProduk() {
        const search = searchInput.value;
        const kategori = filterKategori.value;

        fetch("{{ route('produk.ajaxSearch') }}?search=" + encodeURIComponent(search) + "&filter_kategori=" + encodeURIComponent(kategori))
            .then(res => res.json())
            .then(res => {
                let html = '';
                res.data.forEach((p, i) => {
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td class="text-start">${p.nama_produk}</td>
                        <td>${p.barcode}</td>
                        <td><span class="badge bg-info text-dark">${p.kategori ?? ''}</span></td>
                        <td><span class="badge bg-primary">${p.stok} ${p.satuan_dasar}</span></td>
                        <td>${p.satuan.map(s => `<span class="badge bg-secondary">${s.nama_satuan}</span>`).join(' ')}</td>
                        <td>
                            <a href="/produk/${p.id}/edit" class="btn btn-warning btn-sm me-1"><i class="mdi mdi-pencil"></i></a>
                            <form action="/produk/${p.id}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-delete"><i class="mdi mdi-delete"></i></button>
                            </form>
                        </td>
                    </tr>`;
                });
                produkTbody.innerHTML = html;
                bindDeleteButtons(); // re-bind SweetAlert untuk tombol delete baru
            });
    }

    // Event listener search input
    searchInput.addEventListener('keyup', fetchProduk);

    // Event listener filter kategori
    filterKategori.addEventListener('change', fetchProduk);

});
</script>
@endsection

