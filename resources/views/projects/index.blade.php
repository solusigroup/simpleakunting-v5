@extends('layouts.app')

@section('title', 'Proyek & Program - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Proyek / Program</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">
                + Tambah Proyek/Program
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
                    <th>Kode Proyek</th>
                    <th>Nama Proyek</th>
                    <th>Unit Usaha (Cabang)</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                <tr>
                    <td><strong>{{ $p->kode_project }}</strong></td>
                    <td>{{ $p->nama_project }}</td>
                    <td>{{ $p->unitUsaha->nama_unit ?? '-' }} ({{ $p->unitUsaha->cabang->nama_cabang ?? '-' }})</td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                    <td>
                        @if($p->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Selesai</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('projects.edit', $p->id_project) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('projects.destroy', $p->id_project) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus proyek ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada proyek/program</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $projects->links() }}
@endsection
