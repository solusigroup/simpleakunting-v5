@extends('layouts.app')

@section('title', 'Laporan Penggunaan Material')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Penggunaan Material</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('manufacturing.laporan.penggunaan_material') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Header -->
<div class="text-center mb-4">
    <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
    <h5>Laporan Penggunaan Material</h5>
    <p class="text-muted">Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Kode Barang</th>
                        <th>Nama Material</th>
                        <th class="text-center">Total Kuantitas Digunakan</th>
                        <th class="text-end">Total Nilai Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSemuaBiaya = 0; @endphp
                    @forelse($materialUsage as $usage)
                    @php $totalSemuaBiaya += $usage->total_cost; @endphp
                    <tr>
                        <td class="text-center">{{ $usage->material->kode_barang ?? '-' }}</td>
                        <td>{{ $usage->material->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ number_format($usage->total_qty, 4) }} {{ $usage->material->satuan ?? '' }}</td>
                        <td class="text-end">Rp {{ number_format($usage->total_cost, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3">Tidak ada penggunaan material pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($materialUsage->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL NILAI MATERIAL KELUAR</td>
                        <td class="text-end">Rp {{ number_format($totalSemuaBiaya, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
