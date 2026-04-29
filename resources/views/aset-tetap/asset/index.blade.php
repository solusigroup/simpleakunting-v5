@extends('layouts.app')

@section('title', 'Data Aset Tetap - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Aset Tetap</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <form action="{{ route('aset-tetap.depreciate') }}" method="POST" class="d-inline" onsubmit="return confirm('Jalankan proses penyusutan (depresiasi) untuk semua aset aktif di bulan ini? Proses ini akan membuat jurnal otomatis.');">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <span data-feather="play-circle"></span> Jalankan Depresiasi Bulan Ini
                </button>
            </form>
            <a href="{{ route('aset-tetap-group.index') }}" class="btn btn-sm btn-outline-secondary">
                Kelompok Aset
            </a>
            <a href="{{ route('aset-tetap.create') }}" class="btn btn-sm btn-primary">
                Tambah Aset Tetap
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Kode Aset</th>
                    <th scope="col">Nama Aset</th>
                    <th scope="col">Kelompok</th>
                    <th scope="col">Tgl Perolehan</th>
                    <th scope="col" class="text-end">Harga Perolehan</th>
                    <th scope="col" class="text-end">Nilai Buku</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $a)
                    <tr>
                        <td>{{ $a->kode_aset }}</td>
                        <td>{{ $a->nama_aset }}</td>
                        <td>{{ $a->group->nama_kelompok ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->tanggal_perolehan)->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($a->harga_perolehan, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($a->nilai_buku_saat_ini, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $a->status == 'Aktif' ? 'success' : ($a->status == 'Dijual' ? 'warning' : 'danger') }}">
                                {{ $a->status }}
                            </span>
                        </td>
                        <td>
                            @if($a->status === 'Aktif')
                                <a href="{{ route('aset-tetap.dispose.create', $a->id) }}" class="btn btn-sm btn-outline-danger" title="Pelepasan / Penjualan Aset">Lepas</a>
                            @endif
                            <a href="{{ route('aset-tetap.edit', $a->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('aset-tetap.destroy', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data aset tetap.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
