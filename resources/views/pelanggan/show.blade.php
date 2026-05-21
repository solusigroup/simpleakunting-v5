@extends('layouts.app')

@section('title', 'Buku Pembantu Piutang - ' . $pelanggan->nama_pelanggan)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buku Pembantu Piutang</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <a href="{{ route('pelanggan.index') }}" class="btn btn-sm btn-outline-secondary">
                <span data-feather="arrow-left"></span> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                <span data-feather="printer"></span> Cetak
            </button>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-1">{{ $pelanggan->nama_pelanggan }}</h5>
                    <p class="text-muted small mb-0">{{ $pelanggan->alamat }}</p>
                    <p class="text-muted small">{{ $pelanggan->telepon }} | {{ $pelanggan->email }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-1">Saldo Piutang Terkini</p>
                    <h3 class="fw-bold">Rp {{ number_format((float)($pelanggan->saldo_terkini_piutang ?? 0), 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4 shadow-sm no-print">
        <div class="card-body">
            <form action="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0 py-1">Histori Transaksi Periode {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Keterangan</th>
                        <th class="text-end">Debit (+)</th>
                        <th class="text-end">Kredit (-)</th>
                        <th class="text-end px-3">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-3 text-muted italic" colspan="3">Saldo Awal per {{ date('d/m/Y', strtotime($startDate)) }}</td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end px-3 fw-bold">Rp {{ number_format((float)($saldoAwalPeriode ?? 0), 2, ',', '.') }}</td>
                    </tr>
                    @php $runningBalance = $saldoAwalPeriode; @endphp
                    @foreach($transactions as $t)
                        @php $runningBalance += ($t->debit - $t->kredit); @endphp
                        <tr>
                            <td class="px-3">{{ $t->jurnal ? date('d/m/Y', strtotime($t->jurnal->tanggal)) : '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $t->jurnal ? $t->jurnal->no_transaksi : '-' }}</span>
                            </td>
                            <td>{{ $t->jurnal ? $t->jurnal->deskripsi : '-' }}</td>
                            <td class="text-end text-success">
                                {{ $t->debit > 0 ? 'Rp ' . number_format((float)$t->debit, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger">
                                {{ $t->kredit > 0 ? 'Rp ' . number_format((float)$t->kredit, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-end px-3 fw-bold">
                                Rp {{ number_format((float)($runningBalance ?? 0), 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end px-3">Total Saldo Akhir</th>
                        <th class="text-end px-3">Rp {{ number_format((float)($runningBalance ?? 0), 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
