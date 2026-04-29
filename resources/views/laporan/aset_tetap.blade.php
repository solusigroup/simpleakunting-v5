@extends('layouts.app')

@section('title', 'Laporan Daftar Aset Tetap - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Daftar Aset Tetap</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.aset_tetap') }}" method="GET" class="row g-3 align-items-end">
            <!-- Filter Cabang -->
            @if(auth()->user()->hasRole(['superuser', 'admin']) && $cabang->count() > 0)
                <div class="col-md-4">
                    <label class="form-label">Cabang</label>
                    <select name="id_cabang" class="form-select select2">
                        <option value="">-- Semua Cabang --</option>
                        @foreach($cabang as $c)
                            <option value="{{ $c->id }}" {{ request('id_cabang') == $c->id ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3">
                <label class="form-label">Status Aset</label>
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Dijual" {{ request('status') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
                    <option value="Dibuang" {{ request('status') == 'Dibuang' ? 'selected' : '' }}>Dibuang</option>
                    <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" onclick="window.print()">
                    <span data-feather="printer"></span> Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" id="printableArea">
    <div class="card-header bg-white text-center py-4">
        <h4 class="mb-0">{{ $perusahaan->nama_perusahaan ?? 'Simple Akunting' }}</h4>
        <h5 class="mb-0">LAPORAN DAFTAR ASET TETAP</h5>
    </div>
    <div class="card-body p-0">
        <!-- Rekapitulasi per Kelompok -->
        <div class="p-3 bg-light border-bottom">
            <h6 class="fw-bold mb-3">Ringkasan per Kelompok Aset</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Kelompok Aset</th>
                            <th class="text-center">Jumlah Aset</th>
                            <th class="text-end">Total Perolehan (Rp)</th>
                            <th class="text-end">Akumulasi Penyusutan (Rp)</th>
                            <th class="text-end">Total Nilai Buku (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotalQty = 0;
                            $grandTotalPerolehan = 0;
                            $grandTotalAkumulasi = 0;
                            $grandTotalBuku = 0;
                        @endphp
                        @foreach($rekapGroup as $groupName => $rekap)
                            @php
                                $grandTotalQty += $rekap['jumlah_aset'];
                                $grandTotalPerolehan += $rekap['total_perolehan'];
                                $grandTotalAkumulasi += $rekap['total_akumulasi'];
                                $grandTotalBuku += $rekap['total_nilai_buku'];
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $groupName }}</td>
                                <td class="text-center">{{ $rekap['jumlah_aset'] }}</td>
                                <td class="text-end">{{ number_format($rekap['total_perolehan'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($rekap['total_akumulasi'], 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($rekap['total_nilai_buku'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th>GRAND TOTAL</th>
                            <th class="text-center">{{ $grandTotalQty }}</th>
                            <th class="text-end">{{ number_format($grandTotalPerolehan, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($grandTotalAkumulasi, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($grandTotalBuku, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Rincian Aset Tetap -->
        <div class="p-3">
            <h6 class="fw-bold mb-3 mt-2">Rincian Daftar Aset</h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Kelompok</th>
                            <th>Tgl Perolehan</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">H. Perolehan</th>
                            <th class="text-end">Akum. Susut</th>
                            <th class="text-end">Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asets as $index => $a)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $a->kode_aset }}</td>
                                <td>{{ $a->nama_aset }}</td>
                                <td>{{ $a->group->nama_kelompok ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->tanggal_perolehan)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $a->status == 'Aktif' ? 'success' : ($a->status == 'Dijual' ? 'warning' : 'danger') }}">
                                        {{ $a->status }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($a->harga_perolehan, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($a->harga_perolehan - $a->nilai_buku_saat_ini, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($a->nilai_buku_saat_ini, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data aset tetap.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn, form, header, aside {
            display: none !important;
        }
        .card {
            border: none !important;
        }
        .bg-light {
            background-color: transparent !important;
        }
    }
</style>
@endsection
