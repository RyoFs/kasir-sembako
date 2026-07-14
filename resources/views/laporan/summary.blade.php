@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Summary Penjualan</li>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Judul -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-graph-up-arrow text-primary"></i>
                Summary Penjualan Per Produk
            </h3>

            @if($startDate && $endDate)
                <small class="text-muted">
                    Periode :
                    <strong>
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    </strong>
                    -
                    <strong>
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </strong>
                </small>
            @else
                <small class="text-muted">
                    Menampilkan seluruh data penjualan
                </small>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <h6 class="fw-bold mb-3">
                <i class="bi bi-funnel-fill"></i>
                Filter Laporan
            </h6>

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal</label>
                    <input
                        type="date"
                        name="start_date"
                        class="form-control"
                        value="{{ request('start_date') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input
                        type="date"
                        name="end_date"
                        class="form-control"
                        value="{{ request('end_date') }}">
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel-fill"></i>
                            Filter
                        </button>

                        <a href="{{ route('laporan.summary') }}"
                           class="btn btn-secondary flex-fill">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </a>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <!-- Summary Card -->
    <div class="row mb-4">

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-receipt fs-1 text-primary"></i>
                    <h6 class="mt-2 text-muted">Jumlah Transaksi</h6>
                    <h3 class="fw-bold">{{ $jumlahTransaksi }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam fs-1 text-success"></i>
                    <h6 class="mt-2 text-muted">Qty Terjual</h6>
                    <h3 class="fw-bold">{{ $totalQtyTerjual }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-1 text-warning"></i>
                    <h6 class="mt-2 text-muted">Total Penjualan</h6>
                    <h5 class="fw-bold text-success">
                        Rp {{ number_format($totalPenjualanAll,0,',','.') }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-wallet2 fs-1 text-danger"></i>
                    <h6 class="mt-2 text-muted">Total Profit</h6>
                    <h5 class="fw-bold text-primary">
                        Rp {{ number_format($totalProfitAll,0,',','.') }}
                    </h5>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-table"></i>
                Detail Penjualan Produk
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-dark">

                        <tr class="text-center">
                            <th width="60">No</th>
                            <th class="text-start">
                                <i class="bi bi-box"></i>
                                Produk
                            </th>
                            <th width="120">
                                <i class="bi bi-boxes"></i>
                                Qty
                            </th>
                            <th width="180">
                                <i class="bi bi-currency-dollar"></i>
                                Penjualan
                            </th>
                            <th width="180">
                                <i class="bi bi-graph-up-arrow"></i>
                                Profit
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($profitPerProduk as $produk => $data)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <strong>{{ $produk }}</strong>
                            </td>

                            <td class="text-center">
                                {{ $data['qty'] }}
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($data['total_penjualan'],0,',','.') }}
                            </td>

                            <td class="text-end">

                                @if($data['profit'] >= 0)

                                    <span class="badge bg-success fs-6">
                                        Rp {{ number_format($data['profit'],0,',','.') }}
                                    </span>

                                @else

                                    <span class="badge bg-danger fs-6">
                                        Rp {{ number_format($data['profit'],0,',','.') }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-5">

                                <i class="bi bi-inbox display-5 text-muted"></i>

                                <br>

                                <span class="text-muted">
                                    Belum ada data penjualan pada periode ini.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection