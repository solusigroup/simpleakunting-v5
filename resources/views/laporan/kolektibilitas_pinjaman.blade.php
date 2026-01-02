@extends('layouts.app')

@section('title', 'Kolektibilitas Pinjaman')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Kolektibilitas Pinjaman</h1>
    <form class="d-flex gap-2" method="GET">
        <input type="date" name="per_tanggal" class="form-control form-control-sm" value="{{ $perTanggal }}">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">📊 Rekapitulasi Kolektibilitas</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Kol</th>
                        <th>Status</th>
                        <th class="text-end">Jumlah Pinjaman</th>
                        <th class="text-end">Total Sisa Pokok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $r)
                    <tr>
                        <td>
                            @if($r['kolektibilitas'] == 1)
                                <span class="badge bg-success">{{ $r['kolektibilitas'] }}</span>
                            @elseif($r['kolektibilitas'] == 2)
                                <span class="badge bg-info">{{ $r['kolektibilitas'] }}</span>
                            @elseif($r['kolektibilitas'] == 3)
                                <span class="badge bg-warning">{{ $r['kolektibilitas'] }}</span>
                            @elseif($r['kolektibilitas'] == 4)
                                <span class="badge bg-orange">{{ $r['kolektibilitas'] }}</span>
                            @else
                                <span class="badge bg-danger">{{ $r['kolektibilitas'] }}</span>
                            @endif
                        </td>
                        <td>{{ $r['status'] }}</td>
                        <td class="text-end">{{ $r['jumlah_pinjaman'] }}</td>
                        <td class="text-end">Rp {{ number_format($r['total_sisa_pokok'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-end">{{ $rekap->sum('jumlah_pinjaman') }}</th>
                        <th class="text-end">Rp {{ number_format($rekap->sum('total_sisa_pokok'), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📋 Detail Pinjaman per Anggota</h5>
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
                        <th>Jatuh Tempo</th>
                        <th class="text-end">Hari Tunggak</th>
                        <th>Kolektibilitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman as $p)
                    <tr>
                        <td>{{ $p->no_anggota }}</td>
                        <td>{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->nama_pinjaman }}</td>
                        <td class="text-end">Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}</td>
                        <td>{{ $p->tanggal_jatuh_tempo }}</td>
                        <td class="text-end">{{ number_format($p->hari_tunggak, 0) }}</td>
                        <td>
                            @if($p->kolektibilitas == 1)
                                <span class="badge bg-success">{{ $p->status_kolektibilitas }}</span>
                            @elseif($p->kolektibilitas == 2)
                                <span class="badge bg-info">{{ $p->status_kolektibilitas }}</span>
                            @elseif($p->kolektibilitas == 3)
                                <span class="badge bg-warning">{{ $p->status_kolektibilitas }}</span>
                            @elseif($p->kolektibilitas == 4)
                                <span class="badge bg-orange text-dark">{{ $p->status_kolektibilitas }}</span>
                            @else
                                <span class="badge bg-danger">{{ $p->status_kolektibilitas }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Tidak ada data pinjaman aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
