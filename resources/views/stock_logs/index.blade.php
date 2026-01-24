@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Riwayat Stock</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('stock_logs.create') }}" class="btn btn-primary">Tambah Stock</a>

        <!-- Filter -->
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            <button type="submit" class="btn btn-info">Filter</button>
        </form>
    </div>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Produk</th>
                <th>Type</th>
                <th>Qty (input)</th>
                <th>Satuan</th>
                <th>Qty dasar</th>
                <th>Harga Beli</th>
                <th>Catatan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->produk->nama_produk }}</td>
                <td>{{ $log->type=='in'?'Masuk':'Keluar' }}</td>
                <td>{{ $log->qty }}</td>
                <td>{{ $log->satuan?->nama_satuan ?? '-' }}</td>
                <td>{{ $log->qty_dasar }} {{ $log->produk->satuan_dasar }}</td>
                <td>{{ $log->harga_beli ? number_format($log->harga_beli,0,',','.') : '-' }}</td>
                <td>{{ $log->note ?? '-' }}</td>
                <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    <a href="{{ route('stock_logs.edit',$log->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                    <form action="{{ route('stock_logs.destroy',$log->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus stock log?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
