<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    /**
     * Buka Shift
     */
    public function open(Request $request)
    {
        $request->validate([
            'start_cash' => 'required|numeric|min:0'
        ]);

        // Cek apakah user punya shift aktif
        $activeSettlement = Settlement::where('user_id', Auth::id())
                                      ->where('status', 'open')
                                      ->first();
        if ($activeSettlement) {
            return response()->json([
                'message' => 'Anda masih memiliki shift yang aktif!'
            ], 400);
        }

        // Buat shift baru
        $settlement = Settlement::create([
            'user_id' => Auth::id(),
            'start_cash' => (int) $request->start_cash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        // Simpan ID shift aktif di session
        session(['active_settlement_id' => $settlement->id]);

        return response()->json([
            'message' => 'Shift berhasil dibuka!',
            'settlement' => $settlement
        ]);
    }

    /**
     * Tutup Shift
     */
    public function close(Request $request)
    {
        $request->validate([
            'end_cash' => 'required|numeric|min:0'
        ]);

        // Ambil shift aktif dari session
        $settlement = Settlement::where('user_id', Auth::id())
        ->where('status', 'open')
        ->latest()
        ->first();

        if (!$settlement || $settlement->status == 'closed') {
            return response()->json([
                'message' => 'Tidak ada shift aktif yang bisa ditutup!'
            ], 400);
        }

        // Ambil semua transaksi yang terkait dengan shift ini
        $transactions = Transaksi::where('settlement_id', $settlement->id)->get();

        // Hitung total
        $total_sales       = (int) $transactions->sum('total');
        $total_discount    = (int) $transactions->sum('diskon');
        $total_cash_sales  = (int) $transactions->where('metode_pembayaran', 'cash')->sum('total');
        $total_qris_sales  = (int) $transactions->where('metode_pembayaran', 'qris')->sum('total');
        $total_debit_sales = (int) $transactions->where('metode_pembayaran', 'debit')->sum('total');


        // Update shift
        $settlement->update([
            'end_cash' => (int) $request->end_cash,
            'total_sales' => $total_sales,
            'total_discount' => $total_discount,
            'total_cash_sales' => $total_cash_sales,
            'total_qris_sales' => $total_qris_sales,
            'total_debit_sales' => $total_debit_sales,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        // Hapus session shift aktif
        session()->forget('active_settlement_id');

        return response()->json([
            'message' => 'Shift berhasil ditutup!',
            'settlement' => $settlement
        ]);
    }

    public function totalCashShift()
    {
        $settlement = Settlement::where('user_id', Auth::id())
                                ->where('status','open')
                                ->latest()
                                ->first();

        if (!$settlement) {
            return response()->json(['total_cash' => 0]);
        }

        $totalCash = Transaksi::where('settlement_id', $settlement->id)
                        ->where('metode_pembayaran','cash')
                        ->sum('total');
        $expectedCash = (int) $settlement->start_cash + ($totalCash);
        return response()->json([
            'total_cash' => $expectedCash
        ]);
    }




    /**
     * Cek status shift saat load halaman
     */
    public function status()
    {
        $settlement = Settlement::where('user_id', Auth::id())
                                ->where('status', 'open')
                                ->latest()
                                ->first();
        return response()->json($settlement);
    }

    public function menu()
    {
        // Ambil settlement aktif
        $settlement = Settlement::where('user_id', Auth::id())
                                ->where('status', 'open')
                                ->latest()
                                ->first();

        if (!$settlement) {
            return view('settlement.menu', [
                'settlement' => null,
                'products' => [],
            ]);
        }

        // Ambil transaksi beserta detail & produk
        $transactions = Transaksi::with('details.produk', 'details.satuan')
                        ->where('settlement_id', $settlement->id)
                        ->get();


        // Hitung ringkasan
        $total_sales = $transactions->sum('total');
        $total_discount = $transactions->sum('diskon');
        $total_cash_sales = $transactions->where('metode_pembayaran', 'cash')->sum('total');
        $total_qris_sales = $transactions->where('metode_pembayaran', 'qris')->sum('total');
        $total_debit_sales = $transactions->where('metode_pembayaran', 'debit')->sum('total');

        // Hitung produk per satuan
        $products = [];

        foreach ($transactions as $trx) {
            foreach ($trx->details as $item) {

                $produkNama = $item->produk ? $item->produk->nama_produk : 'Produk tidak ditemukan';
                $satuanNama = $item->satuan ? $item->satuan->nama_satuan : '-';

                $key = $item->produk_id . '-' . $item->satuan_id; // unique per produk+satuan

                if (!isset($products[$key])) {
                    $products[$key] = [
                        'nama' => $produkNama,
                        'satuan' => $satuanNama,
                        'harga' => $item->harga,
                        'qty' => 0,
                        'total' => 0,
                    ];
                }

                $products[$key]['qty'] += $item->qty;
                $products[$key]['total'] += $item->qty * $item->harga;
            }
        }

        return view('settlement.menu', [
            'settlement'        => $settlement,
            'products'          => $products,
            'total_sales'       => $total_sales,
            'total_discount'    => $total_discount,
            'total_cash_sales'  => $total_cash_sales,
            'total_qris_sales'  => $total_qris_sales,
            'total_debit_sales' => $total_debit_sales,
        ]);
    }

    public function printShift(Request $request)
    {
        $settlement = Settlement::where('user_id', Auth::id())
                                ->with('user')   // <-- WAJIB DITAMBAHKAN
                                ->latest()
                                ->first();

        if (!$settlement) {
            return response()->json(['error' => 'Shift tidak ditemukan'], 404);
        }

        return response()->json($settlement);
    }

    // Route untuk menampilkan halaman history
    public function history(Request $request)
    {
        $query = Settlement::where('user_id', Auth::id());

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('opened_at', [$start, $end]);
        }

        $settlements = $query->orderByDesc('opened_at')->get();

        return view('settlement.history', compact('settlements'));
    }


    // Route untuk detail settlement (AJAX)
    public function detail($id)
    {
        $settlement = Settlement::with(['transaksis.details.produk', 'transaksis.details.satuan'])
                        ->findOrFail($id);

        $products = [];
        foreach($settlement->transaksis as $trx){
            foreach($trx->details as $item){
                $key = $item->produk_id . '-' . $item->satuan_id;
                if(!isset($products[$key])){
                    $products[$key] = [
                        'nama' => $item->produk->nama_produk ?? 'Produk tidak ditemukan',
                        'satuan' => $item->satuan->nama_satuan ?? '-',
                        'qty' => 0,
                        'total' => 0,
                    ];
                }
                $products[$key]['qty'] += $item->qty;
                $products[$key]['total'] += $item->qty * $item->harga;
            }
        }

        return response()->json([
            'products' => array_values($products),
            'total_sales' => $settlement->total_sales ?? 0,
            'total_cash_sales' => $settlement->total_cash_sales ?? 0,
            'total_debit_sales' => $settlement->total_debit_sales ?? 0,
            'total_qris_sales' => $settlement->total_qris_sales ?? 0,
        ]);
    }


}
