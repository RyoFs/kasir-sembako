<?php

namespace App\Http\Controllers;

use App\Models\StockLog;
use App\Models\Produk;
use App\Models\ProdukSatuan;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    // Tampilkan list stock log dengan search, filter tanggal, dan pagination
    public function index(Request $request)
    {
        $query = StockLog::with(['produk', 'satuan'])->orderBy('created_at', 'desc');

        // Filter berdasarkan nama produk
        if ($request->filled('search')) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('nama_produk', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Pagination 25 row per halaman
        $logs = $query->paginate(25)->withQueryString();

        return view('stock_logs.index', compact('logs'));
    }

    // Form tambah stock
    public function create()
    {
        $produk = Produk::with('satuan')->orderBy('nama_produk')->get();
        return view('stock_logs.create', compact('produk'));
    }

    // Simpan stock log
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'produk_satuan_id' => 'nullable|exists:produk_satuan,id',
            'type' => 'required|in:in,out',
            'qty' => 'required|numeric|min:0.01',
            'harga_beli' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $satuan = $request->produk_satuan_id ? ProdukSatuan::find($request->produk_satuan_id) : null;

        $qtyDasar = $satuan ? $request->qty * $satuan->konversi : $request->qty;

        if ($request->type == 'in') {
            $produk->stok += $qtyDasar;
        } else {
            if ($qtyDasar > $produk->stok) {
                return back()->withErrors(['qty' => 'Qty melebihi stok tersedia']);
            }
            $produk->stok -= $qtyDasar;
        }

        $produk->save();

        StockLog::create([
            'produk_id' => $produk->id,
            'produk_satuan_id' => $satuan?->id,
            'type' => $request->type,
            'qty' => $request->qty,
            'qty_dasar' => $qtyDasar,
            'harga_beli' => $request->harga_beli,
            'note' => $request->note,
        ]);

        return redirect()->route('stock_logs.index')->with('success','Stock berhasil dicatat');
    }

    // Form edit
    public function edit($id)
    {
        $log = StockLog::findOrFail($id);
        $produk = Produk::with('satuan')->orderBy('nama_produk')->get();
        return view('stock_logs.edit', compact('log','produk'));
    }

    // Update stock log
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'produk_satuan_id' => 'nullable|exists:produk_satuan,id',
            'type' => 'required|in:in,out',
            'qty' => 'required|numeric|min:0.01',
            'harga_beli' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $log = StockLog::findOrFail($id);
        $produk = Produk::findOrFail($request->produk_id);
        $satuan = $request->produk_satuan_id ? ProdukSatuan::find($request->produk_satuan_id) : null;

        $qtyDasarBaru = $satuan ? $request->qty * $satuan->konversi : $request->qty;

        // revert stok lama
        if ($log->type == 'in') $produk->stok -= $log->qty_dasar;
        else $produk->stok += $log->qty_dasar;

        // apply stok baru
        if ($request->type == 'in') $produk->stok += $qtyDasarBaru;
        else {
            if ($qtyDasarBaru > $produk->stok) {
                return back()->withErrors(['qty'=>'Qty melebihi stok tersedia']);
            }
            $produk->stok -= $qtyDasarBaru;
        }

        $produk->save();

        $log->update([
            'produk_id' => $produk->id,
            'produk_satuan_id' => $satuan?->id,
            'type' => $request->type,
            'qty' => $request->qty,
            'qty_dasar' => $qtyDasarBaru,
            'harga_beli' => $request->harga_beli,
            'note' => $request->note,
        ]);

        return redirect()->route('stock_logs.index')->with('success','Stock log berhasil diperbarui');
    }

    // Hapus log
    public function destroy($id)
    {
        $log = StockLog::findOrFail($id);
        $produk = Produk::findOrFail($log->produk_id);

        if ($log->type == 'in') $produk->stok -= $log->qty_dasar;
        else $produk->stok += $log->qty_dasar;

        $produk->save();
        $log->delete();

        return redirect()->route('stock_logs.index')->with('success','Stock log berhasil dihapus');
    }
}
