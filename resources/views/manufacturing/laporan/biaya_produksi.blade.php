@extends('layouts.app')

@section('title', 'Laporan Biaya Produksi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Biaya Produksi</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('manufacturing.laporan.biaya_produksi') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label for="id_cabang" class="form-label">Cabang</label>
                <select name="id_cabang" id="id_cabang" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}" {{ $idCabang == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Header -->
<div class="text-center mb-4">
    <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
    <h5>Laporan Biaya Produksi</h5>
    <p class="text-muted">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="table-light text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>No Produksi</th>
                        <th>Barang Jadi</th>
                        <th>Hasil (Qty)</th>
                        <th>Total Biaya (Material)</th>
                        <th>Biaya per Unit</th>
                        <th>Cabang</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSemua = 0; @endphp
                    @forelse($productions as $p)
                    @php 
                        $totalBiaya = $p->details->sum('total_biaya');
                        $totalSemua += $totalBiaya;
                        $costPerUnit = $p->kuantitas_produksi > 0 ? $totalBiaya / $p->kuantitas_produksi : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $p->no_produksi }}</td>
                        <td>{{ $p->bom->barangJadi->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ number_format($p->kuantitas_produksi, 2) }}</td>
                        <td class="text-end">Rp {{ number_format($totalBiaya, 2, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($costPerUnit, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $p->cabang->nama_cabang ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-3">Tidak ada data produksi untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($productions->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">GRAND TOTAL BIAYA</td>
                        <td class="text-end">Rp {{ number_format($totalSemua, 2, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
