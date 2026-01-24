@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Summary Penjualan Per Produk</h4>

    {{-- Filter tanggal --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-auto">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill"></i> Filter</button>
        </div>
        <div class="col-auto">
            <a href="{{ route('laporan.summary') }}" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>

    {{-- Summary Card --}}
    <div class="d-flex justify-content-end mb-4">
        <div class="card shadow-lg rounded-3 p-3 text-center me-3" style="width: 200px; background: linear-gradient(145deg, #d4edda, #e9f7ef);">
            <i class="bi bi-currency-dollar fs-3 mb-2 text-success"></i>
            <h6>Total Penjualan</h6>
            <h5 class="fw-bold">Rp {{ number_format($totalPenjualanAll,0,',','.') }}</h5>
        </div>
        <div class="card shadow-lg rounded-3 p-3 text-center" style="width: 200px; background: linear-gradient(145deg, #fff3cd, #fef9e7);">
            <i class="bi bi-wallet2 fs-3 mb-2 text-warning"></i>
            <h6>Total Profit</h6>
            <h5 class="fw-bold">Rp {{ number_format($totalProfitAll,0,',','.') }}</h5>
        </div>
    </div>

    {{-- Tabel Produk --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Qty Terjual</th>
                    <th>Total Penjualan</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($profitPerProduk as $produk => $data)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $produk }}</td>
                        <td class="text-center">{{ $data['qty'] }}</td>
                        <td class="text-end">Rp {{ number_format($data['total_penjualan'],0,',','.') }}</td>
                        <td class="text-end">
                            @if($data['profit'] >= 0)
                                <span class="text-success fw-bold">Rp {{ number_format($data['profit'],0,',','.') }}</span>
                            @else
                                <span class="text-danger fw-bold">Rp {{ number_format($data['profit'],0,',','.') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
