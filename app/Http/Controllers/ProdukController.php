<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukSatuan;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // =============================
    // LIST PRODUK
    // =============================
    public function index(Request $request)
    {
        $query = Produk::with('satuan')->orderBy('nama_produk');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->search . '%')
                ->orWhere('barcode', 'like', '%' . $request->search . '%')
                ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }

        // Filter kategori
        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }

        // === PAGINATION ===
        $produk = $query->paginate(25)->appends($request->query());

        // List kategori untuk filter
        $kategori = Produk::select('kategori')->distinct()->orderBy('kategori')->get();

        return view('produk.index', compact('produk', 'kategori'));
    }

    public function ajaxSearch(Request $request)
    {
        $query = Produk::with('satuan')->orderBy('nama_produk');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->search . '%')
                ->orWhere('barcode', 'like', '%' . $request->search . '%')
                ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }

        $produk = $query->paginate(25)->appends($request->query());

        return response()->json([
            'data' => $produk->items()
        ]);
    }

    // =============================
    // FORM TAMBAH PRODUK
    // =============================
    public function create()
    {
        $kategori = Kategori::select('nama')->distinct()->orderBy('nama')->get();
        return view('produk.create', compact('kategori'));
    }


    // =============================
    // SIMPAN PRODUK BARU
    // =============================
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:191',
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:191',
            'stok' => 'required|numeric|min:0',
            'satuan_dasar' => 'required|string|max:100',
            'satuan' => 'nullable|array',
            'satuan.*.nama_satuan' => 'required_with:satuan|string|max:100',
            'satuan.*.konversi' => 'required_with:satuan|numeric',
            'satuan.*.harga_beli' => 'required_with:satuan|numeric|min:0',
            'satuan.*.harga_jual' => 'required_with:satuan|numeric|min:0',
        ]);


        $produk = Produk::create([
            'barcode' => $request->barcode,
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'satuan_dasar' => $request->satuan_dasar,
        ]);

        if ($request->filled('satuan')) {
            foreach ($request->satuan as $s) {
                if (!empty($s['nama_satuan'])) {
                    ProdukSatuan::create([
                        'produk_id' => $produk->id,
                        'nama_satuan' => $s['nama_satuan'],
                        'konversi' => (float) $s['konversi'],
                        'harga_beli' => (float) $s['harga_beli'],
                        'harga_jual' => (float) $s['harga_jual'],
                    ]);
                }
            }
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    // =============================
    // FORM EDIT PRODUK
    // =============================
    public function edit($id)
    {
        $produk = Produk::with('satuan')->findOrFail($id);
        $kategori = Kategori::select('nama')->distinct()->orderBy('nama')->get();

        return view('produk.edit', compact('produk', 'kategori'));
    }

    // =============================
    // UPDATE PRODUK + SATUAN
    // =============================
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Validasi
        $request->validate([
            'barcode' => 'nullable|string|max:191',
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:191',
            'stok' => 'required|numeric|min:0',
            'satuan_dasar' => 'required|string|max:100',
            'satuan' => 'nullable|array',
            'satuan.*.id' => 'nullable|integer|exists:produk_satuan,id',
            'satuan.*.nama_satuan' => 'required|string',
            'satuan.*.konversi' => 'required|numeric',
            'satuan.*.harga_beli' => 'required|numeric|min:0',
            'satuan.*.harga_jual' => 'required|numeric|min:0',
        ]);

        // Update produk
        $produk->update([
            'barcode' => $request->barcode,
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'stok' => $request->stok,
            'satuan_dasar' => $request->satuan_dasar,
        ]);

        // ===============================
        //      FIX UPDATE SATUAN
        // ===============================
        $existingIds = $produk->satuan()->pluck('id')->toArray();
        $submittedIds = [];

        if ($request->filled('satuan')) {

            foreach ($request->satuan as $s) {

                // Jika punya ID → update
                if (!empty($s['id'])) {

                    $submittedIds[] = $s['id'];

                    ProdukSatuan::where('id', $s['id'])->update([
                        'nama_satuan' => $s['nama_satuan'],
                        'konversi' => (float) $s['konversi'],
                        'harga_beli' => (float) $s['harga_beli'],
                        'harga_jual' => (float) $s['harga_jual'],
                    ]);

                } else {
                    // Insert baru
                    ProdukSatuan::create([
                        'produk_id' => $produk->id,
                        'nama_satuan' => $s['nama_satuan'],
                        'konversi' => $s['konversi'],
                        'harga_beli' => $s['harga_beli'],
                        'harga_jual' => $s['harga_jual'],
                    ]);
                }
            }
        }

        // ===============================
        //  HAPUS SATUAN YANG TIDAK ADA LAGI DI FORM
        // ===============================
        $toDelete = array_diff($existingIds, $submittedIds);

        if (!empty($toDelete)) {
            ProdukSatuan::whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }


    // =============================
    // HAPUS SATUAN
    // =============================
    public function deleteSatuan($id)
    {
        ProdukSatuan::findOrFail($id)->delete();
        return response()->json(['message' => 'Satuan dihapus']);
    }

    // =============================
    // HAPUS PRODUK
    // =============================
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->satuan()->delete();
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
