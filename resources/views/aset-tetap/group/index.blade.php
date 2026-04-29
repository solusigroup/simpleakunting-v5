@extends('layouts.app')

@section('title', 'Kelompok Aset Tetap - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kelompok Aset Tetap</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <a href="{{ route('aset-tetap-group.create') }}" class="btn btn-sm btn-primary">
                Tambah Kelompok Aset
            </a>
            <a href="{{ route('aset-tetap.index') }}" class="btn btn-sm btn-secondary">
                Data Aset Tetap
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
                    <th scope="col">Nama Kelompok</th>
                    <th scope="col">Umur Ekonomis</th>
                    <th scope="col">Metode</th>
                    <th scope="col">Akun Aset</th>
                    <th scope="col">Akun Akum. Susut</th>
                    <th scope="col">Akun Beban Susut</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groups as $g)
                    <tr>
                        <td>{{ $g->nama_kelompok }}</td>
                        <td>{{ $g->umur_ekonomis }} Bulan ({{ number_format($g->umur_ekonomis/12, 1) }} Thn)</td>
                        <td>{{ $g->metode_penyusutan }}</td>
                        <td>{{ $g->akun_aset ?? '-' }}</td>
                        <td>{{ $g->akun_akumulasi_penyusutan ?? '-' }}</td>
                        <td>{{ $g->akun_beban_penyusutan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('aset-tetap-group.edit', $g->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('aset-tetap-group.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelompok ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data kelompok aset tetap.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
