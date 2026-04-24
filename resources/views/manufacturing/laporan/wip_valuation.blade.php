@extends('layouts.app')

@section('title', 'Laporan WIP Valuation')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan WIP Valuation</h1>
</div>

<!-- Report Header -->
<div class="text-center mb-4">
    <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
    <h5>Laporan WIP Valuation (Barang Dalam Proses)</h5>
    <p class="text-muted">Per {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
</div>

<div class="row mb-4 justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Nilai WIP</h6>
                <h2 class="fw-bold text-primary">Rp {{ number_format($totalWipValue, 2, ',', '.') }}</h2>
                <small class="text-muted">{{ $wipProductions->count() }} Perintah Produksi Sedang Berjalan</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th>No Produksi</th>
                        <th>Rencana Barang Jadi</th>
                        <th class="text-center">Kuantitas</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Nilai Biaya Terakumulasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wipProductions as $p)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $p->no_produksi }}</td>
                        <td>{{ $p->bom->barangJadi->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ number_format($p->kuantitas_produksi, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $p->status == 'process' ? 'info' : 'secondary' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="text-end">Rp {{ number_format($p->total_value, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada perintah produksi yang berstatus WIP.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 card">
    <div class="card-body bg-light">
        <h6>Informasi</h6>
        <p class="mb-0 small text-muted">
            Laporan WIP (Work In Process) Valuation menampilkan perintah produksi yang telah dibuat (draft) atau sedang diproses (process) namun belum dinyatakan selesai (completed). Nilai yang tampil adalah total biaya material yang telah dialokasikan ke perintah produksi tersebut.
        </p>
    </div>
</div>
@endsection
