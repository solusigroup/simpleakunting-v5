@extends('layouts.app')

@section('title', 'Unit Usaha - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Unit Usaha</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('unit-usaha.create') }}" class="btn btn-sm btn-primary">
                + Tambah Unit Usaha
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Kode Unit</th>
                    <th>Nama Unit</th>
                    <th>Cabang</th>
                    <th>Jenis Usaha</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                <tr>
                    <td><strong>{{ $unit->kode_unit }}</strong></td>
                    <td>{{ $unit->nama_unit }}</td>
                    <td>{{ $unit->cabang->nama_cabang ?? '-' }}</td>
                    <td>{{ ucfirst($unit->jenis_usaha ?? '-') }}</td>
                    <td>
                        @if($unit->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('unit-usaha.edit', $unit->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('unit-usaha.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit usaha ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada unit usaha</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $units->links() }}
@endsection
