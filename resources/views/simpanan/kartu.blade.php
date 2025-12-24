@extends('layouts.app')

@section('title', 'Kartu Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kartu Simpanan Anggota</h1>
    <div class="btn-toolbar">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <span data-feather="printer"></span> Cetak
        </button>
        <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">
            <span data-feather="arrow-left"></span> Kembali
        </a>
    </div>
</div>

<!-- Header Anggota -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <div class="row">
            <div class="col">
                <h4 class="mb-0">{{ $anggota->nama_lengkap }}</h4>
                <small>{{ $anggota->no_anggota }} | NIK: {{ $anggota->nik }}</small>
            </div>
            <div class="col-auto text-end">
                @if($anggota->status == 'aktif')
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($anggota->status) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    @php $totalSimpanan = 0; @endphp
    @foreach($summary as $jenisId => $data)
        @php $totalSimpanan += $data['saldo']; @endphp
        <div class="col-md-3">
            <div class="card h-100 {{ $data['saldo'] > 0 ? 'border-success' : 'border-secondary' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ $data['jenis']->nama_simpanan }}</h6>
                    <h4 class="{{ $data['saldo'] > 0 ? 'text-success' : '' }}">
                        Rp {{ number_format($data['saldo'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-md-3">
        <div class="card h-100 bg-primary text-white">
            <div class="card-body text-center">
                <h6>TOTAL SIMPANAN</h6>
                <h4>Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Mutasi per Jenis -->
@foreach($summary as $jenisId => $data)
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <strong>{{ $data['jenis']->nama_simpanan }}</strong>
        <span class="badge bg-primary">Saldo: Rp {{ number_format($data['saldo'], 0, ',', '.') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Transaksi</th>
                        <th class="text-end">Setor</th>
                        <th class="text-end">Tarik</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($data['mutations']) > 0)
                        @foreach($data['mutations'] as $m)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($m['tanggal'])->format('d/m/Y') }}</td>
                            <td>
                                @if($m['jenis'] == 'setor')
                                    <span class="badge bg-success">Setor</span>
                                @else
                                    <span class="badge bg-danger">Tarik</span>
                                @endif
                            </td>
                            <td class="text-end text-success">
                                {{ $m['jenis'] == 'setor' ? 'Rp ' . number_format($m['jumlah'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger">
                                {{ $m['jenis'] == 'tarik' ? 'Rp ' . number_format($m['jumlah'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end"><strong>Rp {{ number_format($m['saldo'], 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@if(count($summary) == 0)
<div class="alert alert-info text-center">
    <span data-feather="info"></span>
    Anggota ini belum memiliki transaksi simpanan.
</div>
@endif

<div class="text-muted text-center mt-4 small">
    Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush

<style>
@media print {
    .navbar, .sidebar, .btn-toolbar, .no-print {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>
