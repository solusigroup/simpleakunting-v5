@extends('layouts.app')

@section('title', 'Perhitungan SHU')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Perhitungan dan Pembagian SHU</h1>
    <form class="d-flex gap-2" method="GET">
        <select name="tahun" class="form-select form-select-sm">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-sm btn-primary">Hitung</button>
    </form>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📈 Pendapatan Koperasi Tahun {{ $tahun }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Pendapatan Bunga Pinjaman</td>
                        <td class="text-end">Rp {{ number_format($pendapatanBunga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pendapatan Admin</td>
                        <td class="text-end">Rp {{ number_format($pendapatanAdmin, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pendapatan Lain-lain</td>
                        <td class="text-end">Rp {{ number_format($pendapatanLain, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-primary fw-bold">
                        <td>Total Pendapatan</td>
                        <td class="text-end">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">📉 Beban Koperasi Tahun {{ $tahun }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Total Beban Operasional</td>
                        <td class="text-end">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-danger fw-bold">
                        <td>Total Beban</td>
                        <td class="text-end">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header {{ $shuBersih >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
        <h5 class="mb-0">💰 Sisa Hasil Usaha (SHU) Tahun {{ $tahun }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td>SHU Bersih</td>
                        <td class="text-end fw-bold">Rp {{ number_format($shuBersih, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Bagian Anggota ({{ $persenAnggota }}%)</td>
                        <td class="text-end">Rp {{ number_format($shuAnggota, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Bagian Koperasi ({{ 100 - $persenAnggota }}%)</td>
                        <td class="text-end">Rp {{ number_format($shuBersih - $shuAnggota, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">👥 Pembagian SHU per Anggota</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>No. Anggota</th>
                        <th>Nama Anggota</th>
                        <th class="text-end">Simpanan</th>
                        <th class="text-end">Jasa Pinjaman</th>
                        <th class="text-end">Kontribusi</th>
                        <th class="text-end">SHU Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggota as $a)
                    <tr>
                        <td>{{ $a->no_anggota }}</td>
                        <td>{{ $a->nama_lengkap }}</td>
                        <td class="text-end">Rp {{ number_format($a->simpanan, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($a->jasa_pinjaman, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($a->kontribusi, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($a->shu, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Tidak ada anggota dengan kontribusi.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="4">Total</th>
                        <th class="text-end">Rp {{ number_format($totalKontribusi, 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($shuAnggota, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
