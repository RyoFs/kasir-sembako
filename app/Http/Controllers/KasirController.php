<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\ProdukSatuan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\KasirSession;
use App\Models\Settlement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirController extends Controller
{
    public function index()
    {
        // Ambil semua kategori unik dari tabel produk
        $kategoriList = Produk::select('kategori')
                        ->distinct()
                        ->whereNotNull('kategori')
                        ->pluck('kategori');

        return view('kasir.index', compact('kategoriList'));
    }


    public function searchProduk(Request $request)
    {
        $query = $request->query('query');

        $produk = Produk::with('satuanDasar') // ambil satuan dasar
            ->where('nama_produk', 'like', "%{$query}%")
            ->orWhere('barcode', $query)
            ->get(['id','nama_produk','barcode','stok','satuan_dasar']); // ambil kolom yang diperlukan

        $data = $produk->map(function($p){
            $satuan = $p->satuanDasar; // relasi satuan dasar
            return [
                'id' => $p->id,
                'nama' => $p->nama_produk,          // untuk JS: p.nama
                'barcode' => $p->barcode,
                'stok' => $p->stok,
                'harga' => $satuan->harga_jual ?? 0, // ambil harga_jual dari satuan dasar
                'unit_nama' => $satuan->nama_satuan ?? '', // ambil nama satuan
            ];
        });

        return response()->json($data);
    }

    // KasirController.php
    public function getAllProduk(Request $request)
    {
        $query = Produk::with('satuan')->orderBy('nama_produk', 'asc');

        // Filter nama
        if ($request->filled('nama')) {
            $query->where('nama_produk', 'like', '%'.$request->nama.'%');
        }

        // Filter kategori (case-insensitive)
        if ($request->filled('kategori')) {
            $kategori = trim($request->kategori);
            if ($kategori !== '') {
                $query->whereRaw('LOWER(TRIM(kategori)) = ?', [strtolower($kategori)]);
            }
        }

        $produk = $query->get()->map(function ($item) {
            $satuan = $item->satuan->first();
            return [
                'id' => $item->id,
                'nama_produk' => $item->nama_produk,
                'kategori' => $item->kategori,
                'stok' => $item->stok,
                'satuan_dasar' => $item->satuan_dasar,
                'harga_jual' => $satuan ? $satuan->harga_jual : 0,
                'nama_satuan' => $satuan ? $satuan->nama_satuan : '-',
            ];
        });

        return response()->json($produk);
    }


    
    public function addToCart(Request $request)
    {
        $produk = Produk::with('satuan')->find($request->produk_id);
        if (!$produk) return response()->json(['success' => false, 'error' => 'Produk tidak ditemukan']);

        if ($request->filled('satuan_id')) {
            $satuan = $produk->satuan()->where('id', $request->satuan_id)->first();
        } else {
            $satuan = $produk->satuan()->first();
        }
        if (!$satuan) return response()->json(['success' => false, 'error' => 'Satuan tidak ditemukan']);

        $cart = session('cart', []);
        $key = $produk->id . '-' . $satuan->id;

        $konversi = (float)($satuan->konversi ?? 1);
        $stokBase = (float)$produk->stok;

        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $cart[$key] = [
                'produk_id' => $produk->id,
                'nama' => $produk->nama_produk,
                'satuan_id' => $satuan->id,
                'satuan_nama' => $satuan->nama_satuan,
                'satuan_dasar' => $produk->satuan_dasar, // <-- tambahkan ini
                'harga' => $satuan->harga_jual,
                'konversi' => $konversi,
                'stok_base' => $stokBase,
                'qty' => 1,
                'diskon' => 0,
                'qty_in_base' => 1 * $konversi,
                'satuan_options' => $produk->satuan->map(function($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->nama_satuan,
                        'harga_jual' => $s->harga_jual,
                        'konversi' => $s->konversi ?? 1,
                    ];
                })->toArray(),
            ];
        }

        // update qty_in_base jika ada perubahan qty
        $cart[$key]['qty_in_base'] = $cart[$key]['qty'] * $cart[$key]['konversi'];

        session(['cart' => $cart]);
        return response()->json(['success' => true, 'cart' => $cart]);
    }




    // MODIFIKASI: Fungsi ini sekarang membangun ulang data keranjang dari DB untuk memastikan kelengkapan dan kebaruan data
    public function getCart()
    {
        $sessionCart = session('cart', []);
        $cart = [];

        foreach ($sessionCart as $key => $item) {
            $item['qty_in_base'] = $item['qty'] * ($item['konversi'] ?? 1);
            $item['stok_display'] = ($item['stok_base'] ?? 0) . ' ' . ($this->getSatuanDasarLabel($item) ?? '');
            $cart[$key] = $item;
        }

        return response()->json(['cart' => $cart]);
    }

    protected function getSatuanDasarLabel($item)
    {
        // prefer nilai satuan_dasar produk, fallback ke satuan_nama konversi=1
        return $item['satuan_nama'] ?? null;
    }



    public function updateQty(Request $request)
    {
        $key = $request->id;
        $qty = max(1, (int)$request->qty);

        $cart = session('cart', []);
        if (!isset($cart[$key])) return response()->json(['success' => false, 'message' => 'Item tidak ditemukan']);

        $cart[$key]['qty'] = $qty;
        $cart[$key]['qty_in_base'] = $qty * ($cart[$key]['konversi'] ?? 1);
        session(['cart' => $cart]);

        return response()->json(['success' => true, 'cart' => $cart]);
    }


    public function updateDiskon(Request $request)
    {
        $key = $request->key;
        $cart = session('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['diskon'] = (float)$request->diskon;
            session(['cart' => $cart]);
        }
        return response()->json(['success' => true]);
    }

    public function updateSatuan(Request $request)
    {
        $key = $request->id; // key lama
        $newSatuanId = $request->satuan_id;
        $cart = session('cart', []);

        if (!isset($cart[$key])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan']);
        }

        $produkId = $cart[$key]['produk_id'];
        $oldDiskon = $cart[$key]['diskon'] ?? 0;
        $qty = $cart[$key]['qty'] ?? 1;

        $produk = Produk::with('satuan')->find($produkId);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan']);

        $newSatuan = $produk->satuan()->find($newSatuanId);
        if (!$newSatuan) return response()->json(['success' => false, 'message' => 'Satuan tidak ditemukan']);

        $konversi = (float)($newSatuan->konversi ?? 1);
        $stokBase = (float)$produk->stok;

        // Update item di key lama, jangan hapus
        $cart[$key]['satuan_id'] = $newSatuan->id;
        $cart[$key]['satuan_nama'] = $newSatuan->nama_satuan;
        $cart[$key]['harga'] = $newSatuan->harga_jual;
        $cart[$key]['konversi'] = $konversi;
        $cart[$key]['qty_in_base'] = $qty * $konversi;
        $cart[$key]['satuan_options'] = $produk->satuan->map(fn($s) => [
            'id' => $s->id,
            'nama' => $s->nama_satuan,
            'harga_jual' => $s->harga_jual,
            'konversi' => $s->konversi ?? 1,
        ])->toArray();

        session(['cart' => $cart]);

        // Return hanya item yang diupdate, frontend bisa langsung replace tanpa reorder
        return response()->json([
            'success' => true,
            'updated_item' => $cart[$key]
        ]);
    }

    public function remove($id)
    {
        $key = urldecode($id); // decode key
        $cart = session('cart', []);
        if(isset($cart[$key])){
            unset($cart[$key]);
            session(['cart' => $cart]);
        }

        return response()->json(['success' => true]);
    }

    public function clear()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        // Gunakan getCart() untuk mendapatkan data keranjang yang paling lengkap dan terkini
        $cartData = $this->getCart()->getData(true)['cart'];
        if (empty($cartData)) {
            return response()->json(['success' => false, 'error' => 'Keranjang kosong']);
        }

        DB::beginTransaction();
        try {
            $tanggal = Carbon::now('Asia/Jakarta');
            $prefix = 'TRX' . $tanggal->format('Ymd');
            $last = Transaksi::whereDate('tanggal', $tanggal->toDateString())
                ->orderByDesc('created_at')
                ->first();
            $lastNum = 0;
            if ($last && preg_match('/TRX\d{8}(\d+)/', $last->id, $m)) $lastNum = (int) $m[1];
            $newId = $prefix . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

            // Hitung total berdasarkan data keranjang yang sudah diambil ulang
            $total = collect($cartData)->sum(fn($i) => ($i['harga'] - $i['diskon']) * $i['qty']);
            $diskonTotal = (float) $request->diskon_total;
            $bayar = (float) $request->bayar;
            $metode = $request->metode_pembayaran ?? 'cash';
            $grandTotal = max(0, $total - $diskonTotal);
            $kembali = max(0, $bayar - $grandTotal);

            // AMBIL settlement_id dari session
            $settlement = Settlement::where('user_id', Auth::id())
                ->where('status', 'open')
                ->latest()
                ->first();

            if (!$settlement) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Shift belum dibuka'
                ], 400);
            }


            $transaksi = Transaksi::create([
                'id' => $newId,
                'tanggal' => $tanggal,
                'total' => $grandTotal,
                'diskon' => $diskonTotal,
                'bayar' => $bayar,
                'kembali' => $kembali,
                'metode_pembayaran' => $metode,
                'user_id' => Auth::id(),
                'status' => 'selesai', 
                'settlement_id' => $settlement->id,
            ]);

            foreach ($cartData as $key => $item) {
            [$produkId, $satuanId] = explode('-', $key);
            $qty = (int) $item['qty'];
            $konversi = (float) ($item['konversi'] ?? 1);
            $qtyInBase = $qty * $konversi;

            // validasi stok saat ini (ambil ulang dari DB untuk akurasi)
            $produk = Produk::find($produkId);
            if (!$produk) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => "Produk ID $produkId tidak ditemukan"]);
            }

            if ($produk->stok < $qtyInBase) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => "Stok tidak cukup untuk produk {$produk->nama_produk}. Stok tersisa: {$produk->stok}"]);
            }

                TransaksiDetail::create([
                    'transaksi_id' => $newId,
                    'produk_id' => $produkId,
                    'satuan_id' => $satuanId,
                    'qty' => $qty,
                    'harga' => $item['harga'],
                    'subtotal' => ($item['harga'] - $item['diskon']) * $qty,
                ]);

                // Kurangi stok berdasarkan base unit, dengan safeguard
                $updated = Produk::where('id', $produkId)
                    ->where('stok', '>=', $qtyInBase)
                    ->decrement('stok', $qtyInBase);

                if (!$updated) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'error' => 'Gagal mengurangi stok (mungkin stok berubah). Silakan coba lagi.']);
                }
            }

            DB::commit();
            session()->forget('cart');

            return response()->json(['success' => true, 'kode_transaksi' => $newId]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

}