@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Transaksi Penjualan</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Judul Halaman -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-4 text-primary fw-bold">Transaksi Penjualan</h3>
    </div>

    <!-- Kartu Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="mdi mdi-filter-variant"></i> Filter Data</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kartu Tabel Laporan -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="mdi mdi-clipboard-text"></i> Data Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Waktu</th>
                            <th>Kasir</th>
                            <th>Total Belanja</th>
                            <th>Pembayaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksis as $trx)
                            <tr>
                                <td><strong>{{ $trx->id }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $trx->user->nama }}</td>
                                <td class="text-end fw-bold">{{ 'Rp ' . number_format($trx->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $trx->metode_pembayaran == 'cash' ? 'success' : ($trx->metode_pembayaran == 'qris' ? 'info' : 'warning') }}">
                                        {{ strtoupper($trx->metode_pembayaran) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick="showDetail('{{ $trx->id }}')">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="reprintStruk('{{ $trx->id }}', event)">
                                        <i class="mdi mdi-printer"></i> Reprint
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">Tidak ada data transaksi pada periode yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="card-footer">
            {{ $transaksis->links() }}
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    <div class="text-center py-3 text-muted">Memuat data...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
async function reprintStruk(transaksiId, event) {
    // Tampilkan konfirmasi sebelum mencetak
    const confirmResult = await Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin mencetak ulang struk ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Cetak Ulang',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
    }).then((result) => {
        return result.isConfirmed;
    });

    if (!confirmResult) {
        return;
    }

    // Tampilkan indikator loading (opsional, tapi bagus untuk UX)
    const btn = event ? event.currentTarget : null;
    const originalText = btn ? btn.innerHTML : null;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Mencetak...';
    }

    try {
        const response = await fetch(`{{ route('laporan.getTransaksiForPrint', ':id') }}`.replace(':id', transaksiId));
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const result = await response.json();

        if (result.success) {
            // Cetak menggunakan Electron API
            if (window.electronAPI) {
                const printResult = await window.electronAPI.printReprintReceipt({
                    header: {
                        toko: "TOKO DZABAR ASRI",
                        alamat: "Jl. Telaga Malimping",
                        telp: "0882-9146-6517",
                        kasir: result.data.kasir,
                        kode: result.data.kode_transaksi,
                        tanggal: result.data.tanggal,
                        waktu: result.data.waktu
                    },
                    items: result.data.items,
                    total: result.data.total,
                    diskon: result.data.diskon,
                    grand_total: result.data.grand_total,
                    metode: result.data.metode ?? "CASH",
                    bayar: result.data.bayar,
                    kembali: result.data.kembali
                });
                
                if (!printResult.success) {
                    throw new Error(printResult.message || 'Gagal mencetak via Electron.');
                }
            } else {
                // Fallback untuk browser (tampilkan dialog print browser)
                alert('Fitur cetak hanya tersedia di aplikasi desktop.');
            }
        } else {
            throw new Error(result.message || 'Data transaksi tidak ditemukan.');
        }
    } catch (error) {
        console.error('Gagal reprint struk:', error);
        if (window.electronAPI) {
            await window.electronAPI.showMessageBox({
                type: 'error',
                title: 'Gagal Cetak',
                message: `Gagal mencetak ulang struk: ${error.message}`
            });
        } else {
            alert(`Gagal mencetak ulang struk: ${error.message}`);
        }
    } finally {
        // Kembalikan tombol ke keadaan semula
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
}

async function showDetail(transaksiId) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const contentDiv = document.getElementById('detailContent');
    contentDiv.innerHTML = '<div class="text-center py-3 text-muted">Memuat data...</div>';
    modal.show();

    try {
        const response = await fetch(`{{ route('laporan.getTransaksiForPrint', ':id') }}`.replace(':id', transaksiId));
        if (!response.ok) throw new Error('Gagal memuat detail transaksi');

        const result = await response.json();
        if (!result.success) throw new Error(result.message);

        const trx = result.data;

        let html = `
        <div class="mb-3">
            <strong>Kode Transaksi:</strong> ${trx.kode_transaksi}<br>
            <strong>Kasir:</strong> ${trx.kasir}<br>
            <strong>Tanggal:</strong> ${trx.tanggal}<br>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
    `;

    trx.items.forEach(item => {
        html += `
            <tr>
                <td>${item.nama}</td>
                <td>${item.satuan}</td>
                <td class="text-end">
                    ${ Number.isInteger(parseFloat(item.qty)) 
                        ? item.qty 
                        : parseFloat(item.qty).toFixed(2) }
                </td>
                <td class="text-end">Rp ${Number(item.harga).toLocaleString('id-ID')}</td>
                <td class="text-end">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th class="text-end">Rp ${Number(trx.total).toLocaleString('id-ID')}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Diskon</th>
                        <th class="text-end">Rp ${Number(trx.diskon).toLocaleString('id-ID')}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Grand Total</th>
                        <th class="text-end">Rp ${Number(trx.grand_total).toLocaleString('id-ID')}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;


        contentDiv.innerHTML = html;
    } catch (error) {
        contentDiv.innerHTML = `<div class="alert alert-danger">Gagal memuat detail: ${error.message}</div>`;
    }
}

</script>
@endsection
