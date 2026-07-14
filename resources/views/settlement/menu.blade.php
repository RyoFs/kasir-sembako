@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item">Settlement</li>
<li class="breadcrumb-item active">Settlement Shift</li>
@endsection

@section('content')

<style>
    .summary-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
    }
    .summary-title {
        font-weight: 600;
        font-size: 18px;
    }
    .summary-value {
        font-size: 22px;
        font-weight: bold;
    }
</style>

<div class="container py-4">

    <h3 class="mb-4 text-primary fw-bold">
        🧾 Settlement Shift
    </h3>

    @if(!$settlement)
        <div class="alert alert-warning text-center py-5">
            <h4 class="mb-2">⚠ Tidak Ada Shift Aktif</h4>
            <p class="mb-3">Silakan buka shift terlebih dahulu.</p>
            <a href="{{ url('kasir') }}" class="btn btn-primary">Buka Shift</a>
        </div>

    @else

    <!-- ===================== RINGKASAN ===================== -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="summary-card bg-primary shadow">
                <div class="summary-title">Total Penjualan</div>
                <div class="summary-value">Rp {{ number_format($total_sales,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-success shadow">
                <div class="summary-title">Total Diskon</div>
                <div class="summary-value">Rp {{ number_format($total_discount,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-dark shadow">
                <div class="summary-title">Shift Dibuka</div>
                <div class="summary-value">{{ $settlement->opened_at }}</div>
            </div>
        </div>

    </div>

    <!-- ===================== METODE PEMBAYARAN ===================== -->
    <div class="row g-4 mb-5">

        <div class="col-md-4">
            <div class="summary-card bg-warning shadow text-dark">
                <div class="summary-title">Penjualan Cash</div>
                <div class="summary-value">Rp {{ number_format($total_cash_sales,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-info shadow text-dark">
                <div class="summary-title">Penjualan QRIS</div>
                <div class="summary-value">Rp {{ number_format($total_qris_sales,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-secondary shadow">
                <div class="summary-title">Penjualan Debit</div>
                <div class="summary-value">Rp {{ number_format($total_debit_sales,0,',','.') }}</div>
            </div>
        </div>

    </div>

    <!-- ===================== PRODUK TERJUAL ===================== -->
    <div class="card shadow">
        <div class="card-body">
            <h4 class="fw-bold text-primary mb-3">🛒 Produk Terjual</h4>
            <hr>

            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Produk</th>
                        <th>Satuan</th>
                        <th width="120">Harga</th>
                        <th width="70" class="text-center">Qty</th>
                        <th width="120">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>{{ $p['nama'] }}</td>
                        <td>{{ $p['satuan'] }}</td>
                        <td>Rp {{ number_format($p['harga'],0,',','.') }}</td>
                        <td class="text-center">{{ $p['qty'] }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($p['total'],0,',','.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted">
                            Tidak ada produk terjual pada shift ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
    @endif
</div>

@endsection
