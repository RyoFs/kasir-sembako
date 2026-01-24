@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kategori</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    {{-- HEADER --}}
    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="bi bi-tags-fill"></i> Daftar Kategori
        </h5>

        {{-- TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('kategori.create') }}" class="btn btn-success btn-sm d-flex align-items-center">
                <i class="bi bi-plus-circle me-1"></i>
                <span class="d-none d-md-inline">Tambah</span>
            </a>
        </div>
    </div>

    {{-- BODY --}}
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($Kategori as $i => $c)
                    <tr>
                        <td>{{ $Kategori->firstItem() + $i }}</td>
                        <td class="fw-semibold text-start">{{ $c->nama }}</td>
                        <td class="text-start">{{ $c->deskripsi ?? '-' }}</td>
                        <td>
                            {{-- Tombol Edit --}}
                            <a href="{{ route('kategori.edit', $c->id) }}" 
                               class="btn btn-warning btn-sm me-1 d-inline-flex align-items-center" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                                <span class="ms-1 d-none d-md-inline">Edit</span>
                            </a>

                            {{-- Tombol Hapus --}}
                            <button type="button"
                                    class="btn btn-danger btn-sm btn-hapus"
                                    data-url="{{ route('kategori.destroy', $c->id) }}">
                                <i class="bi bi-trash"></i>
                                <span class="ms-1 d-none d-md-inline">Hapus</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data kategori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $Kategori->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<form id="form-delete" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".btn-hapus").forEach(btn => {
        btn.addEventListener("click", function () {
            const url = this.dataset.url;

            Swal.fire({
                title: "Hapus Kategori?",
                text: "Data kategori akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById("form-delete");
                    form.action = url;
                    form.submit();
                }
            });
        });
    });

});
</script>

@endsection
