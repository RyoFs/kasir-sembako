@extends('layouts.kasir')

@section('content')

<!-- PERBAIKAN UI: Container utama menggunakan Flexbox dengan min-height untuk bernapas -->
<div class="pos-layout-container">
    <!-- KOLOM KIRI: Pencarian dan Keranjang -->
    <div class="pos-main-content">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="mdi mdi-cart-outline"></i> Kasir Penjualan</h4>
            </div>
            <div class="card-body">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#produkModal">
                    <i class="mdi mdi-plus"></i> Tambah Produk
                </button>

                <!-- Modal Produk -->
                <div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="produkModalLabel">Daftar Produk</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Filter -->
                                <div class="row mb-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="filter-nama" class="form-label">Cari Produk</label>
                                        <input type="text" id="filter-nama" class="form-control" placeholder="Cari nama produk...">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="filter-kategori" class="form-label">Kategori</label>
                                        <select id="filter-kategori" class="form-select">
                                            <option value="">-- Semua Kategori --</option>
                                            @foreach($kategoriList as $kategori)
                                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex">
                                        <button type="button" class="btn btn-secondary me-2" onclick="resetProdukFilter()">Reset</button>
                                    </div>
                                </div>

                                <!-- Tabel Produk -->
                                <div id="produk-list-container" style="max-height:500px; overflow-y:auto;">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody id="produk-list-body">
                                        <!-- Data produk akan di-render via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search / Scan Produk -->
                <div class="position-relative mb-4">
                    <div class="input-group input-group-lg shadow-sm rounded" id="scan-box">
                        <span class="input-group-text bg-primary text-white">
                            <i class="mdi mdi-barcode-scan"></i>
                        </span>
                        <input type="text" id="search-produk" class="form-control border-0" 
                            placeholder="🔍 Scan barcode atau ketik nama produk..." 
                            autocomplete="off">
                    </div>
                    <div id="search-result" class="list-group position-absolute w-100 mt-1" style="z-index:999;"></div>
                </div>

                <!-- Keranjang -->
                <div id="cart-container" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: Bagian Pembayaran -->
    <div class="pos-sidebar-payment">
        <div id="payment-section" class="card p-0 shadow-sm h-100 d-flex flex-column" style="display:none;">
            
            <!-- PERBAIKAN UI: Bungkus metode pembayaran dengan div sticky -->
            <div class="payment-method-sticky p-3 border-bottom">
                <div class="mb-0">
                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="metode-btn btn btn-outline-primary flex-fill" data-metode="cash">
                            <i class="mdi mdi-cash"></i> Cash
                        </button>
                        <button type="button" class="metode-btn btn btn-outline-success flex-fill" data-metode="debit">
                            <i class="mdi mdi-credit-card-outline"></i> Debit
                        </button>
                        <button type="button" class="metode-btn btn btn-outline-info flex-fill" data-metode="qris">
                            <i class="mdi mdi-qrcode-scan"></i> QRIS
                        </button>
                    </div>
                    <select id="metode" class="d-none">
                        <option value="">-- Pilih --</option>
                        <option value="cash">Cash</option>
                        <option value="debit">Debit</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
            </div>
            
            <!-- PERBAIKAN UI: Bungkus sisa konten pembayaran dalam div yang bisa di-scroll -->
            <div class="payment-content-scrollable p-3 flex-grow-1" style="overflow-y: auto;">
                <!-- Diskon -->
                <div id="diskon-wrapper" class="mb-3 bg-light p-3 rounded shadow-sm" style="display:none;">
                    <label for="diskon_total" class="form-label fw-semibold">Diskon (Rp)</label>
                    <input type="text" id="diskon_total" class="form-control text-end fw-semibold fs-5" value="0" placeholder="0">
                </div>

                <!-- Total Setelah Diskon -->
                <div id="total-akhir-wrapper" class="mb-2 bg-light p-3 rounded shadow-sm" style="display:none;">
                    <label class="form-label fw-semibold">SUBTOTAL</label>
                    <input type="text" id="total_setelah_diskon" class="form-control text-end fw-semibold fs-5" readonly>
                </div>

                <!-- Nominal Uang (Cash) -->
                <div id="cash-options" class="mb-3" style="display:none;">
                    <label class="form-label fw-semibold">NOMINAL UANG</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button class="btn btn-secondary btn-sm flex-fill" onclick="pilihNominal(2000)">Rp 2.000</button>
                        <button class="btn btn-warning btn-sm flex-fill text-white" onclick="pilihNominal(5000)">Rp 5.000</button>
                        <button class="btn btn-info btn-sm flex-fill text-white" onclick="pilihNominal(10000)">Rp 10.000</button>
                        <button class="btn btn-success btn-sm flex-fill" onclick="pilihNominal(20000)">Rp 20.000</button>
                        <button class="btn btn-primary btn-sm flex-fill" onclick="pilihNominal(50000)">Rp 50.000</button>
                        <button class="btn btn-danger btn-sm flex-fill" onclick="pilihNominal(100000)">Rp 100.000</button>
                    </div>

                    <div class="kasir-payment-box">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label fw-semibold">TOTAL BAYAR</label>
                                <div class="input-group">
                                    <input type="text" id="bayar" class="form-control text-end fw-semibold fs-5" placeholder="Masukkan nominal" oninput="formatRupiah(this)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="resetNominal()" title="Reset">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="kembali-wrapper" class="col-12">
                                <label class="form-label fw-semibold">KEMBALIAN</label>
                                <input type="text" id="kembali" class="form-control text-end fw-semibold fs-5" readonly>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-success w-100 mt-2" onclick="bayarPas()">
                        💰 Bayar Pas
                    </button>
                </div>
            </div>
            
            <!-- PERBAIKAN UI: Tempatkan tombol aksi di bagian bawah yang tetap -->
            <div class="p-3 border-top mt-auto">
                <div class="d-grid gap-2">
                    <button id="btn-bersihkan" class="btn btn-danger" onclick="bersihkanKeranjang()" disabled>
                        <i class="mdi mdi-delete-empty"></i> Bersihkan Keranjang
                    </button>
                    <button id="btn-simpan" class="btn btn-success" onclick="simpanTransaksi()" disabled>
                        <i class="mdi mdi-content-save"></i> Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
{{-- Tambahkan Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
/*
  Kasir JS (refactor lengkap)
  - Fullscreen auto
  - Back to Dashboard
  - Search / scan / modal product
  - Cart (satuan konversi-aware)
  - Payment + save + print + open cashdrawer
*/

/* ==================== GLOBALS ==================== */
const BASE_URL = "{{ url('/') }}";
const CSRF = '{{ csrf_token() }}';

let cartData = {};
let totalBelanja = 0;
let totalSetelahDiskon = 0;
let totalBayar = 0;
let cartOrder = [];

// scanner
let scanBuffer = "";
let lastKeyTime = 0;

// debounce / abort
let searchTimeout = null;
let searchController = null;

/* ==================== UTILITIES ==================== */
function formatRupiah(angka) {
    if (angka === null || angka === undefined || isNaN(angka)) return 'Rp. 0';
    const isNegative = angka < 0;
    let formattedAngka = 'Rp. ' + Math.abs(Number(angka)).toLocaleString('id-ID');
    return isNegative ? '-' + formattedAngka : formattedAngka;
}
function parseRupiah(str) {
    return parseFloat(String(str || '').replace(/[^\d-]/g, '')) || 0;
}
function escapeHtml(u) {
    return String(u).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}
function debounce(fn, delay) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), delay);
    };
}
function showToast(icon, title, opts = {}) {
    Swal.fire(Object.assign({
        toast: true, position: 'top-end', icon, title, showConfirmButton: false, timer: 1400
    }, opts));
}

