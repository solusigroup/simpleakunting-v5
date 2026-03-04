@extends('layouts.app')

@section('title', 'Outstanding Simpanan dan Pinjaman')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Outstanding Simpanan dan Pinjaman</h1>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.outstanding_simpan_pinjam') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="per_tanggal" class="form-label">Per Tanggal</label>
                <input type="date" name="per_tanggal" id="per_tanggal" class="form-control" value="{{ $perTanggal }}">
            </div>
            <div class="col-md-3">
                <label for="id_cabang" class="form-label">Cabang</label>
                <select name="id_cabang" id="id_cabang" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->id }}" {{ request('id_cabang', session('active_cabang')) == $c->id ? 'selected' : '' }}>
                            {{ $c->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_unit_usaha" class="form-label">Unit Usaha</label>
                <select name="id_unit_usaha" id="id_unit_usaha" class="form-select">
                    <option value="">Semua Unit</option>
                    @foreach($unitUsaha as $u)
                        <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" 
                            {{ request('id_unit_usaha', session('active_unit')) == $u->id ? 'selected' : '' }}>
                            {{ $u->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('laporan.outstanding_simpan_pinjam') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Simpanan</h5>
                <h2>Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Total Pinjaman Outstanding</h5>
                <h2>Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">💰 Outstanding Simpanan per Anggota</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>No. Anggota</th>
                                <th>Nama Anggota</th>
                                <th>Jenis Simpanan</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($simpanan as $s)
                            <tr>
                                <td>{{ $s->no_anggota }}</td>
                                <td>{{ $s->nama_lengkap }}</td>
                                <td>{{ $s->nama_simpanan }}</td>
                                <td class="text-end">Rp {{ number_format($s->saldo, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📋 Outstanding Pinjaman per Anggota</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>No. Anggota</th>
                                <th>Nama Anggota</th>
                                <th>Jenis Pinjaman</th>
                                <th class="text-end">Sisa Pokok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pinjaman as $p)
                            <tr>
                                <td>{{ $p->no_anggota }}</td>
                                <td>{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->nama_pinjaman }}</td>
                                <td class="text-end">Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('id_cabang').addEventListener('change', function() {
        let cabangId = this.value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let units = unitSelect.querySelectorAll('option');
        
        unitSelect.value = "";
        units.forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-cabang') == cabangId || !cabangId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });
    });

    // Trigger on load
    if (document.getElementById('id_cabang').value) {
        let cabangId = document.getElementById('id_cabang').value;
        let unitSelect = document.getElementById('id_unit_usaha');
        
        unitSelect.querySelectorAll('option').forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-cabang') == cabangId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });
    }
</script>
@endpush
