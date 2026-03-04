@extends('layouts.app')

@section('title', 'Laporan Persediaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Persediaan</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.persediaan') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.persediaan') }}" class="btn btn-secondary">Reset</a>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <div class="text-center">
                <h4 class="mb-0 fw-bold">{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
                <h5 class="mb-0">Laporan Persediaan Barang</h5>
                <p class="text-muted mb-0">Per Tanggal: {{ date('d F Y') }}</p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Harga Beli (Rata-rata)</th>
                            <th class="text-end">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persediaan as $item)
                            <tr>
                                <td>{{ $item->kode_barang }}</td>
                                <td>{{ $item->barcode }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td class="text-end">{{ number_format($item->stok_saat_ini, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_beli, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->stok_saat_ini * $item->harga_beli, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data persediaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">Grand Total Nilai Persediaan</td>
                            <td class="text-end">Rp {{ number_format($totalNilai, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
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
