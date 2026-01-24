<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\StockLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

// Halaman dashboard
Route::get('/', function() {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Auth
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Pengaturan User
Route::get('/pengaturan/user', [UserController::class, 'index'])->name('user.index');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id}/delete', [UserController::class, 'destroy'])->name('user.destroy');


// Manajemen Stok
// Stock Logs
Route::prefix('stock_logs')->group(function () {
    Route::get('/', [StockLogController::class, 'index'])->name('stock_logs.index');       // Tampilkan list stock logs
    Route::get('/create', [StockLogController::class, 'create'])->name('stock_logs.create'); // Form input stock in/out
    Route::post('/store', [StockLogController::class, 'store'])->name('stock_logs.store');   // Simpan stock log
    Route::get('/{id}/edit', [StockLogController::class, 'edit'])->name('stock_logs.edit');  // Form edit log
    Route::put('/{id}', [StockLogController::class, 'update'])->name('stock_logs.update');   // Update log
    Route::delete('/{id}', [StockLogController::class, 'destroy'])->name('stock_logs.destroy'); // Hapus log
    Route::get('/create/{produk}', [StockLogController::class, 'createFromProduk'])->name('stock_logs.createFromProduk');
});


// manajemen produk
Route::middleware(['auth'])->group(function () {
    // 🔹 Kategori
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    
    Route::prefix('produk')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/ajax-search', [ProdukController::class, 'ajaxSearch'])->name('produk.ajaxSearch');
    Route::get('/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    // DELETE satuan per baris
    Route::delete('/satuan/{id}', [ProdukController::class, 'deleteSatuan'])->name('produk.satuan.delete');
});

});

// Semua route kasir / transaksi
Route::middleware('auth')->group(function () {
    Route::prefix('kasir')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/open-day', [KasirController::class, 'openDay'])->name('kasir.open');
    Route::post('/end-day', [KasirController::class, 'endDay'])->name('kasir.end');
    Route::get('/search-produk', [KasirController::class, 'searchProduk'])->name('kasir.search');
    Route::get('/get-all-produk', [KasirController::class, 'getAllProduk'])->name('kasir.getAllProduk');
    Route::get('/get-cart', [KasirController::class, 'getCart'])->name('kasir.getCart');
    Route::post('/add', [KasirController::class, 'addToCart'])->name('kasir.addToCart');
    Route::post('/update-qty', [KasirController::class, 'updateQty'])->name('kasir.updateQty');
    Route::post('/updateDiskon', [KasirController::class, 'updateDiskon'])->name('kasir.updateDiskon');
    Route::post('/update-satuan', [KasirController::class, 'updateSatuan'])->name('kasir.updateSatuan');
    Route::post('/remove/{id}', [KasirController::class, 'remove'])->name('kasir.remove');
    Route::get('/clear', [KasirController::class, 'clear'])->name('kasir.clear');
    Route::post('/store', [KasirController::class, 'store'])->name('kasir.store');
    });

    // Route untuk Settlement
Route::prefix('settlement')->middleware('auth')->group(function () {
    Route::get('/settlement', [SettlementController::class, 'menu'])->name('settlement.menu');
    Route::post('/open', [SettlementController::class, 'open'])->name('settlement.open');
    Route::post('/close', [SettlementController::class, 'close'])->name('settlement.close');
    Route::get('/status', [SettlementController::class, 'status'])->name('settlement.status');
    Route::get('/total-cash-shift', [SettlementController::class, 'totalCashShift'])->name('settlement.totalCashShift');
    Route::post('/settlement/print-shift', [SettlementController::class, 'printShift'])->name('settlement.printShift');
    Route::get('/history', [SettlementController::class, 'history'])->name('settlement.history');
    Route::get('/detail/{id}', [SettlementController::class, 'detail'])->name('settlement.detail');

});

// Route untuk Laporan
Route::middleware('auth')->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/get-transaksi/{id}', [LaporanController::class, 'getTransaksiForPrint'])->name('laporan.getTransaksiForPrint');
    Route::get('/laporan/summary', [LaporanController::class, 'summary'])->name('laporan.summary');
});

});
