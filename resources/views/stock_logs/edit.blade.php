@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Stock</h4>

    <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#produkModal">
        Pilih Produk
    </button>

    <form action="{{ route('stock_logs.update',$log->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">

            <div class="col-md-6">
                <label>Produk Terpilih</label>
                <input type="text" id="produk_name" class="form-control" readonly value="{{ $log->produk->nama_produk }}">
                <input type="hidden" name="produk_id" id="produk_id" value="{{ $log->produk_id }}">
            </div>

            <div class="col-md-6">
                <label>Satuan</label>
                <select name="produk_satuan_id" id="produk_satuan_id" class="form-select">
                    <option value="">Satuan dasar</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Type</label>
                <select name="type" class="form-select" required>
                    <option value="in" {{ $log->type=='in'?'selected':'' }}>Masuk</option>
                    <option value="out" {{ $log->type=='out'?'selected':'' }}>Keluar</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Qty</label>
                <input type="number" step="0.01" name="qty" id="qty" class="form-control" value="{{ $log->qty }}" required>
            </div>

            <div class="col-md-6">
                <label>Qty dasar</label>
                <input type="text" id="qtyDasarPreview" class="form-control" readonly value="{{ $log->qty_dasar }}">
            </div>

            <div class="col-md-6">
                <label>Harga Beli (Opsional)</label>
                <input type="number" step="0.01" name="harga_beli" class="form-control" value="{{ $log->harga_beli }}">
            </div>

            <div class="col-md-12">
                <label>Catatan</label>
                <input type="text" name="note" class="form-control" value="{{ $log->note }}">
            </div>

            <div class="col-md-12 mt-2">
                <button type="button" class="btn btn-secondary" onclick="history.back()">Kembali</button>
                <button class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal pilih produk sama seperti create -->
@include('stock_logs.partials.produk_modal')

<script>
document.addEventListener('DOMContentLoaded', function(){
    const produkIdInput = document.getElementById('produk_id');
    const produkNameInput = document.getElementById('produk_name');
    const satuanSelect = document.getElementById('produk_satuan_id');
    const qtyInput = document.getElementById('qty');
    const qtyDasarPreview = document.getElementById('qtyDasarPreview');
    const currentSatuan = '{{ $log->produk_satuan_id }}';

    function updateSatuanOptions(satuanData){
        satuanSelect.innerHTML = '<option value="">Satuan dasar</option>';
        if(satuanData){
            satuanData.forEach(s => {
                satuanSelect.innerHTML += `<option value="${s.id}" data-konversi="${s.konversi}" ${s.id==currentSatuan?'selected':''}>${s.nama_satuan}</option>`;
            });
        }
        updateQtyDasarPreview();
    }

    function updateQtyDasarPreview(){
        const qty = parseFloat(qtyInput.value) || 0;
        const selectedSatuan = satuanSelect.selectedOptions[0];
        const konversi = selectedSatuan ? parseFloat(selectedSatuan.dataset.konversi) || 1 : 1;
        qtyDasarPreview.value = qty * konversi;
    }

    // Load satuan saat pertama
    const optionSelected = document.querySelector(`#produk_id option[value="${produkIdInput.value}"]`);
    const satuanData = optionSelected ? JSON.parse(optionSelected.dataset.satuan) : [];
    updateSatuanOptions(satuanData);

    // Event select produk modal
    document.querySelectorAll('.select-produk').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const nama = this.closest('tr').querySelector('td').textContent;
            const satuan = JSON.parse(this.dataset.satuan);

            produkIdInput.value = id;
            produkNameInput.value = nama;

            updateSatuanOptions(satuan);
            qtyInput.value = '';
            qtyDasarPreview.value = 0;
        });
    });

    qtyInput.addEventListener('input', updateQtyDasarPreview);
    satuanSelect.addEventListener('change', updateQtyDasarPreview);

    // Search produk modal
    const searchInput = document.getElementById('searchProduk');
    const produkTable = document.getElementById('produkTable').getElementsByTagName('tbody')[0];

    searchInput.addEventListener('keyup', function(){
        const filter = this.value.toLowerCase();
        Array.from(produkTable.rows).forEach(row => {
            const nama = row.querySelector('.produk-nama').textContent.toLowerCase();
            row.style.display = nama.includes(filter) ? '' : 'none';
        });
    });
});
</script>
@endsection
