@extends('layouts.app')

@section('title', 'Kartu Anggota - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kartu Anggota</h1>
    <div class="btn-toolbar">
        <button onclick="window.print()" class="btn btn-outline-primary me-2">
            <span data-feather="printer"></span> Cetak
        </button>
        <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
            <span data-feather="arrow-left"></span> Kembali
        </a>
    </div>
</div>

<!-- Member Card Header -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($anggota->foto)
                    <img src="{{ asset('storage/anggota/' . $anggota->foto) }}" alt="Foto" 
                         class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid white;">
                @else
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <span data-feather="user" style="width: 40px; height: 40px; color: #333;"></span>
                    </div>
                @endif
            </div>
            <div class="col">
                <h3 class="mb-0">{{ $anggota->nama_lengkap }}</h3>
                <p class="mb-0 opacity-75">{{ $anggota->no_anggota }} | NIK: {{ $anggota->nik }}</p>
            </div>
            <div class="col-auto text-end">
                @if($anggota->status == 'aktif')
                    <span class="badge bg-success fs-5">AKTIF</span>
                @elseif($anggota->status == 'non_aktif')
                    <span class="badge bg-warning fs-5">NON-AKTIF</span>
                @else
                    <span class="badge bg-danger fs-5">KELUAR</span>
                @endif
                <p class="mb-0 mt-2 small opacity-75">Anggota sejak: {{ $anggota->tanggal_daftar->format('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <!-- Simpanan Pokok -->
    <div class="col-md-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h6 class="card-title">Simpanan Pokok</h6>
                <h3 class="mb-0">Rp {{ number_format($simpananSummary['pokok'] ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <!-- Simpanan Wajib -->
    <div class="col-md-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title">Simpanan Wajib</h6>
                <h3 class="mb-0">Rp {{ number_format($simpananSummary['wajib'] ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <!-- Simpanan Sukarela -->
    <div class="col-md-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h6 class="card-title">Simpanan Sukarela</h6>
                <h3 class="mb-0">Rp {{ number_format($simpananSummary['sukarela'] ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <!-- Total Simpanan -->
    <div class="col-md-3">
        <div class="card text-dark bg-light h-100">
            <div class="card-body">
                <h6 class="card-title">Total Simpanan</h6>
                @php
                    $totalSimpanan = ($simpananSummary['pokok'] ?? 0) + ($simpananSummary['wajib'] ?? 0) + ($simpananSummary['sukarela'] ?? 0);
                @endphp
                <h3 class="mb-0 text-success">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Pinjaman Aktif -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <strong><span data-feather="credit-card"></span> Pinjaman Aktif</strong>
    </div>
    <div class="card-body">
        @if($pinjamanAktif->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Jenis</th>
                        <th>Tgl Pencairan</th>
                        <th class="text-end">Pokok Pinjaman</th>
                        <th class="text-end">Sisa Pokok</th>
                        <th>Tenor</th>
                        <th>Kolektibilitas</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSisaPokok = 0; @endphp
                    @foreach($pinjamanAktif as $p)
                    @php $totalSisaPokok += $p->sisa_pokok; @endphp
                    <tr>
                        <td><a href="{{ route('pinjaman.show', $p->id_pinjaman) }}">{{ $p->no_pinjaman }}</a></td>
                        <td>{{ $p->jenisPinjaman->nama_pinjaman }}</td>
                        <td>{{ $p->tanggal_pencairan?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}</strong></td>
                        <td>{{ $p->tenor }} bulan</td>
                        <td>
                            @switch($p->kolektibilitas)
                                @case('1')
                                    <span class="badge bg-success">Lancar</span>
                                    @break
                                @case('2')
                                    <span class="badge bg-info">DPK</span>
                                    @break
                                @case('3')
                                    <span class="badge bg-warning">Kurang Lancar</span>
                                    @break
                                @case('4')
                                    <span class="badge bg-orange">Diragukan</span>
                                    @break
                                @case('5')
                                    <span class="badge bg-danger">Macet</span>
                                    @break
                            @endswitch
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="4" class="text-end">Total Sisa Pokok:</th>
                        <th class="text-end text-danger">Rp {{ number_format($totalSisaPokok, 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-4 text-muted">
            <span data-feather="check-circle" style="width: 48px; height: 48px;"></span>
            <p class="mt-2 mb-0">Tidak ada pinjaman aktif</p>
        </div>
        @endif
    </div>
</div>

<!-- Summary -->
<div class="row">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body">
                <h5>Ringkasan Keuangan</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td>Total Simpanan</td>
                        <td class="text-end text-success"><strong>Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total Sisa Pinjaman</td>
                        <td class="text-end text-danger"><strong>Rp {{ number_format($totalSisaPokok ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Saldo Bersih</strong></td>
                        @php $saldoBersih = $totalSimpanan - ($totalSisaPokok ?? 0); @endphp
                        <td class="text-end {{ $saldoBersih >= 0 ? 'text-success' : 'text-danger' }}">
                            <strong>Rp {{ number_format($saldoBersih, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body">
                <h5>Informasi Kontak</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="100">Telepon</td>
                        <td>: {{ $anggota->telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>: {{ $anggota->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $anggota->alamat }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

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
