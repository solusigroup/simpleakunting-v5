@extends('layouts.app')

@section('title', 'Audit & Diagnosa Neraca')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Alat Diagnosa Neraca</h1>
    <form action="{{ route('audit.neraca') }}" method="GET" class="d-flex gap-2">
        <input type="date" name="per_tanggal" class="form-control form-control-sm" value="{{ $perTanggal }}">
        <button type="submit" class="btn btn-sm btn-primary">Analisis</button>
    </form>
</div>

<div class="row">
    <!-- Ringkasan Persamaan Akuntansi -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0 {{ abs($gap) < 0.01 ? 'bg-success text-white' : 'bg-danger text-white' }}">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">Status Keseimbangan (Equation Check)</h5>
                        <p class="mb-0 small">Persamaan: Aset = Kewajiban + Ekuitas + Laba Berjalan</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h3 class="mb-0">Selisih: Rp {{ number_format($gap, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Persamaan -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light fw-bold">AKTIVA (Harta)</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Seluruh Aset:</span>
                    <span class="fw-bold">Rp {{ number_format($totalAset, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light fw-bold">PASIVA (Kewajiban + Modal)</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <span>Total Kewajiban:</span>
                    <span>Rp {{ number_format($totalKewajiban, 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Total Ekuitas (Modal):</span>
                    <span>Rp {{ number_format($totalEkuitas, 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                    <span>Laba Tahun Berjalan:</span>
                    <span>Rp {{ number_format($labaBerjalan, 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold pt-1">
                    <span>TOTAL PASIVA:</span>
                    <span>Rp {{ number_format($totalPasiva, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosa Masalah -->
    <div class="col-md-12">
        <h5 class="mb-3">Temuan Diagnosa:</h5>
        
        <!-- 1. Jurnal Tidak Balance -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>1. Jurnal Umum Tidak Balance</span>
                <span class="badge bg-danger">{{ count($unbalancedData) }} Temuan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Total Debit</th>
                            <th class="text-end">Total Kredit</th>
                            <th class="text-end">Selisih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unbalancedData as $j)
                        <tr>
                            <td>{{ $j->no_transaksi }}</td>
                            <td>{{ $j->tanggal }}</td>
                            <td>{{ $j->deskripsi }}</td>
                            <td class="text-end">{{ number_format($j->total_debit, 2) }}</td>
                            <td class="text-end">{{ number_format($j->total_kredit, 2) }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($j->selisih, 2) }}</td>
                            <td>
                                <a href="{{ route('jurnal.edit', $j->id_jurnal) }}" class="btn btn-xs btn-primary py-0 px-2" target="_blank">Perbaiki</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-success">Semua jurnal sudah balance. ✅</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Akun Tanpa Klasifikasi -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>2. Akun Tanpa Klasifikasi Tipe (Tidak Muncul di Neraca/LR)</span>
                <span class="badge bg-warning text-dark">{{ count($invalidAccounts) }} Temuan</span>
            </div>
            <div class="card-body bg-light border-bottom">
                <p class="small mb-0 fw-bold text-muted text-uppercase">Daftar Tipe Akun yang Dikenali Sistem:</p>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($allowedTypes as $type)
                        <span class="badge bg-secondary opacity-75" style="font-size: 0.7rem;">{{ $type }}</span>
                    @endforeach
                </div>
                <div class="mt-2 text-danger small">
                    <span data-feather="info" style="width: 14px; height: 14px;"></span> 
                    <strong>Penting:</strong> Ejaan dan spasi harus sama persis dengan daftar di atas. Pastikan tidak ada spasi tambahan di awal atau akhir nama tipe.
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Tipe Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invalidAccounts as $a)
                        <tr>
                            <td>{{ $a->kode_akun }}</td>
                            <td>{{ $a->nama_akun }}</td>
                            <td><span class="badge bg-danger">{{ $a->tipe_akun ?: 'KOSONG' }}</span></td>
                            <td>
                                <a href="{{ route('akun.index') }}?search={{ $a->kode_akun }}" class="btn btn-xs btn-primary py-0 px-2">Ubah Master Akun</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-success">Semua akun sudah memiliki klasifikasi yang benar. ✅</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Integritas Database -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <span>3. Integritas Database</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Data Jurnal Detail Tanpa Induk (Orphaned):</span>
                    <span class="fw-bold {{ $orphanedDetails > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $orphanedDetails }} Record {{ $orphanedDetails > 0 ? '⚠️' : '✅' }}
                    </span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <span>Integritas Total Debit vs Kredit (Seluruh DB):</span>
                    <span class="fw-bold {{ $grandDiff > 0.01 ? 'text-danger' : 'text-success' }}">
                        {{ $grandDiff > 0.01 ? 'Selisih Rp '.number_format($grandDiff, 2) : 'Balance ✅' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 4. Duplikasi Nama Akun -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>4. Duplikasi Nama Akun (Kode Berbeda)</span>
                @php $totalDuplicates = $duplicateAccounts->flatten(1)->count(); @endphp
                <span class="badge {{ $totalDuplicates > 0 ? 'bg-warning text-dark' : 'bg-success' }}">{{ $duplicateAccounts->count() }} Grup ({{ $totalDuplicates }} Akun)</span>
            </div>
            @if($duplicateAccounts->count() > 0)
            <div class="card-body border-bottom bg-light">
                <p class="small mb-0 text-muted">
                    <span data-feather="info" style="width: 14px; height: 14px;"></span>
                    Akun-akun berikut memiliki <strong>nama yang sama tetapi kode berbeda</strong>. Centang akun duplikat yang ingin dihapus, lalu klik <strong>Hapus Terpilih</strong>.
                    Akun yang sudah digunakan dalam transaksi tidak dapat dihapus (gunakan fitur <strong>Merge</strong> di menu Master Akun).
                </p>
            </div>
            <form method="POST" action="{{ route('akun.bulk-delete-duplicates') }}" id="bulkDeleteForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="selectAllDuplicates" class="form-check-input" title="Pilih Semua yang Bisa Dihapus">
                                </th>
                                <th>Nama Akun</th>
                                <th>Kode Akun</th>
                                <th>Tipe</th>
                                <th class="text-end">Saldo Awal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($duplicateAccounts as $namaAkun => $group)
                                @foreach($group as $idx => $a)
                                <tr class="{{ $idx === 0 ? 'table-warning' : '' }}">
                                    <td>
                                        @if($a->has_transactions)
                                            <input type="checkbox" class="form-check-input" disabled title="Tidak bisa dihapus: sudah digunakan dalam transaksi">
                                        @else
                                            <input type="checkbox" name="kode_akun[]" value="{{ $a->kode_akun }}" class="form-check-input dup-checkbox">
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $idx === 0 ? '' : 'text-danger' }}">{{ $a->nama_akun }}</td>
                                    <td><span class="badge bg-secondary">{{ $a->kode_akun }}</span></td>
                                    <td>{{ $a->tipe_akun }}</td>
                                    <td class="text-end">Rp {{ number_format($a->saldo_awal ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @if($a->has_transactions)
                                            <span class="badge bg-info text-dark">Digunakan</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Tidak Digunakan</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @if(!$loop->last)
                                <tr><td colspan="6" class="p-0" style="border-bottom: 2px solid #dee2e6;"></td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted"><span id="selectedCount">0</span> akun dipilih</small>
                    <button type="submit" class="btn btn-sm btn-danger" id="btnBulkDelete" disabled
                        onclick="return confirm('Yakin ingin menghapus akun yang dipilih? Proses ini tidak bisa dibatalkan.')">
                        <span data-feather="trash-2" style="width:14px;height:14px;"></span> Hapus Terpilih
                    </button>
                </div>
            </form>
            @else
            <div class="card-body">
                <p class="text-center py-3 text-success mb-0">Tidak ada duplikasi nama akun. ✅</p>
            </div>
            @endif
        </div>

    <!-- Missing Master Accounts Alert -->
    @if(count($missingMasterAccounts) > 0)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-danger shadow-sm">
                <h5 class="alert-heading">⚠️ Ditemukan Akun Tanpa Master!</h5>
                <p>Daftar kode akun berikut memiliki transaksi tapi tidak terdaftar di Master Akun: 
                    <strong>{{ $missingMasterAccounts->implode(', ') }}</strong>
                </p>
                <hr>
                <p class="mb-0">Mohon segera buat akun ini di menu Master Akun agar nilainya muncul di laporan.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .btn-xs { font-size: 0.75rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAllDuplicates');
    var checkboxes = document.querySelectorAll('.dup-checkbox');
    var btnDelete = document.getElementById('btnBulkDelete');
    var countEl = document.getElementById('selectedCount');

    if (!selectAll || !btnDelete) return;

    function updateState() {
        var checked = document.querySelectorAll('.dup-checkbox:checked').length;
        countEl.textContent = checked;
        btnDelete.disabled = checked === 0;
        selectAll.checked = checkboxes.length > 0 && checked === checkboxes.length;
        selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(function(cb) { cb.checked = selectAll.checked; });
        updateState();
    });

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateState);
    });
});
</script>
@endsection
