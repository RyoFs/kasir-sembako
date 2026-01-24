@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold display-6 text-primary mb-2">Dashboard Kasir Sembako</h2>
        <p class="text-muted">Pilih menu untuk mulai bekerja. Aplikasi ini dirancang agar cepat, efisien, dan mudah digunakan.</p>
        <hr class="mt-4" style="opacity: .15;">
    </div>

    {{-- MENU UTAMA --}}
    <h5 class="fw-bold mb-3 text-secondary">Menu Utama</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ url('/') }}" class="menu-card">
                <i class="mdi mdi-home-variant-outline icon"></i>
                <span>Home</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('kasir.index') }}" class="menu-card">
                <i class="mdi mdi-cart-outline icon"></i>
                <span>Kasir</span>
            </a>
        </div>
    </div>

    {{-- PRODUCT MANAGEMENT --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Product Management</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('kategori.index') }}" class="menu-card">
                <i class="mdi mdi-format-list-bulleted-type icon"></i>
                <span>Kategori</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('produk.index') }}" class="menu-card">
                <i class="mdi mdi-cube-outline icon"></i>
                <span>Items</span>
            </a>
        </div>
    </div>

    {{-- TRANSAKSI --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Transaksi Stok</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('stock_logs.index') }}" class="menu-card">
                <i class="mdi mdi-database-plus icon"></i>
                <span>Stock Logs</span>
            </a>
        </div>
    </div>

    {{-- SETTLEMENT --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Settlement</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('settlement.menu') }}" class="menu-card">
                <i class="mdi mdi-book-multiple-outline icon"></i>
                <span>Settlement Shift</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('settlement.history') }}" class="menu-card">
                <i class="mdi mdi-chart-bar-outline icon"></i>
                <span>Settlement History</span>
            </a>
        </div>
    </div>

    {{-- LAPORAN --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Laporan</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('laporan.index') }}" class="menu-card">
                <i class="mdi mdi-file-document-outline icon"></i>
                <span>Logs Penjualan</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('laporan.summary') }}" class="menu-card">
                <i class="mdi mdi-chart-line icon"></i>
                <span>Summary Penjualan</span>
            </a>
        </div>
    </div>

    {{-- PENGATURAN --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Pengaturan</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('user.index') }}" class="menu-card">
                <i class="mdi mdi-account-cog-outline icon"></i>
                <span>Pengaturan User</span>
            </a>
        </div>
    </div>

    {{-- AKUN --}}
    <h5 class="fw-bold mt-5 mb-3 text-secondary">Akun</h5>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="menu-card menu-danger">
                <i class="mdi mdi-logout icon"></i>
                <span>Keluar</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

</div>

{{-- STYLE --}}
<style>
.menu-card {
    background: #fff;
    padding: 20px 10px;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #333;
    font-weight: 600;
    font-size: 15px;
    text-align: center;
    transition: all .23s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: 1px solid #efefef;
}

.menu-card .icon {
    font-size: 38px;
    margin-bottom: 8px;
    color: #0d6efd;
    transition: .23s;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.12);
}

.menu-card:hover .icon {
    color: #003b9f;
}

/* RED CARD */
.menu-danger {
    background: #ffefef !important;
    border-color: #ffb7b7 !important;
}

.menu-danger .icon {
    color: #d60000 !important;
}

.menu-danger:hover {
    background: #ffd4d4 !important;
}
</style>
@endsection
