@extends('layouts.app')

@section('title', 'Data Pemasok - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Pemasok</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span data-feather="upload-cloud"></span> Import/Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('import-export.export', 'pemasok') }}"><span data-feather="download"></span> Export CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.template', 'pemasok') }}"><span data-feather="file"></span> Download Template</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.index') }}"><span data-feather="upload"></span> Import Data</a></li>
                </ul>
            </div>
            <a href="{{ route('pemasok.create') }}" class="btn btn-sm btn-primary">
                Tambah Pemasok
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama Pemasok</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Telepon</th>
                    <th scope="col">Saldo Hutang</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemasok as $p)
                    <tr>
                        <td>{{ $p->id_pemasok }}</td>
                        <td>{{ $p->nama_pemasok }}</td>
                        <td>{{ Str::limit($p->alamat, 30) }}</td>
                        <td>{{ $p->telepon }}</td>
                        <td>Rp {{ number_format($p->saldo_terkini_hutang, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('pemasok.edit', $p->id_pemasok) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('pemasok.destroy', $p->id_pemasok) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data pemasok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
