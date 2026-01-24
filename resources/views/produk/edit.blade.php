@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Edit Produk</h4>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Barcode</label>
                    <input type="text" name="barcode" value="{{ $produk->barcode }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" value="{{ $produk->nama_produk }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->nama }}" {{ $produk->kategori == $k->nama ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Satuan Dasar</label>
                    <input type="text" name="satuan_dasar" class="form-control" 
                        value="{{ old('satuan_dasar', $produk->satuan_dasar) }}" required>
                </div>

                <div class="mb-3">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" value="{{ $produk->stok }}" min="0.01" step="0.01"  min="0" required>
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
                    <tbody id="satuanEditBody">

                        @foreach($produk->satuan as $s)
                        <tr>
                            <input type="hidden" name="satuan[{{ $loop->index }}][id]" value="{{ $s->id }}">
                            <td><input type="text" name="satuan[{{ $loop->index }}][nama_satuan]" class="form-control" value="{{ $s->nama_satuan }}"></td>
                            <td><input type="number" name="satuan[{{ $loop->index }}][konversi]" class="form-control" min="0.01" step="0.01" value="{{ $s->konversi }}"></td>
                            <td><input type="number" name="satuan[{{ $loop->index }}][harga_beli]" class="form-control" value="{{ $s->harga_beli }}"></td>
                            <td><input type="number" name="satuan[{{ $loop->index }}][harga_jual]" class="form-control" value="{{ $s->harga_jual }}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm" id="btnAddEditRow">
                    + Tambah Satuan
                </button>

                <div class="mt-3">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">Kembali</button>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let index = {{ count($produk->satuan) }};

        function row() {
            return `
            <tr>
                <td><input type="text" name="satuan[${index}][nama_satuan]" class="form-control" required></td>
                <td><input type="number" name="satuan[${index}][konversi]" class="form-control" min="0.01" step="0.01" value="1"></td>
                <td><input type="number" name="satuan[${index}][harga_beli]" class="form-control" value="0"></td>
                <td><input type="number" name="satuan[${index}][harga_jual]" class="form-control" value="0"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
            </tr>`;
        }

        document.getElementById("btnAddEditRow").addEventListener("click", function () {
            document.getElementById("satuanEditBody").insertAdjacentHTML("beforeend", row());
            index++;
        });


    });
</script>
@endsection
