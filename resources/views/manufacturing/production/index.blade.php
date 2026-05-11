@extends('layouts.app')

@section('title', 'Daftar Produksi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Produksi (Manufacturing Orders)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('manufacturing.production.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-gear-fill"></i> Produksi Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>No Produksi</th>
                <th>Tanggal</th>
                <th>BOM</th>
                <th>Output Product</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Cabang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $prod)
            <tr>
                <td>{{ $prod->no_produksi }}</td>
                <td>{{ \Carbon\Carbon::parse($prod->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $prod->bom->nama_bom ?? '-' }}</td>
                <td>{{ $prod->bom->barangJadi->nama_barang ?? '-' }}</td>
                <td>{{ $prod->kuantitas_produksi }}</td>
                <td><span class="badge bg-success">{{ strtoupper($prod->status) }}</span></td>
                <td>{{ $prod->cabang->nama_cabang ?? '-' }}</td>
                <td>
                    <div class="action-buttons">
                        {{-- <a href="{{ route('manufacturing.production.show', $prod->id) }}" class="btn btn-sm btn-icon btn-light" title="Detail">
                            <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                        </a> --}}
                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperuser())
                            <a href="{{ route('manufacturing.production.edit', $prod->id) }}" class="btn btn-sm btn-icon btn-light" title="Edit">
                                <span data-feather="edit-2" style="width: 14px; height: 14px;"></span>
                            </a>
                            <form action="{{ route('manufacturing.production.destroy', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan produksi ini? Stok bahan baku dan barang jadi akan disesuaikan kembali.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-light text-danger" title="Hapus">
                                    <span data-feather="trash-2" style="width: 14px; height: 14px;"></span>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data produksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
