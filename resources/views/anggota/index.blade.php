@extends('layouts.app')

@section('title', 'Data Anggota - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Data Anggota</h1>
            <p class="page-subtitle">Kelola data anggota koperasi</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span data-feather="upload-cloud" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                    Import/Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('import-export.export', 'anggota') }}"><span data-feather="download"></span> Export CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.template', 'anggota') }}"><span data-feather="file"></span> Download Template</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.index') }}"><span data-feather="upload"></span> Import Data</a></li>
                </ul>
            </div>
            <a href="{{ route('anggota.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Tambah Anggota
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="form-card mb-4">
        <div class="form-card-body">
            <form method="GET" action="{{ route('anggota.index') }}" class="form-row" style="margin-bottom: 0;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="No. Anggota, Nama, NIK..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non_aktif" {{ request('status') == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div class="form-group d-flex align-items-end gap-2" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span data-feather="search" style="width: 14px; height: 14px; margin-right: 4px;"></span>
                        Cari
                    </button>
                    <a href="{{ route('anggota.index') }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
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
                                <span class="badge badge-success">Aktif</span>
                            @elseif($a->status == 'non_aktif')
                                <span class="badge badge-warning">Non-Aktif</span>
                            @else
                                <span class="badge badge-danger">Keluar</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('anggota.show', $a->id_anggota) }}" class="btn btn-sm btn-primary" title="Detail">
                                    <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                </a>
                                <a href="{{ route('anggota.kartu', $a->id_anggota) }}" class="btn btn-sm btn-success" title="Kartu">
                                    <span data-feather="credit-card" style="width: 14px; height: 14px;"></span>
                                </a>
                                <a href="{{ route('anggota.edit', $a->id_anggota) }}" class="btn btn-sm btn-outline" title="Edit">
                                    <span data-feather="edit" style="width: 14px; height: 14px;"></span>
                                </a>
                                <form action="{{ route('anggota.destroy', $a->id_anggota) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus anggota ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <span data-feather="trash-2" style="width: 14px; height: 14px;"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="table-empty">
                                <div class="table-empty-icon">👥</div>
                                <p>Belum ada data anggota</p>
                                <a href="{{ route('anggota.create') }}" class="btn btn-primary btn-sm">Tambah Anggota Pertama</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anggota->hasPages())
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="text-muted" style="font-size: var(--font-size-sm);">
                    Menampilkan {{ $anggota->firstItem() ?? 0 }} - {{ $anggota->lastItem() ?? 0 }} dari {{ $anggota->total() }} anggota
                </div>
                {{ $anggota->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
