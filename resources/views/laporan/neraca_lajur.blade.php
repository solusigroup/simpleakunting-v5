@extends('layouts.app')

@section('title', 'Neraca Lajur 10 Kolom - Simple Akunting')

@section('content')
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Neraca Lajur 10 Kolom</h1>
            <p class="page-subtitle">Periode s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>
        <div class="d-flex no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm me-2" style="margin-right: 8px;">
                <span data-feather="printer" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Cetak
            </button>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-primary btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="form-card mb-4 no-print">
        <div class="form-card-body">
            <form action="{{ route('laporan.neraca_lajur') }}" method="GET">
                <div class="row align-items-end" style="display: flex; gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Cabang</label>
                        <select name="id_cabang" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}" {{ request('id_cabang') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Unit Usaha</label>
                        <select name="id_unit_usaha" class="form-select">
                            <option value="">Semua Unit</option>
                            @foreach($unitUsaha as $u)
                                <option value="{{ $u->id }}" {{ request('id_unit_usaha') == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="font-size: 0.75rem; border-collapse: collapse; width: 100%;">
                <thead class="text-center" style="background: var(--color-bg-light, #f8f9fa);">
                    <tr>
                        <th rowspan="2" style="vertical-align: middle; border: 1px solid #dee2e6;">Kode</th>
                        <th rowspan="2" style="vertical-align: middle; border: 1px solid #dee2e6;">Nama Akun</th>
                        <th colspan="2" style="border: 1px solid #dee2e6;">Neraca Saldo</th>
                        <th colspan="2" style="border: 1px solid #dee2e6;">Penyesuaian</th>
                        <th colspan="2" style="border: 1px solid #dee2e6;">NS Setelah Penyesuaian</th>
                        <th colspan="2" style="border: 1px solid #dee2e6;">Laba Rugi</th>
                        <th colspan="2" style="border: 1px solid #dee2e6;">Neraca</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #dee2e6;">D</th>
                        <th style="border: 1px solid #dee2e6;">K</th>
                        <th style="border: 1px solid #dee2e6;">D</th>
                        <th style="border: 1px solid #dee2e6;">K</th>
                        <th style="border: 1px solid #dee2e6;">D</th>
                        <th style="border: 1px solid #dee2e6;">K</th>
                        <th style="border: 1px solid #dee2e6;">D</th>
                        <th style="border: 1px solid #dee2e6;">K</th>
                        <th style="border: 1px solid #dee2e6;">D</th>
                        <th style="border: 1px solid #dee2e6;">K</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = [
                            'ns_d' => 0, 'ns_k' => 0,
                            'adj_d' => 0, 'adj_k' => 0,
                            'nssp_d' => 0, 'nssp_k' => 0,
                            'lr_d' => 0, 'lr_k' => 0,
                            'n_d' => 0, 'n_k' => 0
                        ];
                    @endphp
                    @foreach($data as $row)
                        <tr>
                            <td style="border: 1px solid #dee2e6; padding: 4px;">{{ $row['kode_akun'] }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 4px;">{{ $row['nama_akun'] }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['ns_d'] != 0 ? number_format($row['ns_d'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['ns_k'] != 0 ? number_format($row['ns_k'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['adj_d'] != 0 ? number_format($row['adj_d'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['adj_k'] != 0 ? number_format($row['adj_k'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['nssp_d'] != 0 ? number_format($row['nssp_d'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['nssp_k'] != 0 ? number_format($row['nssp_k'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['lr_d'] != 0 ? number_format($row['lr_d'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['lr_k'] != 0 ? number_format($row['lr_k'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['n_d'] != 0 ? number_format($row['n_d'], 0, ',', '.') : '-' }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ $row['n_k'] != 0 ? number_format($row['n_k'], 0, ',', '.') : '-' }}</td>
                        </tr>
                        @php
                            foreach($totals as $k => $v) $totals[$k] += $row[$k];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot style="background: var(--color-bg-light, #f8f9fa); font-weight: bold;">
                    <tr>
                        <td colspan="2" class="text-center" style="border: 1px solid #dee2e6; text-align: center;">Jumlah</td>
                        @foreach($totals as $k => $v)
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($v, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    @php
                        $labaRugiNet = $totals['lr_k'] - $totals['lr_d'];
                        $isUntung = $labaRugiNet >= 0;
                    @endphp
                    <tr>
                        <td colspan="2" class="text-center" style="border: 1px solid #dee2e6; text-align: center;">{{ $isUntung ? 'Laba Bersih' : 'Rugi Bersih' }}</td>
                        <td colspan="6" style="border: 1px solid #dee2e6;"></td>
                        @if($isUntung)
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($labaRugiNet, 0, ',', '.') }}</td>
                            <td style="border: 1px solid #dee2e6;"></td>
                            <td style="border: 1px solid #dee2e6;"></td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($labaRugiNet, 0, ',', '.') }}</td>
                        @else
                            <td style="border: 1px solid #dee2e6;"></td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format(abs($labaRugiNet), 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format(abs($labaRugiNet), 0, ',', '.') }}</td>
                            <td style="border: 1px solid #dee2e6;"></td>
                        @endif
                    </tr>
                    <tr style="background: var(--color-bg-light, #f8f9fa);">
                        <td colspan="2" class="text-center" style="border: 1px solid #dee2e6; text-align: center;">Total Akhir</td>
                        <td colspan="6" style="border: 1px solid #dee2e6;"></td>
                        @if($isUntung)
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['lr_d'] + $labaRugiNet, 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['lr_k'], 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['n_d'], 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['n_k'] + $labaRugiNet, 0, ',', '.') }}</td>
                        @else
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['lr_d'], 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['lr_k'] + abs($labaRugiNet), 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['n_d'] + abs($labaRugiNet), 0, ',', '.') }}</td>
                            <td class="text-end" style="border: 1px solid #dee2e6; padding: 4px; text-align: right;">{{ number_format($totals['n_k'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .table-responsive { overflow: visible !important; }
        table { font-size: 0.65rem !important; }
        .page-header-actions { margin-bottom: 10px !important; }
    }
</style>
@endpush
