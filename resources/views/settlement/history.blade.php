@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">History Settlement</h4>

    {{-- Filter Tanggal --}}
    <form method="GET" class="row g-3 mb-3">
        <div class="col-auto">
            <label>Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-auto">
            <label>Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        <div class="col-auto">
            <a href="{{ route('settlement.history') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center" id="settlementTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Jam Buka</th>
                        <th>Jam Tutup</th>
                        <th>Total Sales</th>
                        <th>Total Cash</th>
                        <th>Total Debit</th>
                        <th>Total QRIS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settlements as $i => $s)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->opened_at)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->opened_at)->format('H:i:s') }}</td>
                            <td>{{ $s->closed_at ? \Carbon\Carbon::parse($s->closed_at)->format('H:i:s') : '-' }}</td>
                            <td>{{ 'Rp ' . number_format($s->total_sales ?? 0, 0, ',', '.') }}</td>
                            <td>{{ 'Rp ' . number_format($s->total_cash_sales ?? 0, 0, ',', '.') }}</td>
                            <td>{{ 'Rp ' . number_format($s->total_debit_sales ?? 0, 0, ',', '.') }}</td>
                            <td>{{ 'Rp ' . number_format($s->total_qris_sales ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-detail" data-id="{{ $s->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Settlement -->
<div class="modal fade" id="detailSettlementModal" tabindex="-1" aria-labelledby="detailSettlementLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Settlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Satuan</th>
                            <th>Qty</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="detailSettlementBody">
                        <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
                <div class="mt-3 text-end">
                    <strong>Total Penjualan Keseluruhan: <span id="totalSales">0</span></strong><br>
                    <strong>Total Cash: <span id="totalCash">0</span></strong><br>
                    <strong>Total Debit: <span id="totalDebit">0</span></strong><br>
                    <strong>Total QRIS: <span id="totalQris">0</span></strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){

    const detailButtons = document.querySelectorAll('.btn-detail');
    const detailModal = new bootstrap.Modal(document.getElementById('detailSettlementModal'));
    const tbody = document.getElementById('detailSettlementBody');
    const totalSalesEl = document.getElementById('totalSales');
    const totalCashEl = document.getElementById('totalCash');
    const totalDebitEl = document.getElementById('totalDebit');
    const totalQrisEl = document.getElementById('totalQris');

    // Fungsi format Rupiah
    const formatRupiah = (angka) => 'Rp ' + Number(angka).toLocaleString('id-ID');

    detailButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            const settlementId = this.dataset.id;

            tbody.innerHTML = `<tr><td colspan="4" class="text-center">Memuat data...</td></tr>`;

            fetch(`{{ route('settlement.detail', ['id' => '__ID__']) }}`.replace('__ID__', settlementId))
                .then(res => res.json())
                .then(res => {
                    if(res.products.length === 0){
                        tbody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada produk terjual</td></tr>`;
                    } else {
                        let html = '';
                        res.products.forEach(p => {
                            html += `<tr>
                                <td>${p.nama}</td>
                                <td>${p.satuan}</td>
                                <td>${p.qty}</td>
                                <td>${formatRupiah(p.total)}</td>
                            </tr>`;
                        });
                        tbody.innerHTML = html;
                    }

                    totalSalesEl.textContent = formatRupiah(res.total_sales);
                    totalCashEl.textContent = formatRupiah(res.total_cash_sales);
                    totalDebitEl.textContent = formatRupiah(res.total_debit_sales);
                    totalQrisEl.textContent = formatRupiah(res.total_qris_sales);

                    detailModal.show();
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center">Gagal memuat data</td></tr>`;
                });
        });
    });

});
</script>
@endsection
