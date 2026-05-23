@extends('layouts.app')

@section('title', 'Neraca - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Neraca</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.neraca') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="per_tanggal" class="form-label">Per Tanggal</label>
                    <input type="date" class="form-control" id="per_tanggal" name="per_tanggal" value="{{ $perTanggal }}">
                </div>
                <div class="col-md-2">
                    <label for="banding_tanggal" class="form-label">Bandingkan (Opsional)</label>
                    <input type="date" class="form-control" id="banding_tanggal" name="banding_tanggal" value="{{ $bandingTanggal }}">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-3">
                    <label for="id_project" class="form-label">Proyek / Program</label>
                    <select name="id_project" id="id_project" class="form-select">
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id_project }}" data-unit="{{ $p->id_unit_usaha }}"
                                {{ request('id_project') == $p->id_project ? 'selected' : '' }}>
                                {{ $p->nama_project }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.neraca') }}" class="btn btn-secondary">Reset</a>
                    <a href="{{ route('laporan.neraca.pdf', request()->all()) }}" class="btn btn-danger" target="_blank">
                        <span data-feather="file-text"></span> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-4">
        <h3>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan Belum Diset' }}</h3>
        <h4>Neraca</h4>
        @if(request('id_project'))
            @php $selectedProj = $projects->firstWhere('id_project', request('id_project')); @endphp
            @if($selectedProj)
                <h5 class="text-primary mb-2">Proyek: {{ $selectedProj->nama_project }} ({{ $selectedProj->kode_project }})</h5>
            @endif
        @endif
        <p class="text-muted">
            Per Tanggal {{ \Carbon\Carbon::parse($perTanggal)->format('d F Y') }}
            @if($bandingTanggal)
                vs {{ \Carbon\Carbon::parse($bandingTanggal)->format('d F Y') }}
            @endif
        </p>
    </div>

    <!-- Balance Check Alert -->
    @if(!$isBalanced)
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid #dc3545;">
        <div style="font-size: 2rem; margin-right: 16px;">⚠️</div>
        <div>
            <h5 class="alert-heading mb-1" style="color: #dc3545;">NERACA TIDAK BALANCE!</h5>
            <p class="mb-1">
                Total Aset: <strong>Rp {{ number_format($totalAset, 2, ',', '.') }}</strong> |
                Total Kewajiban & Ekuitas: <strong>Rp {{ number_format($totalKewajibanEkuitas, 2, ',', '.') }}</strong>
            </p>
            <p class="mb-0">
                Selisih: <strong class="text-danger">Rp {{ number_format(abs($selisih), 2, ',', '.') }}</strong> — 
                Periksa kembali jurnal yang tidak balance atau transaksi yang belum lengkap.
            </p>
        </div>
    </div>
    @else
    <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid #198754;">
        <div style="font-size: 1.5rem; margin-right: 12px;">✅</div>
        <div>
            <strong>NERACA BALANCE</strong> — Total Aset = Total Kewajiban & Ekuitas = 
            <strong>Rp {{ number_format($totalAset, 2, ',', '.') }}</strong>
        </div>
    </div>
    @endif

    <!-- Report Content -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50%">Keterangan</th>
                            <th class="text-end">{{ \Carbon\Carbon::parse($perTanggal)->format('d M Y') }}</th>
                            @if($bandingTanggal)
                                <th class="text-end">{{ \Carbon\Carbon::parse($bandingTanggal)->format('d M Y') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ASET -->
                        <tr class="fw-bold table-primary"><td colspan="{{ $bandingTanggal ? 3 : 2 }}">ASET</td></tr>
                        
                        <!-- Aset Lancar -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4">Aset Lancar</td></tr>
                        @foreach($asetLancar as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Aset Lancar</td>
                            <td class="text-end">Rp {{ number_format($asetLancar->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- Aset Tetap -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4 mt-2">Aset Tetap</td></tr>
                        @foreach($asetTetap as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Aset Tetap</td>
                            <td class="text-end">Rp {{ number_format($asetTetap->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->where('tipe_akun', 'Aset Tetap')->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- TOTAL ASET -->
                        <tr class="fw-bold table-success">
                            <td>TOTAL ASET</td>
                            <td class="text-end">Rp {{ number_format($asetLancar->sum('saldo_akhir') + $asetTetap->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format(
                                    $laporanBanding->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->sum('saldo_akhir') + 
                                    $laporanBanding->where('tipe_akun', 'Aset Tetap')->sum('saldo_akhir'), 
                                    2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- KEWAJIBAN & EKUITAS -->
                        <tr class="fw-bold table-primary mt-4"><td colspan="{{ $bandingTanggal ? 3 : 2 }}">KEWAJIBAN DAN EKUITAS</td></tr>

                        <!-- Kewajiban -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4">Kewajiban</td></tr>
                        @foreach($kewajiban as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Kewajiban</td>
                            <td class="text-end">Rp {{ number_format($kewajiban->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])->sum('saldo_akhir'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- Ekuitas -->
                        <tr class="fw-bold"><td colspan="{{ $bandingTanggal ? 3 : 2 }}" class="ps-4 mt-2">Ekuitas</td></tr>
                        @foreach($ekuitas as $akun)
                            <tr>
                                <td class="ps-5">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_akhir, 2, ',', '.') }}</td>
                                @if($bandingTanggal)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_akhir ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <!-- Laba Rugi Tahun Berjalan -->
                        <tr>
                            <td class="ps-5">Laba Rugi Tahun Berjalan</td>
                            <td class="text-end">Rp {{ number_format($labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($labaRugiBerjalanBanding, 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <tr class="fw-bold bg-light">
                            <td class="ps-4">Total Ekuitas</td>
                            <td class="text-end">Rp {{ number_format($ekuitas->sum('saldo_akhir') + $labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format($laporanBanding->where('tipe_akun', 'Ekuitas')->sum('saldo_akhir') + $labaRugiBerjalanBanding, 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- TOTAL KEWAJIBAN & EKUITAS -->
                        <tr class="fw-bold table-success">
                            <td>TOTAL KEWAJIBAN DAN EKUITAS</td>
                            <td class="text-end">Rp {{ number_format($kewajiban->sum('saldo_akhir') + $ekuitas->sum('saldo_akhir') + $labaRugiBerjalan, 2, ',', '.') }}</td>
                            @if($bandingTanggal)
                                <td class="text-end">Rp {{ number_format(
                                    $laporanBanding->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])->sum('saldo_akhir') + 
                                    $laporanBanding->where('tipe_akun', 'Ekuitas')->sum('saldo_akhir') + 
                                    $labaRugiBerjalanBanding, 
                                    2, ',', '.') }}</td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <div class="row mt-5 text-center">
                <div class="col-md-4 offset-md-2">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_direktur ?? '(....................)' }}</p>
                    <p>Direktur</p>
                </div>
                <div class="col-md-4">
                    <p>Dibuat Oleh,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ $perusahaan->nama_akuntan ?? '(....................)' }}</p>
                    <p>Akuntan</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterUnits() {
        let cabangId = document.getElementById('id_cabang').value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let units = unitSelect.querySelectorAll('option');
        
        let activeUnits = [];
        units.forEach(opt => {
            if (opt.value === "") return;
            if (!cabangId || opt.getAttribute('data-cabang') == cabangId) {
                opt.style.display = "";
                activeUnits.push(opt.value);
            } else {
                opt.style.display = "none";
            }
        });
        
        if (unitSelect.value && !activeUnits.includes(unitSelect.value)) {
            unitSelect.value = "";
        }
        
        filterProjects();
    }

    function filterProjects() {
        let cabangId = document.getElementById('id_cabang').value;
        let unitId = document.getElementById('id_unit_usaha').value;
        let projectSelect = document.getElementById('id_project');
        let projects = projectSelect.querySelectorAll('option');
        
        let visibleUnitIds = [];
        let unitSelect = document.getElementById('id_unit_usaha');
        unitSelect.querySelectorAll('option').forEach(opt => {
            if (opt.value !== "" && opt.style.display !== "none") {
                visibleUnitIds.push(opt.value);
            }
        });

        let activeProjects = [];
        projects.forEach(opt => {
            if (opt.value === "") return;
            let projectUnitId = opt.getAttribute('data-unit');
            
            let show = false;
            if (unitId) {
                show = (projectUnitId == unitId);
            } else {
                show = visibleUnitIds.includes(projectUnitId);
            }

            if (show) {
                opt.style.display = "";
                activeProjects.push(opt.value);
            } else {
                opt.style.display = "none";
            }
        });

        if (projectSelect.value && !activeProjects.includes(projectSelect.value)) {
            projectSelect.value = "";
        }
    }

    document.getElementById('id_cabang').addEventListener('change', filterUnits);
    document.getElementById('id_unit_usaha').addEventListener('change', filterProjects);

    // Trigger on load
    filterUnits();
    
    // Set initial project selection if request exists
    let initialProject = "{{ request('id_project') }}";
    if (initialProject) {
        document.getElementById('id_project').value = initialProject;
    }
</script>
@endpush