/* ==================== UI INIT (fullscreen + header) ==================== */
function ensureHeaderAndBackButton() {
    // add header if not exists
    if (!document.querySelector('.kasir-header')) {
        const header = document.createElement('div');
        header.className = 'kasir-header';
        header.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px">
                <button id="btn-back-dashboard" class="kasir-back-btn"><i class="mdi mdi-arrow-left"></i> Dashboard</button>
                <h1 style="margin:0;font-size:18px">Kasir - Penjualan</h1>
            </div>
            <div>
                <button id="btn-exit-full" class="kasir-back-btn" title="Keluar Fullscreen"><i class="mdi mdi-fullscreen-exit"></i></button>
            </div>
        `;
        document.body.prepend(header);

        document.getElementById('btn-back-dashboard').addEventListener('click', () => {
            window.location.href = "{{ url('/') }}"; // ubah jika dashboard di route lain
        });
        document.getElementById('btn-exit-full').addEventListener('click', () => {
            exitFullscreenMode();
        });
    }
}

function enterFullscreenMode() {
    document.body.classList.add('kasir-fullscreen');
    ensureHeaderAndBackButton();
    // Try requestFullscreen on html element for better full-screen effect
    const el = document.documentElement;
    if (el.requestFullscreen && !document.fullscreenElement) {
        el.requestFullscreen().catch(() => {});
    }
}
function exitFullscreenMode() {
    document.body.classList.remove('kasir-fullscreen');
    if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => {});
    }
}

/* ==================== CART (sync with server) ==================== */

async function fetchCart() {
    const container = document.getElementById('cart-container');
    if (container) container.innerHTML = `<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>`;
    try {
        const res = await fetch("{{ route('kasir.getCart') }}");
        const data = await res.json();
        cartData = data.cart || {};

        // inisialisasi cartOrder jika belum ada
        if (!cartOrder || !cartOrder.length) {
            cartOrder = Object.keys(cartData);
        } else {
            // update urutan agar key lama tetap
            const newOrder = [];
            for (let oldKey of cartOrder) {
                if (cartData[oldKey]) newOrder.push(oldKey);
            }
            for (let k of Object.keys(cartData)) {
                if (!newOrder.includes(k)) newOrder.push(k);
            }
            cartOrder = newOrder;
        }

        renderCart();
    } catch (err) {
        console.error('fetchCart error', err);
        if (container) container.innerHTML = `<div class="alert alert-danger">Gagal memuat keranjang.</div>`;
    }
}

async function onSatuanChange(key, satuanId) {
    try {
        const res = await fetch("{{ route('kasir.updateSatuan') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: key, satuan_id: parseInt(satuanId) })
        });
        const data = await res.json();

        if (!data.success) {
            showToast('error', data.message || 'Gagal update satuan');
            return;
        }

        // Ambil item yang diupdate dari server
        const updatedItem = data.updated_item; // pastikan backend mengembalikan item yang diupdate
        if (!updatedItem) return;

        // Hanya update properti item yang diubah, tanpa mengubah urutan
        cartData[key] = {
            ...cartData[key],
            ...updatedItem
        };

        // Render ulang cart, posisi row tetap
        renderCart();
    } catch (err) {
        console.error('onSatuanChange err', err);
        showToast('error', 'Gagal update satuan');
    }
}


function renderCart() {
    const container = document.getElementById('cart-container');
    if (!container) return;

    // Simpan posisi scroll relatif terhadap item pertama
    const firstRow = container.querySelector('tr.table-kasir-row');
    let firstRowOffsetTop = firstRow ? firstRow.offsetTop : 0;
    let scrollOffset = container.scrollTop - firstRowOffsetTop;

    let html = '';
    totalBelanja = 0;

    if (!cartOrder || !cartOrder.length) {
        container.innerHTML = `<div class="alert alert-info text-center">Keranjang kosong. Tambah produk terlebih dahulu.</div>`;
        document.getElementById('payment-section').style.display = 'none';
        updatePaymentButtons(false);
        hitungTotalSetelahDiskon();
        return;
    }

    html += `<div class="table-modern cart-scroll-container">
        <table class="table table-borderless mb-0">
            <thead><tr>
                <th>Produk</th>
                <th class="text-end">Harga</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
                <th class="text-center">Aksi</th>
            </tr></thead><tbody>`;

    for (let k of cartOrder) {
        const item = cartData[k];
        if (!item) continue;

        const harga = Number(item.harga ?? 0);
        const qty = Number(item.qty ?? 1);
        const subtotal = harga * qty;
        item.subtotal = subtotal;
        totalBelanja += subtotal;

        const stokBase = Number(item.stok_base ?? 0);
        const konversi = Number(item.konversi ?? 1);
        const maxQtyUnit = konversi > 0 ? Math.floor(stokBase / konversi) : 0;
        const stokDisplay = `${stokBase} ${item.satuan_dasar}`;

        html += `<tr class="table-kasir-row" data-key="${k}">
            <td>
                <div class="fw-semibold">${escapeHtml(item.nama)}</div>
                <small class="text-muted">Stok: ${escapeHtml(stokDisplay)}</small>
            </td>
            <td class="text-end">${formatRupiah(item.harga)}</td>
            <td class="text-center">
                <input type="number" class="form-control qty-input" 
                    value="${item.qty}" 
                    min="1" 
                    max="${maxQtyUnit}" 
                    step="1"
                    onchange="onQtyChange('${k}', this.value)" 
                    ${maxQtyUnit <= 0 ? 'disabled' : ''}>
                <div class="mt-1">
                    <select class="form-select form-select-sm" onchange="onSatuanChange('${k}', this.value)">
                        ${(item.satuan_options || []).map(s => `<option value="${s.id}" ${s.id == item.satuan_id ? 'selected' : ''} data-konv="${s.konversi ?? 1}">${escapeHtml(s.nama)}</option>`).join('')}
                    </select>
                </div>
            </td>
            <td class="text-end text-success fw-bold">${formatRupiah(subtotal)}</td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm btn-delete"><i class="bi bi-trash"></i></button>
            </td>
        </tr>`;
    }

    html += `</tbody></table></div>`;
    container.innerHTML = html;

    // Restore scroll posisi relatif
    const newFirstRow = container.querySelector('tr.table-kasir-row');
    if (newFirstRow) {
        container.scrollTop = newFirstRow.offsetTop + scrollOffset;
    }

    document.getElementById('payment-section').style.display = 'flex';
    updatePaymentButtons(true);
    hitungTotalSetelahDiskon();
    highlightTotalIfChanged();
}



// ==================== EVENT DELEGATION DELETE ====================

document.getElementById('cart-container').addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;

    const tr = btn.closest('tr');
    const key = tr.dataset.key;
    if (!key) return;

    onRemoveItem(key);
});

function updatePaymentButtons(enable) {
    const btnSimpan = document.getElementById('btn-simpan');
    const btnBersihkan = document.getElementById('btn-bersihkan');
    if (btnSimpan) btnSimpan.disabled = !enable;
    if (btnBersihkan) btnBersihkan.disabled = !enable;
}

/* ==================== CART ACTIONS (calls to backend) ==================== */
async function onQtyChange(key, qty) {
    qty = Math.max(1, parseInt(qty) || 1);
    try {
        const res = await fetch("{{ route('kasir.updateQty') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: key, qty })
        });
        const data = await res.json();
        if (!data.success) {
            showToast('error', data.message || 'Gagal update qty');
            await fetchCart();
            return;
        }
        cartData = data.cart || {};
        renderCart();
    } catch (err) {
        console.error('onQtyChange err', err);
        showToast('error', 'Terjadi kesalahan saat update qty');
        await fetchCart();
    }
}


async function onRemoveItem(encodedKey) {
    const key = decodeURIComponent(encodedKey);
    try {
        await fetch(`${BASE_URL}/kasir/remove/${encodeURIComponent(key)}`, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }
        });
        delete cartData[key];
        cartOrder = cartOrder.filter(k => k !== key); // hapus dari urutan
        renderCart();
    } catch (err) {
        console.error('onRemoveItem err', err);
    }
}


/* ==================== DISCOUT & PAYMENT ==================== */
function hitungTotalSetelahDiskon() {
    const diskonInput = document.getElementById('diskon_total');
    const diskon = diskonInput ? parseRupiah(diskonInput.value) : 0;
    totalSetelahDiskon = Math.max(0, totalBelanja - diskon);
    const field = document.getElementById('total_setelah_diskon');
    if (field) field.value = formatRupiah(totalSetelahDiskon);
    hitungKembalian();
}

function pilihNominal(nominal) {
    totalBayar = (Number(totalBayar) || 0) + Number(nominal || 0);
    const bayarField = document.getElementById('bayar');
    if (bayarField) bayarField.value = formatRupiah(totalBayar);
    hitungKembalian();
}

function resetNominal() {
    totalBayar = 0;
    const bayarField = document.getElementById('bayar');
    if (bayarField) bayarField.value = formatRupiah(0);
    hitungKembalian();
}

function bayarPas() {
    const diskon = parseRupiah(document.getElementById('diskon_total').value || 0);
    const total = Math.max(0, totalBelanja - diskon);
    totalBayar = total;
    const bayarField = document.getElementById('bayar');
    if (bayarField) bayarField.value = formatRupiah(total);
    hitungKembalian();
}

function hitungKembalian() {
    const metode = document.getElementById('metode').value || 'cash';
    const diskon = parseRupiah(document.getElementById('diskon_total').value || 0);
    const total = Math.max(0, totalBelanja - diskon);

    const kembaliField = document.getElementById('kembali');
    if (metode !== 'cash') {
        if (kembaliField) kembaliField.value = "Rp 0";
        return;
    }
    const kembali = Math.max(0, (Number(totalBayar) || 0) - total);
    if (kembaliField) {
        kembaliField.value = formatRupiah(kembali);
        kembaliField.classList.add('animate');
        setTimeout(() => kembaliField.classList.remove('animate'), 400);
    }
}

/* ==================== SAVE TRANSACTION + PRINT + CASHDRAWER ==================== */
async function simpanTransaksi() {
    const metode = document.getElementById('metode').value;
    const diskon = parseRupiah(document.getElementById('diskon_total').value || 0);
    const bayar = metode === 'cash' ? (Number(totalBayar) || 0) : totalSetelahDiskon;

    if (!metode) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih metode pembayaran dulu!' });
        return;
    }
    if (metode === 'cash' && (Number(totalBayar) || 0) < totalSetelahDiskon) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pembayaran cash kurang!' });
        return;
    }

    try {
        const res = await fetch("{{ route('kasir.store') }}", {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ metode_pembayaran: metode, diskon_total: diskon, bayar })
        });
        const result = await res.json();
        if (!result.success) {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: result.error || 'Terjadi kesalahan.' });
            return;
        }

        await Swal.fire({ icon: 'success', title: 'Transaksi Berhasil!', text: `Kode: ${result.kode_transaksi}`, timer: 1500, showConfirmButton: false });

        // PRINT via electronAPI (if tersedia)
        if (window.electronAPI) {
            try {
                const isCash = metode === 'cash';
                const receiptData = {
                    header: {
                        toko: 'TOKO DZABAR ASRI',
                        alamat: 'Jl. Telaga Malimping ',
                        telp: '0882-9146-6517',
                        kasir: '{{ Auth::user()->nama }}',
                        kode: result.kode_transaksi,
                        tanggal: new Date().toLocaleDateString('id-ID'),
                        waktu: new Date().toLocaleTimeString('id-ID')
                    },
                    items: Object.values(cartData).map(i => ({
                        nama: i.nama,
                        qty: i.qty,
                        satuan: i.satuan_nama,
                        harga: i.harga,
                        subtotal: i.subtotal
                    })),
                    total: totalBelanja,
                    diskon: diskon,
                    grand_total: totalSetelahDiskon,
                    bayar: isCash ? (Number(totalBayar) || 0) : null,
                    kembali: isCash ? Math.max(0, (Number(totalBayar) || 0) - totalSetelahDiskon) : null,
                    metode: metode.toUpperCase()
                };

                // print
                if (window.electronAPI.printReceipt) {
                    window.electronAPI.printReceipt(receiptData).catch(err => console.error('printReceipt err', err));
                }

            } catch (err) {
                console.error('Print', err);
            }
        }

        // reset & fetch fresh cart (backend cleared it)
        await fetchCart();
        resetPaymentForm();

    } catch (err) {
        console.error('simpanTransaksi err', err);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menyimpan transaksi.' });
    }
}

/* ==================== CLEAN CART ==================== */
async function bersihkanKeranjang() {
    const result = await Swal.fire({
        title: 'Kosongkan Keranjang?', text: 'Semua item akan dihapus dari keranjang.', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
    });
    if (!result.isConfirmed) return;

    try {
        const res = await fetch("{{ route('kasir.clear') }}", { method: 'GET', headers: { 'X-CSRF-TOKEN': CSRF }});
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Keranjang dikosongkan.' });
            await fetchCart();
            resetPaymentForm();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengosongkan keranjang.' });
        }
    } catch (err) {
        console.error('bersihkanKeranjang err', err);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengosongkan keranjang.' });
    }
}

/* ==================== SEARCH & ADD (modal & scanner) ==================== */
async function searchProdukAPI(q) {
    const url = `{{ url('/kasir/search-produk') }}?query=${encodeURIComponent(q)}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('search failed');
    return await res.json();
}

async function pilihProduk(barcode) {
    try {
        // search by barcode
        const res = await fetch(`{{ url('/kasir/search-produk') }}?query=${encodeURIComponent(barcode)}`);
        if (!res.ok) throw new Error('search failed');
        const data = await res.json();
        const produk = data.find(p => p.barcode === barcode || String(p.id) === String(barcode));
        if (!produk) {
            Swal.fire({ icon: 'error', title: 'Produk Tidak Ditemukan', text: `Barcode: ${barcode}` });
            return;
        }
        if (produk.stok <= 0) {
            Swal.fire({ icon: 'warning', title: 'Stok Habis!', text: `Produk "${produk.nama}" tidak tersedia.` });
            return;
        }

        // call addToCart endpoint
        await fetch("{{ route('kasir.addToCart') }}", {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ produk_id: produk.id })
        });

        await fetchCart();
        showToast('success', `"${produk.nama}" ditambahkan ke keranjang`);
    } catch (err) {
        console.error('pilihProduk err', err);
        Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal menambahkan produk' });
    }
}

