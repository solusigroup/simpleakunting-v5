@extends('layouts.app')

@section('title', 'Rasio Keuangan Rekomendasi')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-percent text-success me-2"></i>Rasio Keuangan Rekomendasi
            </h3>
            <p class="text-muted mb-0">Indikator Kinerja Finansial Utama (Likuiditas, Profitabilitas, Solvabilitas, & Aktivitas)</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Ringkasan Rasio
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('analisa.rasio') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal (Periode Laba Rugi)</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal (Posisi Neraca)</label>
                    <input type="date" name="per_tanggal" class="form-control form-control-sm" value="{{ $perTanggal }}" required>
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
                    <button type="submit" class="btn btn-success btn-sm w-100 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Hitung Rasio
                    </button>
                    <a href="{{ route('analisa.rasio') }}" class="btn btn-light btn-sm text-secondary border">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 1. Rasio Likuiditas -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-primary-subtle text-primary p-2 me-2 rounded-circle">
                <i class="bi bi-droplet-half fs-6"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">1. Rasio Likuiditas (Liquidity Ratios)</h5>
                <small class="text-muted">Mengukur kesiapan aset lancar perusahaan dalam memenuhi kewajiban jangka pendek.</small>
            </div>
        </div>
        <div class="row g-3">
            @foreach($ratios['likuiditas'] as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-top border-{{ $item['status'] }} border-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold text-dark mb-0">{{ $item['nama'] }}</h6>
                        <span class="badge bg-{{ $item['status'] }}-subtle text-{{ $item['status'] }} rounded-pill px-2 py-1">
                            {{ $item['status_label'] }}
                        </span>
                    </div>
                    <div class="my-2">
                        <h3 class="fw-bold text-{{ $item['status'] }} mb-0">{{ $item['format'] }}</h3>
                        <small class="text-muted">Standar Acuan: <span class="fw-semibold text-dark">{{ $item['benchmark'] }}</span></small>
                    </div>
                    <p class="text-muted small mb-0 mt-2 border-top pt-2">{{ $item['deskripsi'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2. Rasio Profitabilitas -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <span class="badge bg-success-subtle text-success p-2 me-2 rounded-circle">
                <i class="bi bi-cash-stack fs-6"></i>
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">2. Rasio Profitabilitas & Imbal Hasil (Profitability Ratios)</h5>
                <small class="text-muted">Mengukur daya hasilkan laba bersih perusahaan terhadap penjualan, aset, dan modal sendiri.</small>
            </div>
        </div>
        <div class="row g-3">
            @foreach($ratios['profitabilitas'] as $item)
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-top border-{{ $item['status'] }} border-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold text-dark mb-0 small">{{ $item['nama'] }}</h6>
                        <span class="badge bg-{{ $item['status'] }}-subtle text-{{ $item['status'] }} rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                            {{ $item['status_label'] }}
                        </span>
                    </div>
                    <div class="my-2">
                        <h3 class="fw-bold text-{{ $item['status'] }} mb-0">{{ $item['format'] }}</h3>
                        <small class="text-muted">Standar Acuan: <span class="fw-semibold text-dark">{{ $item['benchmark'] }}</span></small>
                    </div>
                    <p class="text-muted small mb-0 mt-2 border-top pt-2">{{ $item['deskripsi'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 3. Rasio Solvabilitas & Aktivitas -->
    <div class="row g-4">
        <!-- Solvabilitas -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-warning-subtle text-warning p-2 me-2 rounded-circle">
                    <i class="bi bi-shield-check fs-6"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">3. Rasio Solvabilitas (Leverage Ratios)</h5>
                    <small class="text-muted">Tingkat perlindungan modal dan risiko ketergantungan utang.</small>
                </div>
            </div>
            <div class="row g-3">
                @foreach($ratios['solvabilitas'] as $item)
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-top border-{{ $item['status'] }} border-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark mb-0 small">{{ $item['nama'] }}</h6>
                            <span class="badge bg-{{ $item['status'] }}-subtle text-{{ $item['status'] }} rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                {{ $item['status_label'] }}
                            </span>
                        </div>
                        <div class="my-2">
                            <h3 class="fw-bold text-{{ $item['status'] }} mb-0">{{ $item['format'] }}</h3>
                            <small class="text-muted">Batas Aman: <span class="fw-semibold text-dark">{{ $item['benchmark'] }}</span></small>
                        </div>
                        <p class="text-muted small mb-0 mt-2 border-top pt-2">{{ $item['deskripsi'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Aktivitas / Efisiensi -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-info-subtle text-info p-2 me-2 rounded-circle">
                    <i class="bi bi-speedometer2 fs-6"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">4. Rasio Aktivitas (Activity & Turnover)</h5>
                    <small class="text-muted">Kecepatan perputaran aset dan perputaran persediaan barang.</small>
                </div>
            </div>
            <div class="row g-3">
                @foreach($ratios['aktivitas'] as $item)
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-top border-{{ $item['status'] }} border-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark mb-0 small">{{ $item['nama'] }}</h6>
                            <span class="badge bg-{{ $item['status'] }}-subtle text-{{ $item['status'] }} rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                {{ $item['status_label'] }}
                            </span>
                        </div>
                        <div class="my-2">
                            <h3 class="fw-bold text-{{ $item['status'] }} mb-0">{{ $item['format'] }}</h3>
                            <small class="text-muted">Target Efisiensi: <span class="fw-semibold text-dark">{{ $item['benchmark'] }}</span></small>
                        </div>
                        <p class="text-muted small mb-0 mt-2 border-top pt-2">{{ $item['deskripsi'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
