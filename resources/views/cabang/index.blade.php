@extends('layouts.app')

@section('title', 'Daftar Cabang')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Cabang</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('cabang.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> Tambah Cabang
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
                <th>Kode</th>
                <th>Nama Cabang</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cabang as $c)
            <tr>
                <td>{{ $c->kode_cabang }}</td>
                <td>{{ $c->nama_cabang }}</td>
                <td>{{ $c->alamat }}</td>
                <td>{{ $c->telepon }}</td>
                <td>
                    <a href="{{ route('cabang.edit', $c->id) }}" class="btn btn-xs btn-outline-secondary">Edit</a>
                    <form action="{{ route('cabang.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus cabang ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data cabang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