/* add from modal (tambah button) */
async function tambahKeKeranjang(produkId, satuanId = null) {
    try {
        await fetch("{{ route('kasir.addToCart') }}", {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ produk_id: produkId, satuan_id: satuanId })
        });

        // tambahkan key baru ke cartOrder agar urutan stabil
        const resCart = await fetch("{{ route('kasir.getCart') }}");
        const data = await resCart.json();
        const newCart = data.cart || {};

        // update cartOrder
        for (let k of Object.keys(newCart)) {
            if (!cartOrder.includes(k)) cartOrder.push(k);
        }
        cartData = newCart;

        renderCart();
        showToast('success', 'Produk ditambahkan ke keranjang');
    } catch (err) {
        console.error('tambahKeKeranjang err', err);
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menambahkan produk ke keranjang' });
    }
}

/* product list loader for modal */
async function loadProdukList() {
    const nama = document.getElementById('filter-nama')?.value || '';
    const kategori = document.getElementById('filter-kategori')?.value || '';
    try {
        const res = await fetch(`{{ url('/kasir/get-all-produk') }}?nama=${encodeURIComponent(nama)}&kategori=${encodeURIComponent(kategori)}`);
        if (!res.ok) throw new Error('Gagal load produk');
        const data = await res.json();

        const tbody = document.getElementById('produk-list-body');
        if (!tbody) return;

        tbody.innerHTML = data.map(p => `
            <tr>
                <td>${escapeHtml(p.nama_produk)}</td>
                <td>${escapeHtml(p.kategori)}</td>
                <td>${formatRupiah(p.harga_jual)}</td>
                <td>${p.stok} ${escapeHtml(p.satuan_dasar || '')}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="tambahKeKeranjang(${p.id})">
                        <i class="mdi mdi-cart-plus"></i> Tambah
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('loadProdukList err', err);
    }
}
document.getElementById('filter-nama')?.addEventListener('input', () => loadProdukList());
document.getElementById('filter-kategori')?.addEventListener('change', () => loadProdukList());

function resetProdukFilter() {
    const namaInput = document.getElementById('filter-nama');
    const kategoriSelect = document.getElementById('filter-kategori');
    if(namaInput) namaInput.value = '';
    if(kategoriSelect) kategoriSelect.value = '';
    loadProdukList(); // reload daftar produk tanpa filter
}

/* ==================== SEARCH UI (debounce + abort) ==================== */
const handleSearchInput = debounce(async (val, resultContainer, searchInput) => {
    if (!val || val.length < 2) {
        resultContainer.style.display = 'none';
        resultContainer.innerHTML = '';
        return;
    }
    if (searchController) searchController.abort();
    searchController = new AbortController();
    const signal = searchController.signal;
    try {
        const res = await fetch(`{{ url('/kasir/search-produk') }}?query=${encodeURIComponent(val)}`, { signal });
        if (!res.ok) throw new Error('search fail');
        const data = await res.json();
        if (!data.length) {
            resultContainer.innerHTML = `<div class="p-3 text-center text-muted">Tidak ada produk ditemukan</div>`;
        } else {
            resultContainer.innerHTML = data.map(p => `
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="pilihProduk('${p.barcode || p.id}')">
                    <div>
                        <strong>${escapeHtml(p.nama)}</strong><br>
                        <span class="text-primary">${formatRupiah(p.harga)}</span><br>
                        <small class="text-muted">Stok: ${p.stok} ${escapeHtml(p.unit_nama || '')}</small>
                    </div>
                    <span class="badge bg-primary rounded-pill"><i class="mdi mdi-plus"></i></span>
                </div>
            `).join('');
        }
        resultContainer.style.display = 'block';
    } catch (err) {
        if (err.name === 'AbortError') return;
        console.error('search error', err);
    }
}, 260);

/* ==================== INIT / EVENTS ==================== */
function highlightTotalIfChanged() {
    const el = document.getElementById('total-belanja-cell');
    if (!el) return;
    el.textContent = formatRupiah(totalBelanja);
    el.classList.add('total-highlight');
    setTimeout(() => el.classList.remove('total-highlight'), 380);
}

function highlightInput(id) {
    const el = document.getElementById(id);
    el.classList.remove('input-pulse'); // hapus class dulu

    // force reflow supaya animasi bisa jalan lagi
    void el.offsetWidth;

    el.classList.add('input-pulse');

    // optional: hapus class setelah animasi selesai supaya bisa di-trigger lagi
    setTimeout(() => {
        el.classList.remove('input-pulse');
    }, 500); // sesuai durasi animasi
}

// Update subtotal
function updateSubtotal(value) {
    const subtotalInput = document.getElementById('total_setelah_diskon');
    subtotalInput.value = formatRupiahValue(value); // pastikan fungsi formatRp dipakai
    highlightInput('total_setelah_diskon');
}

// Update kembalian
function updateKembalian(value) {
    const kembaliInput = document.getElementById('kembali');
    kembaliInput.value = formatRupiahValue(value);
    highlightInput('kembali');
}



function resetPaymentForm() {
    resetNominal();
    const disk = document.getElementById('diskon_total');
    if (disk) disk.value = formatRupiah(0);

    // default metode = cash
    const metodeSelect = document.getElementById('metode');
    if (metodeSelect) metodeSelect.value = 'cash';
    document.querySelectorAll('.metode-btn').forEach(b => b.classList.remove('active'));
    const cashBtn = document.querySelector('.metode-btn[data-metode="cash"]');
    if (cashBtn) cashBtn.classList.add('active');

    const searchInput = document.getElementById('search-produk');
    if (searchInput) {
        searchInput.value = '';
        searchInput.disabled = false;
        searchInput.readOnly = false;
    }
    const resultContainer = document.getElementById('search-result');
    if (resultContainer) { resultContainer.style.display = 'none'; resultContainer.innerHTML = ''; }

    if (searchController) { searchController.abort(); searchController = null; }
    if (searchTimeout) { clearTimeout(searchTimeout); searchTimeout = null; }

    ubahMetode();
}

function ubahMetode() {
    const metode = document.getElementById('metode').value || 'cash';
    const diskWrap = document.getElementById('diskon-wrapper');
    const cashOpt = document.getElementById('cash-options');
    const totalWrap = document.getElementById('total-akhir-wrapper');

    if (diskWrap) diskWrap.style.display = 'block';
    if (totalWrap) totalWrap.style.display = 'block';
    if (cashOpt) cashOpt.style.display = (metode === 'cash') ? 'block' : 'none';
    hitungTotalSetelahDiskon();
}

window.addEventListener('DOMContentLoaded', () => {
    // Fullscreen + header
    enterFullscreenMode();

    // init cart & ui
    fetchCart();
    resetPaymentForm();

    // metode button handlers
    document.querySelectorAll('.metode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.metode-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const metodeSelect = document.getElementById('metode');
            if (metodeSelect) metodeSelect.value = this.dataset.metode;
            ubahMetode();
        });
    });

    // diskon input format
    const diskInput = document.getElementById('diskon_total');
    if (diskInput) {
        diskInput.addEventListener('input', (e) => {
            const val = parseRupiah(e.target.value);
            e.target.value = formatRupiah(val);
            hitungTotalSetelahDiskon();
        });
    }

    // bayar input format
    const bayarInput = document.getElementById('bayar');
    if (bayarInput) {
        bayarInput.addEventListener('input', (e) => {
            const val = parseRupiah(e.target.value);
            e.target.value = formatRupiah(val);
            totalBayar = val;
            hitungKembalian();
        });
    }

    // modal product load
    const produkModal = document.getElementById('produkModal');
    if (produkModal) produkModal.addEventListener('shown.bs.modal', () => loadProdukList());

    // search input handler
    const searchInput = document.getElementById('search-produk');
    const resultContainer = document.getElementById('search-result');
    if (searchInput && resultContainer) {
        searchInput.addEventListener('input', (e) => handleSearchInput(e.target.value.trim(), resultContainer, searchInput));
    }

    // click outside search => hide
    document.addEventListener('click', (e) => {
        const searchInput = document.getElementById('search-produk');
        const resultContainer = document.getElementById('search-result');
        if (!searchInput || !resultContainer) return;
        if (!searchInput.contains(e.target) && !resultContainer.contains(e.target)) {
            resultContainer.style.display = 'none';
        }
    });

    // barcode scanner (keyboard emulation)
    document.addEventListener('keydown', async (e) => {
        const searchInput = document.getElementById('search-produk');
        if (searchInput && document.activeElement === searchInput) return;

        const now = Date.now();
        const diff = now - lastKeyTime;
        lastKeyTime = now;

        if (e.key === 'Enter') {
            if (scanBuffer.length >= 3) {
                const code = scanBuffer.trim();
                scanBuffer = '';
                await pilihProduk(code);
                return;
            } else {
                scanBuffer = '';
            }
        }

        if (diff < 40) {
            scanBuffer += e.key;
        } else {
            scanBuffer = e.key.match(/^[a-zA-Z0-9]+$/) ? e.key : '';
        }

        // safety: clear buffer after 800ms idle
        if (scanBuffer.length && typeof window._scanBufferTimeout !== 'undefined') {
            clearTimeout(window._scanBufferTimeout);
        }
        window._scanBufferTimeout = setTimeout(() => scanBuffer = '', 900);
    });
});

/* ==================== EXPORT untuk blade onclick (expose) ==================== */
/* fungsi ini sudah digunakan di HTML: tambahKeKeranjang, pilihProduk, bersihkanKeranjang, simpanTransaksi */
window.tambahKeKeranjang = tambahKeKeranjang;
window.pilihProduk = pilihProduk;
window.bersihkanKeranjang = bersihkanKeranjang;
window.simpanTransaksi = simpanTransaksi;
window.loadProdukList = loadProdukList;
</script>

@endsection