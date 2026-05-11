@extends('layouts.app')

@section('title', 'Daftar Retur Penjualan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Retur Penjualan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('retur.penjualan.create') }}" class="btn btn-sm btn-primary">
                <span data-feather="plus"></span> Buat Retur Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th scope="col">TANGGAL</th>
                    <th scope="col">NO. RETUR</th>
                    <th scope="col">PELANGGAN</th>
                    <th scope="col">NO. FAKTUR</th>
                    <th scope="col">TOTAL RETUR</th>
                    <th scope="col">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($retur as $r)
                    <tr>
                        <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                        <td><span class="badge bg-info text-dark">{{ $r->no_retur }}</span></td>
                        <td>{{ $r->pelanggan->nama_pelanggan }}</td>
                        <td>{{ $r->penjualan->no_faktur }}</td>
                        <td>Rp {{ number_format($r->total_retur, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('retur.penjualan.show', $r->id_retur_penjualan) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail / Nota">
                                <span data-feather="eye"></span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data retur penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $retur->links() }}
    </div>
@endsection
