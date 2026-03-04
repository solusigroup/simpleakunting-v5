@extends('layouts.app')

@section('title', 'Laporan Mutasi Persediaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Mutasi Persediaan</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.mutasi_persediaan') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari_tanggal" name="dari_tanggal" 
                           value="{{ request('dari_tanggal', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai_tanggal" name="sampai_tanggal"
                           value="{{ request('sampai_tanggal', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="id_barang" class="form-label">Barang</label>
                    <select name="id_barang" id="id_barang" class="form-select">
                        <option value="">Semua Barang</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->id_barang }}" {{ request('id_barang') == $b->id_barang ? 'selected' : '' }}>
                                {{ $b->kode_barang }} - {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.mutasi_persediaan') }}" class="btn btn-secondary">Reset</a>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <div class="text-center">
                <h4 class="mb-0 fw-bold">{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
                <h5 class="mb-0">Laporan Mutasi Persediaan</h5>
                <p class="text-muted mb-0">Periode: {{ \Carbon\Carbon::parse(request('dari_tanggal', now()->startOfMonth()))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('sampai_tanggal', now()))->format('d M Y') }}</p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasi as $m)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $m->kode_barang ?? '-' }}</td>
                                <td>{{ $m->nama_barang ?? '-' }}</td>
                                <td>{{ $m->keterangan }}</td>
                                <td class="text-end text-success fw-bold">
                                    {{ $m->tipe_transaksi == 'IN' ? number_format($m->kuantitas, 2, ',', '.') : '-' }}
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    {{ $m->tipe_transaksi == 'OUT' ? number_format($m->kuantitas, 2, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data mutasi pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($mutasi) > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end text-success">{{ number_format($mutasi->where('tipe_transaksi', 'IN')->sum('kuantitas'), 2, ',', '.') }}</td>
                            <td class="text-end text-danger">{{ number_format($mutasi->where('tipe_transaksi', 'OUT')->sum('kuantitas'), 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
