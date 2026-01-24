@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item">Pengaturan</li>
<li class="breadcrumb-item active">Pengaturan User</li>
@endsection

@section('content')
<div class="container">
    <h3 class="mb-4 text-primary fw-bold">Pengaturan User</h3>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="mdi mdi-account-plus"></i> Tambah User
    </button>

    <!-- Table User -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th width="140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $i => $user)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <!-- Button Edit -->
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit{{ $user->id }}">
                                    <i class="mdi mdi-square-edit-outline"></i>
                                </button>

                                <!-- Button Delete (panggil JS) -->
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="hapusUser('{{ $user->id }}')">
                                    <i class="mdi mdi-delete"></i>
                                </button>

                                <!-- Form delete -->
                                <form id="form-hapus-{{ $user->id }}"
                                    action="{{ route('user.destroy', $user->id) }}"
                                    method="POST"
                                    class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        <!-- Modal Edit User -->
                        <div class="modal fade" id="modalEdit{{ $user->id }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('user.update', $user->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Nama</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>PIN (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="pin" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label>Role</label>
                                                <select name="role" class="form-control">
                                                    <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
                                                    <option value="kasir" {{ $user->role=='kasir'?'selected':'' }}>Kasir</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button class="btn btn-success">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('user.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>PIN</label>
                        <input type="password" name="pin" class="form-control" maxlength="4" required>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection


@section('js')
<script>
function hapusUser(id) {
    Swal.fire({
        title: "Hapus user?",
        text: "Data user akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("form-hapus-" + id).submit();
        }
    });
}
</script>

@endsection
