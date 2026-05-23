@extends('layouts.app')

@section('title', 'Laba Rugi - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Laba Rugi</h1>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.labarugi') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-3 mb-2">
                            <div class="col-md-12 fw-bold">Periode Utama</div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12 fw-bold mt-2">Periode Pembanding (Opsional)</div>
                            <div class="col-md-6">
                                <label for="start_banding" class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" id="start_banding" name="start_banding" value="{{ $startBanding }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end_banding" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="end_banding" name="end_banding" value="{{ $endBanding }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-md-12 fw-bold">Filter Cabang, Unit & Proyek</div>
                            <div class="col-md-12">
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
                            <div class="col-md-6">
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
                            <div class="col-md-6">
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
                        </div>
                    </div>
                    <div class="col-md-12 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        <a href="{{ route('laporan.labarugi.pdf', request()->all()) }}" class="btn btn-danger" target="_blank">
                            <span data-feather="file-text"></span> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Header -->
    <div class="text-center mb-4">
        <h3>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan Belum Diset' }}</h3>
        <h4>Laporan Laba Rugi</h4>
        @if(request('id_project'))
            @php $selectedProj = $projects->firstWhere('id_project', request('id_project')); @endphp
            @if($selectedProj)
                <h5 class="text-primary mb-2">Proyek: {{ $selectedProj->nama_project }} ({{ $selectedProj->kode_project }})</h5>
            @endif
        @endif
        <p class="text-muted">
            Periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            @if($startBanding && $endBanding)
                <br>vs<br>
                Periode {{ \Carbon\Carbon::parse($startBanding)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endBanding)->format('d F Y') }}
            @endif
        </p>
    </div>

    <!-- Report Content -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50%">Keterangan</th>
                            <th class="text-end">Periode Ini</th>
                            @if($startBanding && $endBanding)
                                <th class="text-end">Periode Pembanding</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PENDAPATAN -->
                        <tr class="fw-bold table-primary"><td colspan="{{ ($startBanding && $endBanding) ? 3 : 2 }}">PENDAPATAN</td></tr>
                        @foreach($pendapatan as $akun)
                            <tr>
                                <td class="ps-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_periode, 2, ',', '.') }}</td>
                                @if($startBanding && $endBanding)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_periode ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td>Total Pendapatan</td>
                            <td class="text-end">Rp {{ number_format($pendapatan->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @if($startBanding && $endBanding)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya'])->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- HPP -->
                        <tr class="fw-bold table-warning mt-4"><td colspan="{{ ($startBanding && $endBanding) ? 3 : 2 }}">HARGA POKOK PENJUALAN</td></tr>
                        @foreach($hpp as $akun)
                            <tr>
                                <td class="ps-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_periode, 2, ',', '.') }}</td>
                                @if($startBanding && $endBanding)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_periode ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td>Total HPP</td>
                            <td class="text-end">Rp {{ number_format($hpp->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @if($startBanding && $endBanding)
                                <td class="text-end">Rp {{ number_format($laporanBanding->where('tipe_akun', 'HPP')->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- LABA KOTOR -->
                        <tr class="fw-bold table-success">
                            <td>LABA KOTOR</td>
                            <td class="text-end">Rp {{ number_format($pendapatan->sum('saldo_periode') - $hpp->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @if($startBanding && $endBanding)
                                <td class="text-end">Rp {{ number_format(
                                    $laporanBanding->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya'])->sum('saldo_periode') - 
                                    $laporanBanding->where('tipe_akun', 'HPP')->sum('saldo_periode'), 
                                    2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- BEBAN -->
                        <tr class="fw-bold table-danger mt-4"><td colspan="{{ ($startBanding && $endBanding) ? 3 : 2 }}">BEBAN OPERASIONAL</td></tr>
                        @foreach($beban as $akun)
                            <tr>
                                <td class="ps-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</td>
                                <td class="text-end">Rp {{ number_format($akun->saldo_periode, 2, ',', '.') }}</td>
                                @if($startBanding && $endBanding)
                                    @php $banding = $laporanBanding->where('kode_akun', $akun->kode_akun)->first()->saldo_periode ?? 0; @endphp
                                    <td class="text-end">Rp {{ number_format($banding, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="fw-bold bg-light">
                            <td>Total Beban</td>
                            <td class="text-end">Rp {{ number_format($beban->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @if($startBanding && $endBanding)
                                <td class="text-end">Rp {{ number_format($laporanBanding->whereIn('tipe_akun', ['Beban', 'Beban Lainnya'])->sum('saldo_periode'), 2, ',', '.') }}</td>
                            @endif
                        </tr>

                        <!-- LABA BERSIH -->
                        <tr class="fw-bold table-success fs-5">
                            <td>LABA BERSIH</td>
                            <td class="text-end">Rp {{ number_format(
                                ($pendapatan->sum('saldo_periode') - $hpp->sum('saldo_periode')) - $beban->sum('saldo_periode'), 
                                2, ',', '.') }}</td>
                            @if($startBanding && $endBanding)
                                <td class="text-end">Rp {{ number_format(
                                    ($laporanBanding->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya'])->sum('saldo_periode') - 
                                    $laporanBanding->where('tipe_akun', 'HPP')->sum('saldo_periode')) - 
                                    $laporanBanding->whereIn('tipe_akun', ['Beban', 'Beban Lainnya'])->sum('saldo_periode'), 
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
