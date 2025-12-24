@extends('layouts.app')

@section('title', 'Data Anggota - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Anggota Koperasi</h1>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <span data-feather="upload-cloud"></span> Import/Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('import-export.export', 'anggota') }}"><span data-feather="download"></span> Export CSV</a></li>
                <li><a class="dropdown-item" href="{{ route('import-export.template', 'anggota') }}"><span data-feather="file"></span> Download Template</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('import-export.index') }}"><span data-feather="upload"></span> Import Data</a></li>
            </ul>
        </div>
        <a href="{{ route('anggota.create') }}" class="btn btn-primary">
            <span data-feather="plus"></span> Tambah Anggota
        </a>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('anggota.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="No. Anggota, Nama, NIK..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non_aktif" {{ request('status') == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <span data-feather="search"></span> Cari
                </button>
                <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>No. Anggota</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th>Telepon</th>
                <th>Tgl Daftar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anggota as $a)
            <tr>
                <td><strong>{{ $a->no_anggota }}</strong></td>
                <td>{{ $a->nama_lengkap }}</td>
                <td>{{ $a->nik }}</td>
                <td>{{ $a->telepon ?? '-' }}</td>
                <td>{{ $a->tanggal_daftar->format('d/m/Y') }}</td>
                <td>
                    @if($a->status == 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @elseif($a->status == 'non_aktif')
                        <span class="badge bg-warning">Non-Aktif</span>
                    @else
                        <span class="badge bg-danger">Keluar</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('anggota.show', $a->id_anggota) }}" class="btn btn-info" title="Detail">
                            <span data-feather="eye"></span>
                        </a>
                        <a href="{{ route('anggota.kartu', $a->id_anggota) }}" class="btn btn-success" title="Kartu Anggota">
                            <span data-feather="credit-card"></span>
                        </a>
                        <a href="{{ route('anggota.edit', $a->id_anggota) }}" class="btn btn-warning" title="Edit">
                            <span data-feather="edit"></span>
                        </a>
                        <form action="{{ route('anggota.destroy', $a->id_anggota) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus anggota ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                <span data-feather="trash-2"></span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Belum ada data anggota</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Menampilkan {{ $anggota->firstItem() ?? 0 }} - {{ $anggota->lastItem() ?? 0 }} dari {{ $anggota->total() }} anggota
    </div>
    {{ $anggota->appends(request()->query())->links() }}
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
