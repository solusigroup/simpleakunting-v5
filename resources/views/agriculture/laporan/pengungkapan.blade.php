@extends('layouts.app')

@section('title', 'Pengungkapan Aset Biologis')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pengungkapan Aset Biologis</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('agriculture.laporan.pengungkapan') }}" method="GET" class="row g-3 align-items-end">
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
    <h5>Catatan atas Laporan Keuangan — Aset Biologis (PSAK 69)</h5>
    <p class="text-muted">Periode Tahun {{ $tahun }}</p>
</div>

<!-- 1. Kebijakan Akuntansi -->
<div class="card mb-4">
    <div class="card-header fw-bold">1. Kebijakan Akuntansi</div>
    <div class="card-body">
        <p>Perusahaan menerapkan <strong>PSAK 69: Agrikultur</strong> dalam pengakuan dan pengukuran aset biologis. 
           Aset biologis diukur pada <strong>nilai wajar dikurangi estimasi biaya penjualan</strong> (fair value less costs to sell), 
           kecuali jika nilai wajar tidak dapat diukur secara andal, maka digunakan <strong>biaya perolehan dikurangi akumulasi penyusutan</strong>.</p>
        <p>Keuntungan atau kerugian yang timbul dari perubahan nilai wajar aset biologis diakui dalam <strong>laporan laba rugi</strong> 
           pada periode terjadinya perubahan.</p>
    </div>
</div>

<!-- 2. Klasifikasi per Jenis -->
<div class="card mb-4">
    <div class="card-header fw-bold">2. Klasifikasi Aset Biologis per Jenis</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Nilai Perolehan</th>
                        <th class="text-end">Nilai Wajar</th>
                        <th class="text-end">Estimasi Biaya Jual</th>
                        <th class="text-end">Unrealized Gain/(Loss)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byJenis as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->jenis }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_perolehan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_nilai_wajar, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_estimasi_biaya_jual, 0, ',', '.') }}</td>
                        <td class="text-end {{ $item->unrealized_gain_loss >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $item->unrealized_gain_loss >= 0 ? '' : '(' }}Rp {{ number_format(abs($item->unrealized_gain_loss), 0, ',', '.') }}{{ $item->unrealized_gain_loss >= 0 ? '' : ')' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
                @if($byJenis->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center">{{ $byJenis->sum('jumlah') }}</td>
                        <td class="text-end">Rp {{ number_format($byJenis->sum('total_perolehan'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byJenis->sum('total_nilai_wajar'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byJenis->sum('total_estimasi_biaya_jual'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($byJenis->sum('unrealized_gain_loss'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- 3. Status Aset -->
<div class="card mb-4">
    <div class="card-header fw-bold">3. Status Aset Biologis</div>
    <div class="card-body">
        <div class="row">
            @foreach($byStatus as $item)
            <div class="col-md-3 mb-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">{{ $item->status }}</h6>
                        <h3 class="fw-bold">{{ $item->jumlah }}</h3>
                        <small>Rp {{ number_format($item->total_nilai_wajar, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 4. Distribusi per Cabang -->
<div class="card mb-4">
    <div class="card-header fw-bold">4. Distribusi per Cabang/Lokasi</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Cabang</th>
                        <th class="text-center">Jumlah Aset</th>
                        <th class="text-end">Total Nilai Wajar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byCabang as $item)
                    <tr>
                        <td>{{ $item->cabang }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($item->total_nilai_wajar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 5. Aktivitas Revaluasi -->
<div class="card mb-4">
    <div class="card-header fw-bold">5. Aktivitas Revaluasi Tahun {{ $tahun }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card border-secondary text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Jumlah Revaluasi</h6>
                        <h3 class="fw-bold">{{ $totalRevaluasi }}×</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Keuntungan</h6>
                        <h4 class="fw-bold text-success">Rp {{ number_format($totalGain, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Kerugian</h6>
                        <h4 class="fw-bold text-danger">(Rp {{ number_format(abs($totalLoss), 0, ',', '.') }})</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6. Daftar Lengkap Aset -->
<div class="card">
    <div class="card-header fw-bold">6. Daftar Lengkap Aset Biologis</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Tgl Perolehan</th>
                        <th>Umur</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th class="text-end">Perolehan</th>
                        <th class="text-end">Nilai Wajar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td>{{ \Carbon\Carbon::parse($asset->tanggal_perolehan)->format('d/m/Y') }}</td>
                        <td>{{ $asset->umur_bulan }} bln</td>
                        <td><span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'panen' ? 'info' : ($asset->status == 'dijual' ? 'warning' : 'danger')) }}">{{ ucfirst($asset->status) }}</span></td>
                        <td>{{ $asset->lokasi ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($asset->nilai_wajar, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($assets->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="7">Grand Total</td>
                        <td class="text-end">Rp {{ number_format($assets->sum('nilai_perolehan'), 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($assets->sum('nilai_wajar'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
