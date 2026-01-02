@extends('layouts.app')

@section('title', 'Outstanding Simpanan dan Pinjaman')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Outstanding Simpanan dan Pinjaman</h1>
    <form class="d-flex gap-2" method="GET">
        <input type="date" name="per_tanggal" class="form-control form-control-sm" value="{{ $perTanggal }}">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </form>
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
