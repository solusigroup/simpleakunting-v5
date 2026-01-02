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
                <th>Revaluasi</th>
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
                    <button type="button" class="btn btn-xs btn-outline-info" data-bs-toggle="modal" data-bs-target="#revaluationModal{{ $asset->id }}">
                        Revaluasi
                    </button>

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
