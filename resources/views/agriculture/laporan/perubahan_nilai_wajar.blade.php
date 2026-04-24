@extends('layouts.app')

@section('title', 'Perubahan Nilai Wajar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Perubahan Nilai Wajar</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('agriculture.laporan.perubahan_nilai_wajar') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Header -->
<div class="text-center mb-4">
    <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
    <h5>Perubahan Nilai Wajar Aset Biologis (PSAK 69)</h5>
    <p class="text-muted">Periode Tahun {{ $tahun }}</p>
</div>

<!-- Ringkasan -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted">Keuntungan Revaluasi</h6>
                <h4 class="text-success fw-bold">Rp {{ number_format($totalGain, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted">Kerugian Revaluasi</h6>
                <h4 class="text-danger fw-bold">(Rp {{ number_format(abs($totalLoss), 0, ',', '.') }})</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-{{ $netChange >= 0 ? 'primary' : 'warning' }}">
            <div class="card-body text-center">
                <h6 class="text-muted">Perubahan Bersih</h6>
                <h4 class="fw-bold {{ $netChange >= 0 ? 'text-primary' : 'text-warning' }}">
                    {{ $netChange >= 0 ? '' : '(' }}Rp {{ number_format(abs($netChange), 0, ',', '.') }}{{ $netChange >= 0 ? '' : ')' }}
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Detail Riwayat Revaluasi -->
<div class="card">
    <div class="card-header fw-bold">Riwayat Revaluasi Tahun {{ $tahun }}</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Aset</th>
                        <th>Kode</th>
                        <th class="text-end">Nilai Sebelum</th>
                        <th class="text-end">Nilai Wajar Baru</th>
                        <th class="text-end">Selisih</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($log->tanggal_revaluasi)->format('d/m/Y') }}</td>
                        <td>{{ $log->asetBiologis->nama_aset ?? '-' }}</td>
                        <td>{{ $log->asetBiologis->kode_aset ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($log->nilai_buku_sebelum, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($log->nilai_wajar_baru, 0, ',', '.') }}</td>
                        <td class="text-end {{ $log->selisih_nilai >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $log->selisih_nilai >= 0 ? '+' : '' }}Rp {{ number_format($log->selisih_nilai, 0, ',', '.') }}
                        </td>
                        <td>{{ $log->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Belum ada revaluasi pada tahun {{ $tahun }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
