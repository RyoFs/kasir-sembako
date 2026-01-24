@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Tambah Produk</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Barcode</label>
                    <input type="text" name="barcode" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Satuan Dasar</label>
                    <input type="text" name="satuan_dasar" class="form-control" placeholder="Contoh: kg, liter, pcs" required>
                </div>
                
                {{-- === STOK TAMBAHAN === --}}
                <div class="mb-3">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" class="form-control" min="0.01" step="0.01" value="0" required>
                </div>

                <hr>
                <h5>Satuan Produk</h5>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Satuan</th>
                            <th>Konversi</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody id="satuanBody"></tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm" id="btnAddRow">+ Tambah Satuan</button>

                <div class="mt-3">
                    <button class="btn btn-secondary" onclick="history.back()" type="button">Kembali</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let index = 0;

        function row() {
            return `
            <tr>
                <td><input type="text" name="satuan[${index}][nama_satuan]" class="form-control" required></td>
                <td><input type="number" name="satuan[${index}][konversi]" class="form-control" min="0.01" step="0.01" min="1" value="1"></td>
                <td><input type="number" name="satuan[${index}][harga_beli]" class="form-control" value="0"></td>
                <td><input type="number" name="satuan[${index}][harga_jual]" class="form-control" value="0"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
            </tr>`;
        }

        document.getElementById("btnAddRow").addEventListener("click", function () {
            document.getElementById("satuanBody").insertAdjacentHTML("beforeend", row());
            index++;
        });

    });
</script>
@endsection
