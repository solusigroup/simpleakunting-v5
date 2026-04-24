@extends('layouts.app')

@section('title', 'Rekonsiliasi Aset Biologis')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Rekonsiliasi Aset Biologis</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('agriculture.laporan.rekonsiliasi') }}" method="GET" class="row g-3 align-items-end">
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
    <h5>Rekonsiliasi Aset Biologis (PSAK 69)</h5>
    <p class="text-muted">Periode Tahun {{ $tahun }}</p>
</div>

<!-- Ringkasan per Jenis -->
<div class="card mb-4">
    <div class="card-header fw-bold">Ringkasan per Jenis Aset</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Aset</th>
                        <th class="text-center">Jml</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Penambahan</th>
                        <th class="text-end">Pengurangan</th>
                        <th class="text-end">Keuntungan Revaluasi</th>
                        <th class="text-end">Kerugian Revaluasi</th>
                        <th class="text-end">Saldo Akhir (Nilai Wajar)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ringkasan as $r)
                    <tr>
                        <td class="fw-bold">{{ $r->jenis }}</td>
                        <td class="text-center">{{ $r->jumlah_aset }}</td>
                        <td class="text-end">Rp {{ number_format($r->saldo_awal, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($r->penambahan, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">(Rp {{ number_format($r->pengurangan, 0, ',', '.') }})</td>
                        <td class="text-end text-success">Rp {{ number_format($r->keuntungan_revaluasi, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">(Rp {{ number_format($r->kerugian_revaluasi, 0, ',', '.') }})</td>
                        <td class="text-end fw-bold">Rp {{ number_format($r->saldo_akhir, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
                @if($ringkasan->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center">{{ $ringkasan->sum('jumlah_aset') }}</td>
                        <td class="text-end">Rp {{ number_format($ringkasan->sum('saldo_awal'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($ringkasan->sum('penambahan'), 0, ',', '.') }}</td>
                        <td class="text-end">(Rp {{ number_format($ringkasan->sum('pengurangan'), 0, ',', '.') }})</td>
                        <td class="text-end">Rp {{ number_format($ringkasan->sum('keuntungan_revaluasi'), 0, ',', '.') }}</td>
                        <td class="text-end">(Rp {{ number_format($ringkasan->sum('kerugian_revaluasi'), 0, ',', '.') }})</td>
                        <td class="text-end">Rp {{ number_format($ringkasan->sum('saldo_akhir'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Detail per Aset -->
<div class="card">
    <div class="card-header fw-bold">Detail per Aset</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tgl Perolehan</th>
                        <th class="text-end">Nilai Perolehan</th>
                        <th class="text-end">Nilai Wajar</th>
                        <th class="text-end">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    @php $selisih = $asset->nilai_wajar - $asset->nilai_perolehan; @endphp
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td><span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'panen' ? 'info' : ($asset->status == 'dijual' ? 'warning' : 'danger')) }}">{{ ucfirst($asset->status) }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($asset->tanggal_perolehan)->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_wajar, 0, ',', '.') }}</td>
                        <td class="text-end {{ $selisih >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $selisih >= 0 ? '' : '(' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}{{ $selisih >= 0 ? '' : ')' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
