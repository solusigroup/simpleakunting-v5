@extends('layouts.app')

@section('title', 'Bill of Materials (BOM)')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bill of Materials (BOM)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('manufacturing.bom.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> Buat BOM Baru
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
                <th>Kode BOM</th>
                <th>Nama BOM</th>
                <th>Barang Jadi</th>
                <th>Output Qty</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boms as $bom)
            <tr>
                <td>{{ $bom->kode_bom }}</td>
                <td>{{ $bom->nama_bom }}</td>
                <td>{{ $bom->barangJadi->nama_barang ?? '-' }}</td>
                <td>{{ $bom->kuantitas_hasil }}</td>
                <td>{{ $bom->deskripsi }}</td>
                <td>
                    <a href="{{ route('manufacturing.bom.edit', $bom->id) }}" class="btn btn-xs btn-outline-primary">Edit</a>
                    <form action="{{ route('manufacturing.bom.destroy', $bom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus BOM ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data BOM.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
