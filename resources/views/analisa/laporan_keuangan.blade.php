@extends('layouts.app')

@section('title', 'Analisa Laporan Keuangan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-bar-chart-line text-primary me-2"></i>Analisa Laporan Keuangan
            </h3>
            <p class="text-muted mb-0">Analisa Vertikal (Common-Size) & Analisa Horizontal (Trend / Growth) untuk Neraca & Laba Rugi</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Analisa
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('analisa.laporan_keuangan') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Periode Utama</label>
                    <input type="date" name="per_tanggal" class="form-control form-control-sm" value="{{ $perTanggal }}" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Periode Pembanding</label>
                    <input type="date" name="banding_tanggal" class="form-control form-control-sm" value="{{ $bandingTanggal }}" required>
                </div>
                @if(isset($cabang) && $cabang->count() > 1)
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Cabang</label>
                    <select name="id_cabang" class="form-select form-select-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($cabang as $c)
                            <option value="{{ $c->id }}" {{ request('id_cabang', session('active_cabang')) == $c->id ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if(isset($unitUsaha) && $unitUsaha->count() > 0)
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Unit Usaha</label>
                    <select name="id_unit_usaha" class="form-select form-select-sm">
                        <option value="">Semua Unit</option>
                        @foreach($unitUsaha as $u)
                            <option value="{{ $u->id }}" {{ request('id_unit_usaha', session('active_unit')) == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2 col-sm-12 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('analisa.laporan_keuangan') }}" class="btn btn-light btn-sm text-secondary border">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Executive Growth Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Pertumbuhan Pendapatan</span>
                    <span class="badge {{ $growth['pendapatan']['persen'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                        {{ ($growth['pendapatan']['persen'] >= 0 ? '+' : '') . number_format($growth['pendapatan']['persen'], 1, ',', '.') }}%
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($growth['pendapatan']['utama'], 0, ',', '.') }}</h5>
                <span class="text-muted small">Vs Rp {{ number_format($growth['pendapatan']['banding'], 0, ',', '.') }} (Lalu)</span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Pertumbuhan Laba Bersih</span>
                    <span class="badge {{ $growth['laba_bersih']['persen'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                        {{ ($growth['laba_bersih']['persen'] >= 0 ? '+' : '') . number_format($growth['laba_bersih']['persen'], 1, ',', '.') }}%
                    </span>
                </div>
                <h5 class="fw-bold mb-1 {{ $growth['laba_bersih']['utama'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($growth['laba_bersih']['utama'], 0, ',', '.') }}
                </h5>
                <span class="text-muted small">Vs Rp {{ number_format($growth['laba_bersih']['banding'], 0, ',', '.') }} (Lalu)</span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Pertumbuhan Total Aset</span>
                    <span class="badge {{ $growth['total_aset']['persen'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                        {{ ($growth['total_aset']['persen'] >= 0 ? '+' : '') . number_format($growth['total_aset']['persen'], 1, ',', '.') }}%
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($growth['total_aset']['utama'], 0, ',', '.') }}</h5>
                <span class="text-muted small">Vs Rp {{ number_format($growth['total_aset']['banding'], 0, ',', '.') }} (Lalu)</span>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold">Pertumbuhan Ekuitas Bersih</span>
                    <span class="badge {{ $growth['total_ekuitas']['persen'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                        {{ ($growth['total_ekuitas']['persen'] >= 0 ? '+' : '') . number_format($growth['total_ekuitas']['persen'], 1, ',', '.') }}%
                    </span>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($growth['total_ekuitas']['utama'], 0, ',', '.') }}</h5>
                <span class="text-muted small">Vs Rp {{ number_format($growth['total_ekuitas']['banding'], 0, ',', '.') }} (Lalu)</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-3" id="analysisTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold shadow-sm px-4" id="neraca-tab" data-bs-toggle="tab" data-bs-target="#tabNeraca" type="button" role="tab">
                <i class="bi bi-wallet2 me-1"></i> Analisa Neraca (Posisi Keuangan)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold shadow-sm px-4" id="labarugi-tab" data-bs-toggle="tab" data-bs-target="#tabLabaRugi" type="button" role="tab">
                <i class="bi bi-graph-up-arrow me-1"></i> Analisa Laba Rugi (Kinerja Pendapatan)
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="analysisTabsContent">
        <!-- TAB 1: NERACA -->
        <div class="tab-pane fade show active" id="tabNeraca" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tabel Analisa Neraca: Vertikal (Common-Size) & Horizontal (Trend)</h6>
                        <small class="text-muted">Vertikal dihitung dari % Total Aset (Rp {{ number_format($totalAsetUtama, 0, ',', '.') }})</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Kode Akun</th>
                                <th style="width: 25%;">Nama Akun & Kategori</th>
                                <th class="text-end" style="width: 15%;">Periode Utama (Rp)</th>
                                <th class="text-center" style="width: 10%;">Common-Size (%)</th>
                                <th class="text-end" style="width: 15%;">Periode Pembanding (Rp)</th>
                                <th class="text-end" style="width: 15%;">Perubahan (Nominal)</th>
                                <th class="text-center" style="width: 10%;">Pertumbuhan (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $tipeGrup = '';
                            @endphp
                            @forelse($analisaNeraca as $row)
                                @if($tipeGrup !== $row->tipe_akun)
                                    @php $tipeGrup = $row->tipe_akun; @endphp
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="7" class="text-uppercase py-2 ps-3">{{ $tipeGrup }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="font-monospace text-muted">{{ $row->kode_akun }}</td>
                                    <td class="fw-medium text-dark">{{ $row->nama_akun }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($row->saldo_utama, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ number_format($row->common_size_utama, 1, ',', '.') }}%</span>
                                    </td>
                                    <td class="text-end text-muted">Rp {{ number_format($row->saldo_banding, 0, ',', '.') }}</td>
                                    <td class="text-end {{ $row->nominal_perubahan >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ ($row->nominal_perubahan >= 0 ? '+' : '') . number_format($row->nominal_perubahan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->persen_perubahan >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                                            {{ ($row->persen_perubahan >= 0 ? '+' : '') . number_format($row->persen_perubahan, 1, ',', '.') }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data transaksi akun neraca untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: LABA RUGI -->
        <div class="tab-pane fade" id="tabLabaRugi" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tabel Analisa Laba Rugi: Vertikal (Common-Size) & Horizontal (Trend)</h6>
                        <small class="text-muted">Vertikal dihitung dari % Total Pendapatan (Rp {{ number_format($totalPendapatanUtama, 0, ',', '.') }})</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Kode Akun</th>
                                <th style="width: 25%;">Nama Akun & Kategori</th>
                                <th class="text-end" style="width: 15%;">Periode Utama (Rp)</th>
                                <th class="text-center" style="width: 10%;">Common-Size (%)</th>
                                <th class="text-end" style="width: 15%;">Periode Pembanding (Rp)</th>
                                <th class="text-end" style="width: 15%;">Perubahan (Nominal)</th>
                                <th class="text-center" style="width: 10%;">Pertumbuhan (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $tipeGrupLR = '';
                            @endphp
                            @forelse($analisaLabaRugi as $row)
                                @if($tipeGrupLR !== $row->tipe_akun)
                                    @php $tipeGrupLR = $row->tipe_akun; @endphp
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="7" class="text-uppercase py-2 ps-3">{{ $tipeGrupLR }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="font-monospace text-muted">{{ $row->kode_akun }}</td>
                                    <td class="fw-medium text-dark">{{ $row->nama_akun }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($row->saldo_utama, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ number_format($row->common_size_utama, 1, ',', '.') }}%</span>
                                    </td>
                                    <td class="text-end text-muted">Rp {{ number_format($row->saldo_banding, 0, ',', '.') }}</td>
                                    <td class="text-end {{ $row->nominal_perubahan >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ ($row->nominal_perubahan >= 0 ? '+' : '') . number_format($row->nominal_perubahan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->persen_perubahan >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                                            {{ ($row->persen_perubahan >= 0 ? '+' : '') . number_format($row->persen_perubahan, 1, ',', '.') }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data pendapatan/beban untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
