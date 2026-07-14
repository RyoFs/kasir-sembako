@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Stock</h4>

    <!-- Tombol modal pilih produk -->
    <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#produkModal">Pilih Produk</button>

    <form action="{{ route('stock_logs.store') }}" method="POST">
        @csrf

        <!-- Produk readonly -->
        <div class="mb-3">
            <label>Produk Terpilih</label>
            <input type="text" id="produk_name" class="form-control" placeholder="Pilih produk dari modal" readonly>
            <input type="hidden" name="produk_id" id="produk_id">
        </div>

        <!-- Satuan -->
        <div class="mb-3">
            <label>Satuan</label>
            <select name="produk_satuan_id" id="produk_satuan_id" class="form-select">
                <option value="">-- Pilih Satuan --</option>
            </select>
        </div>

        <!-- Type -->
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-select" required>
                <option value="in">Masuk</option>
                <option value="out">Keluar</option>
            </select>
        </div>

        <!-- Qty -->
        <div class="mb-3">
            <label>Qty</label>
            <input type="number" step="0.01" name="qty" id="qty" class="form-control" required>
        </div>

        <!-- Qty dasar preview -->
        <div class="mb-3">
            <label>Qty dasar yang akan masuk/keluar:</label>
            <input type="text" id="qtyDasarPreview" class="form-control" readonly value="0">
        </div>

        <!-- Harga & Catatan -->
        <div class="mb-3">
            <label>Harga Beli (per satuan)</label>
            <input type="number" step="0.01" name="harga_beli" class="form-control">
        </div>
        <div class="mb-3">
            <label>Catatan</label>
            <input type="text" name="note" class="form-control">
        </div>
        <button type="button" class="btn btn-secondary" onclick="history.back()">Kembali</button>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>

<!-- Modal pilih produk -->
<div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Search Produk -->
                <div class="mb-3">
                    <input type="text" id="searchProduk" class="form-control" placeholder="Cari produk...">
                </div>

                <table class="table table-bordered table-hover" id="produkTable">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Barcode</th>
                            <th>Satuan</th>
                            <th>Pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $p)
                        <tr>
                            <td class="produk-nama">{{ $p->nama_produk }}</td>
                            <td>{{ $p->barcode }}</td>
                            <td>
                                @foreach($p->satuan as $s)
                                    {{ $s->nama_satuan }} (x{{ $s->konversi }})<br>
                                @endforeach
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success select-produk"
                                    data-id="{{ $p->id }}"
                                    data-satuan='@json($p->satuan)'
                                    data-bs-dismiss="modal">Pilih</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const produkIdInput = document.getElementById('produk_id');
    const produkNameInput = document.getElementById('produk_name');
    const satuanSelect = document.getElementById('produk_satuan_id');
    const qtyInput = document.getElementById('qty');
    const qtyDasarPreview = document.getElementById('qtyDasarPreview');

    // Fungsi update satuan dropdown
    function updateSatuanOptions(satuanData){
        satuanSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';
        if(satuanData){
            satuanData.forEach(s => {
                satuanSelect.innerHTML += `<option value="${s.id}" data-konversi="${s.konversi}">${s.nama_satuan}</option>`;
            });
        }
        updateQtyDasarPreview();
    }

    // Update preview qty dasar
    function updateQtyDasarPreview(){
        const qty = parseFloat(qtyInput.value) || 0;
        const selectedSatuan = satuanSelect.selectedOptions[0];
        const konversi = selectedSatuan ? parseFloat(selectedSatuan.dataset.konversi) || 1 : 1;
        qtyDasarPreview.value = qty * konversi;
    }

    // Pilih produk dari modal
    document.querySelectorAll('.select-produk').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const nama = this.closest('tr').querySelector('td').textContent;
            const satuan = JSON.parse(this.dataset.satuan);

            produkIdInput.value = id;
            produkNameInput.value = nama;

            updateSatuanOptions(satuan);

            // Reset qty
            qtyInput.value = '';
            qtyDasarPreview.value = 0;
        });
    });

    // Scan barcode
    const barcodeInput = document.createElement('input');
    barcodeInput.type = 'text';
    barcodeInput.id = 'barcodeInput';
    barcodeInput.placeholder = 'Scan barcode di sini';
    barcodeInput.classList.add('form-control', 'mb-3');
    const modalBody = document.querySelector('#produkModal .modal-body');
    modalBody.insertBefore(barcodeInput, modalBody.firstChild);

    // Fokus otomatis saat modal terbuka
    const produkModal = document.getElementById('produkModal');
    produkModal.addEventListener('shown.bs.modal', function () {
        barcodeInput.focus();
    });

    barcodeInput.addEventListener('keypress', function(e){
        if(e.key === 'Enter'){
            const barcode = this.value.trim();
            const rows = document.querySelectorAll('#produkTable tbody tr');
            let found = false;

            rows.forEach(row => {
                const rowBarcode = row.querySelector('td:nth-child(2)').textContent.trim();
                if(rowBarcode === barcode){
                    const btn = row.querySelector('.select-produk');
                    btn.click(); // trigger pilih produk
                    found = true;
                }
            });

            if(!found){
                Swal.fire({
                    icon: 'error',
                    title: 'Produk tidak ditemukan',
                    text: 'Produk dengan barcode "'+barcode+'" tidak ditemukan!',
                    timer: 2500,
                    showConfirmButton: false
                });
            }

            this.value = '';
            e.preventDefault();
        }
    });

    // Update preview saat qty & satuan berubah
    qtyInput.addEventListener('input', updateQtyDasarPreview);
    satuanSelect.addEventListener('change', updateQtyDasarPreview);

    // Search produk modal
    const searchInput = document.getElementById('searchProduk');
    const produkTable = document.getElementById('produkTable').getElementsByTagName('tbody')[0];

    searchInput.addEventListener('keyup', function(){
        const filter = this.value.toLowerCase();
        const rows = produkTable.getElementsByTagName('tr');

        for(let i=0; i<rows.length; i++){
            const namaProduk = rows[i].querySelector('.produk-nama').textContent.toLowerCase();
            rows[i].style.display = (namaProduk.indexOf(filter) > -1) ? '' : 'none';
        }
    });
});
</script>

@endsection
