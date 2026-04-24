@extends('layouts.app')

@section('title', 'Produksi dan Panen')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Produksi dan Panen</h1>
</div>

<!-- Report Header -->
<div class="text-center mb-4">
    <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
    <h5>Status Siklus Hidup Aset Biologis (PSAK 69)</h5>
    <p class="text-muted">Per {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted">🌱 Aktif / Tumbuh</h6>
                <h3 class="text-success fw-bold">{{ $summary['aktif']->count() }}</h3>
                <small>Rp {{ number_format($summary['aktif']->sum('nilai_wajar'), 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="text-muted">🌾 Panen / Produksi</h6>
                <h3 class="text-info fw-bold">{{ $summary['panen']->count() }}</h3>
                <small>Rp {{ number_format($summary['panen']->sum('nilai_wajar'), 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="text-muted">💰 Dijual</h6>
                <h3 class="text-warning fw-bold">{{ $summary['dijual']->count() }}</h3>
                <small>Rp {{ number_format($summary['dijual']->sum('nilai_wajar'), 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted">💀 Mati / Rusak</h6>
                <h3 class="text-danger fw-bold">{{ $summary['mati']->count() }}</h3>
                <small>Rp {{ number_format($summary['mati']->sum('nilai_wajar'), 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Detail per Status -->
@foreach($summary as $status => $group)
@if($group->count() > 0)
<div class="card mb-3">
    <div class="card-header fw-bold">
        @switch($status)
            @case('aktif') 🌱 Aset Aktif / Tumbuh @break
            @case('panen') 🌾 Aset dalam Panen / Produksi @break
            @case('dijual') 💰 Aset Dijual @break
            @case('mati') 💀 Aset Mati / Rusak @break
        @endswitch
        <span class="badge bg-secondary">{{ $group->count() }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Jenis</th>
                        <th>Umur (Bulan)</th>
                        <th>Lokasi</th>
                        <th>Cabang</th>
                        <th class="text-end">Nilai Perolehan</th>
                        <th class="text-end">Nilai Wajar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group as $asset)
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td>{{ $asset->umur_bulan }}</td>
                        <td>{{ $asset->lokasi ?? '-' }}</td>
                        <td>{{ $asset->cabang->nama_cabang ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_wajar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6">Subtotal</td>
                        <td class="text-end">Rp {{ number_format($group->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($group->sum('nilai_wajar'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif
@endforeach

@if($assets->count() == 0)
<div class="card">
    <div class="card-body text-center py-5">
        <p class="text-muted">Belum ada data aset biologis.</p>
    </div>
</div>
@endif
@endsection
