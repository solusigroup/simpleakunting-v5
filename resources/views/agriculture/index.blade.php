@extends('layouts.app')

@section('title', 'Daftar Aset Biologis')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Aset Biologis (PSAK 69)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('agriculture.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> Tambah Aset
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Aset</th>
                <th>Jenis</th>
                <th>Umur (Bulan)</th>
                <th>Lokasi</th>
                <th>Nilai Perolehan</th>
                <th>Nilai Wajar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>{{ $asset->kode_aset }}</td>
                <td>{{ $asset->nama_aset }}</td>
                <td>{{ ucfirst($asset->jenis) }}</td>
                <td>{{ $asset->umur_bulan }}</td>
                <td>{{ $asset->lokasi }}</td>
                <td>Rp {{ number_format($asset->nilai_perolehan, 2) }}</td>
                <td>Rp {{ number_format($asset->nilai_wajar, 2) }}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#revaluationModal{{ $asset->id }}" title="Revaluasi">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                        <a href="{{ route('agriculture.edit', $asset->id) }}" class="btn btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $asset->id }}" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <!-- Modal Revaluasi -->
                    <div class="modal fade" id="revaluationModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('agriculture.revaluation', $asset->id) }}" method="POST">
                                @csrf
                                <div class="modal-content bg-white text-dark">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark">Revaluasi: {{ $asset->nama_aset }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label text-dark">Tanggal Revaluasi</label>
                                            <input type="date" name="tanggal_revaluasi" class="form-control bg-light text-dark" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark">Nilai Wajar Sekarang</label>
                                            <input type="text" class="form-control bg-light text-dark" value="Rp {{ number_format($asset->nilai_wajar, 2) }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark">Nilai Wajar Baru</label>
                                            <input type="number" step="0.01" name="nilai_wajar_baru" class="form-control bg-light text-dark" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark">Keterangan</label>
                                            <textarea name="keterangan" class="form-control bg-light text-dark" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Revaluasi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="deleteModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content bg-white text-dark">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah Anda yakin ingin menghapus aset <strong>{{ $asset->nama_aset }}</strong> ({{ $asset->kode_aset }})?</p>
                                    <p class="text-danger"><small><i class="bi bi-exclamation-triangle"></i> Semua log revaluasi dan jurnal terkait akan ikut dihapus.</small></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('agriculture.destroy', $asset->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada aset biologis.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
