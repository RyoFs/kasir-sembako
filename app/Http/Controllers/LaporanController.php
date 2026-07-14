<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\StockLog;
use App\Models\Produk;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan penjualan.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = \App\Models\Transaksi::with('user')->orderBy('created_at', 'desc');

        // Filter tanggal hanya jika keduanya diisi
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $transaksis = $query->paginate(10);

        return view('laporan.index', [
            'transaksis' => $transaksis,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Mengambil data transaksi lengkap untuk keperluan reprint.
     * Dipanggil via AJAX.
     */
    public function getTransaksiForPrint($id)
    {
        try {
            $transaksi = Transaksi::with(['user', 'details.produk','details.satuan'])->findOrFail($id);

            // Format tanggal berdasarkan created_at
            $tanggalFormatted = \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i');
            $waktuFormatted = \Carbon\Carbon::parse($transaksi->created_at)->format('H:i:s');

            $receiptData = [
                'kode_transaksi' => $transaksi->id,
                'kasir' => $transaksi->user->nama,
                'tanggal' => $tanggalFormatted,
                'waktu' => $waktuFormatted,
                'items' => $transaksi->details->map(function ($detail) {
                    return [
                        'nama' => $detail->produk->nama_produk,
                        'satuan' => $detail->satuan ? $detail->satuan->nama_satuan : 'pcs',
                        'qty' => $detail->qty,
                        'harga' => $detail->harga,
                        'subtotal' => $detail->subtotal,
                    ];
                }),
                'total' => $transaksi->total + $transaksi->diskon,
                'diskon' => $transaksi->diskon,
                'grand_total' => $transaksi->total,
                'bayar' => $transaksi->bayar,
                'kembali' => $transaksi->kembali,
            ];

            return response()->json([
                'success' => true,
                'data' => $receiptData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transaksi.'
            ], 500);
        }
    }

public function summary(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    /* ===============================
       1. AMBIL TRANSAKSI
    ================================*/
    $transaksiQuery = \App\Models\Transaksi::with('details.produk.satuan');

    if ($startDate && $endDate) {
        $transaksiQuery->whereBetween('tanggal', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay()
        ]);
    }

    $transaksis = $transaksiQuery->get();
    $jumlahTransaksi = $transaksis->count();


    /* ====================================
    2. HITUNG AVERAGE COST
    ==================================== */

    // Ambil seluruh stok masuk dan hitung total nilai pembelian serta total qty dasar
    $log = StockLog::where('type', 'in')
        ->selectRaw('
            produk_id,
            SUM(qty * harga_beli) AS total_harga_beli,
            SUM(qty_dasar) AS total_qty_dasar
        ')
        ->groupBy('produk_id')
        ->get();

    // Hitung Average Cost per produk
    $avgCost = [];

    foreach ($log as $item) {

        $avgCost[$item->produk_id] = ($item->total_qty_dasar > 0)
            ? ($item->total_harga_beli / $item->total_qty_dasar)
            : 0;
    }



    /* ===============================
       3. HITUNG PROFIT PER PRODUK DENGAN AVG COST
    ================================*/
    $profitPerProduk = [];
    $totalPenjualanAll = 0;
    $totalProfitAll = 0;

    foreach ($transaksis as $trx) {
        foreach ($trx->details as $item) {

            $produkId = $item->produk_id;
            $namaProduk = $item->produk->nama_produk;

            $qty = $item->qty;
            $hargaJual = $item->harga;

            // Ambil average cost dari database
            $avgCostProduk = $avgCost[$produkId] ?? 0;

            // Perhitungan profit
            $profit = ($hargaJual - $avgCostProduk) * $qty;
            $totalPenjualanProduk = $hargaJual * $qty;

            // Siapkan slot array jika belum ada
            if (!isset($profitPerProduk[$namaProduk])) {
                $profitPerProduk[$namaProduk] = [
                    'qty' => 0,
                    'total_penjualan' => 0,
                    'profit' => 0,
                    'average_cost' => $avgCostProduk,
                ];
            }

            $profitPerProduk[$namaProduk]['qty'] += $qty;
            $profitPerProduk[$namaProduk]['total_penjualan'] += $totalPenjualanProduk;
            $profitPerProduk[$namaProduk]['profit'] += $profit;

            // Total keseluruhan
            $totalPenjualanAll += $totalPenjualanProduk;
            $totalProfitAll += $profit;
        }
    }

    $totalQtyTerjual = collect($profitPerProduk)->sum('qty');
    
    /* ===============================
       4. RETURN KE VIEW
    ================================*/
    return view('laporan.summary', compact(
        'jumlahTransaksi',
        'profitPerProduk',
        'totalPenjualanAll',
        'totalProfitAll',
        'totalQtyTerjual',
        'startDate',
        'endDate'
    ));
}


}