@extends('layouts.app')

@section('title', 'Data Pelanggan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Pelanggan</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span data-feather="upload-cloud"></span> Import/Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('import-export.export', 'pelanggan') }}"><span data-feather="download"></span> Export CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.template', 'pelanggan') }}"><span data-feather="file"></span> Download Template</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.index') }}"><span data-feather="upload"></span> Import Data</a></li>
                </ul>
            </div>
            <a href="{{ route('pelanggan.create') }}" class="btn btn-sm btn-primary">
                Tambah Pelanggan
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama Pelanggan</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Telepon</th>
                    <th scope="col">Saldo Piutang</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelanggan as $p)
                    <tr>
                        <td>{{ $p->id_pelanggan }}</td>
                        <td>{{ $p->nama_pelanggan }}</td>
                        <td>{{ Str::limit($p->alamat, 30) }}</td>
                        <td>{{ $p->telepon }}</td>
                        <td>Rp {{ number_format($p->saldo_terkini_piutang, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('pelanggan.edit', $p->id_pelanggan) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data pelanggan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
