@extends('layouts.app')

@section('title', 'Kesehatan Perusahaan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-activity text-danger me-2"></i>Evaluasi Kesehatan Perusahaan
            </h3>
            <p class="text-muted mb-0">Model Altman Z'-Score & Indeks Kesehatan Finansial Komprehensif (4 Pilar Kinerja)</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Laporan Kesehatan
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('analisa.kesehatan_perusahaan') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Awal Periode Laba Rugi</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Posisi Tanggal Neraca</label>
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
                    <button type="submit" class="btn btn-danger btn-sm w-100 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Analisa Kesehatan
                    </button>
                    <a href="{{ route('analisa.kesehatan_perusahaan') }}" class="btn btn-light btn-sm text-secondary border">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Score Overview Grid -->
    <div class="row g-4 mb-4">
        <!-- 1. Skor Kesehatan Komprehensif -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase text-muted fw-bold small">Indeks Kesehatan Finansial</span>
                    <span class="badge bg-{{ $healthAnalysis['grade_badge'] }} rounded-pill px-3 py-1 fs-6">
                        Peringkat: {{ $healthAnalysis['grade'] }}
                    </span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="display-3 fw-bold text-{{ $healthAnalysis['grade_badge'] }} me-3">
                        {{ $healthAnalysis['total_score'] }}
                    </div>
                    <div>
                        <div class="h5 fw-bold text-dark mb-0">{{ $healthAnalysis['grade_label'] }}</div>
                        <small class="text-muted">Skala Penilaian 0 - 100 Poin Berbasis 4 Pilar Kinerja</small>
                    </div>
                </div>

                <!-- 4 Pilar Progress Bars -->
                <div class="mt-2">
                    @foreach($healthAnalysis['pillars'] as $p)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-dark fw-medium">{{ $p['label'] }}</span>
                            <span class="fw-bold">{{ $p['score'] }} / {{ $p['max'] }} Poin</span>
                        </div>
                        <div class="progress" style="height: 7px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($p['score'] / $p['max']) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 2. Altman Z-Score Model -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 bg-white border-start border-{{ $healthAnalysis['z_score']['color'] }} border-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase text-muted fw-bold small">Model Prediksi Altman Z'-Score</span>
                    <span class="badge bg-{{ $healthAnalysis['z_score']['color'] }}-subtle text-{{ $healthAnalysis['z_score']['color'] }} rounded-pill px-3 py-1 fs-6">
                        {{ $healthAnalysis['z_score']['label'] }}
                    </span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="display-3 fw-bold text-{{ $healthAnalysis['z_score']['color'] }} me-3">
                        {{ number_format($healthAnalysis['z_score']['value'], 2, ',', '.') }}
                    </div>
                    <div>
                        <div class="h6 fw-bold text-dark mb-0">Nilai Skor Z'</div>
                        <small class="text-muted">Formula Z'-Score Khusus Perusahaan Non-Publik / Umum</small>
                    </div>
                </div>
                <div class="alert alert-{{ $healthAnalysis['z_score']['color'] }} bg-{{ $healthAnalysis['z_score']['color'] }}-subtle border-0 rounded-3 mb-0 small">
                    <i class="bi bi-info-circle me-1"></i> {{ $healthAnalysis['z_score']['desc'] }}
                </div>
                <div class="mt-3 d-flex justify-content-between small text-muted border-top pt-2">
                    <span>Distress: &lt; 1.23</span>
                    <span>Grey: 1.23 - 2.90</span>
                    <span>Safe: &gt; 2.90</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations & Diagnosis -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-lightbulb text-warning me-2"></i>Diagnosis Manajemen & Rekomendasi Tindakan Korektif
            </h6>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                @foreach($healthAnalysis['recommendations'] as $rec)
                <div class="col-md-6">
                    <div class="d-flex align-items-start p-3 rounded-3 bg-light border border-{{ $rec['tipe'] }}-subtle h-100">
                        <span class="badge bg-{{ $rec['tipe'] }} rounded-pill me-2 mt-1 px-2 py-1">
                            {{ $rec['pilar'] }}
                        </span>
                        <div class="text-dark small">
                            {{ $rec['pesan'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Altman Z-Score Variable Breakdown -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-calculator text-primary me-2"></i>Rincian Komponen Altman Z'-Score (5 Variabel)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Variabel</th>
                        <th style="width: 35%;">Rasio & Komponen</th>
                        <th class="text-end" style="width: 15%;">Nilai Rasio</th>
                        <th class="text-center" style="width: 15%;">Bobot Koefisien</th>
                        <th class="text-end" style="width: 25%;">Kontribusi Skor (Rasio × Bobot)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($healthAnalysis['z_score']['variables'] as $key => $var)
                    <tr>
                        <td class="fw-bold text-primary font-monospace">{{ strtoupper($key) }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $var['label'] }}</div>
                        </td>
                        <td class="text-end font-monospace">{{ number_format($var['val'], 4, ',', '.') }}</td>
                        <td class="text-center font-monospace">{{ number_format($var['bobot'], 3, ',', '.') }}</td>
                        <td class="text-end fw-bold font-monospace">
                            {{ number_format($var['val'] * $var['bobot'], 4, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="table-light fw-bold">
                        <td colspan="4" class="text-end py-3">Total Skor Altman Z':</td>
                        <td class="text-end text-{{ $healthAnalysis['z_score']['color'] }} fs-6 py-3 font-monospace">
                            {{ number_format($healthAnalysis['z_score']['value'], 4, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
