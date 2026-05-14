@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Buku Pembantu Piutang</h2>
            <p class="text-muted">Rincian transaksi piutang per pelanggan untuk memastikan saldo yang akurat.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.piutang') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Pelanggan</label>
                    <select name="id_pelanggan" class="form-select select2" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelanggans as $p)
                            <option value="{{ $p->id_pelanggan }}" {{ $idPelanggan == $p->id_pelanggan ? 'selected' : '' }}>
                                {{ $p->nama_pelanggan }} ({{ $p->kode_pelanggan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Cabang</label>
                    <select name="id_cabang" id="id_cabang" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach($cabang as $c)
                            <option value="{{ $c->id }}" {{ request('id_cabang', session('active_cabang')) == $c->id ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Unit Usaha</label>
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
                <div class="col-md-2">
                    <label class="form-label fw-bold">Per Tanggal</label>
                    <input type="date" name="per_tanggal" class="form-control" value="{{ $perTanggal }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

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

    @if($idPelanggan)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Rincian Transaksi: {{ $pelanggans->firstWhere('id_pelanggan', $idPelanggan)->nama_pelanggan }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small fw-bold">
                    <tr>
                        <th width="120">Tanggal</th>
                        <th width="150">No. Bukti</th>
                        <th>Keterangan</th>
                        <th width="150" class="text-end">Debet (+)</th>
                        <th width="150" class="text-end">Kredit (-)</th>
                        <th width="180" class="text-end">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saldo = 0; @endphp
                    @forelse($data as $row)
                        @php $saldo += ($row->debit - $row->kredit); @endphp
                        <tr>
                            <td>{{ $row->jurnal ? date('d/m/Y', strtotime($row->jurnal->tanggal)) : '-' }}</td>
                            <td class="fw-bold">{{ $row->jurnal->no_transaksi ?? '-' }}</td>
                            <td>{{ $row->jurnal->deskripsi ?? $row->jurnal->keterangan ?? '-' }}</td>
                            <td class="text-end text-success">{{ number_format($row->debit, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($row->kredit, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($saldo, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada transaksi piutang untuk pelanggan ini sampai tanggal {{ date('d/m/Y', strtotime($perTanggal)) }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-uppercase small">Total Akhir</td>
                        <td class="text-end">{{ number_format($data->sum('debit'), 2) }}</td>
                        <td class="text-end">{{ number_format($data->sum('kredit'), 2) }}</td>
                        <td class="text-end fs-5 text-primary">{{ number_format($saldo, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
