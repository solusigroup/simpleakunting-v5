@extends('layouts.app')

@section('title', 'Jenis Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Jenis Pinjaman</h1>
    <a href="{{ route('jenis-pinjaman.create') }}" class="btn btn-primary">
        <span data-feather="plus"></span> Tambah Jenis
    </a>
</div>

@if($jenisPinjaman->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Bunga/Tahun</th>
                <th>Metode</th>
                <th>Tenor Max</th>
                <th>Akun Piutang</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jenisPinjaman as $jenis)
            <tr>
                <td>{{ $jenis->kode_pinjaman }}</td>
                <td>{{ $jenis->nama_pinjaman }}</td>
                <td>{{ ucfirst($jenis->kategori) }}</td>
                <td>{{ number_format($jenis->bunga_pertahun, 2) }}%</td>
                <td>{{ ucfirst($jenis->metode_bunga) }}</td>
                <td>{{ $jenis->tenor_max }} bln</td>
                <td>
                    @if($jenis->akun_piutang_pinjaman)
                        <span class="badge bg-{{ $jenis->akunPiutang ? 'success' : 'danger' }}">
                            {{ $jenis->akun_piutang_pinjaman }}
                            @if(!$jenis->akunPiutang)
                                (TIDAK VALID!)
                            @endif
                        </span>
                    @else
                        <span class="badge bg-warning">Belum diset</span>
                    @endif
                </td>
                <td>
                    @if($jenis->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('jenis-pinjaman.edit', $jenis->id_jenis_pinjaman) }}" class="btn btn-sm btn-warning">
                        <span data-feather="edit"></span>
                    </a>
                    <form action="{{ route('jenis-pinjaman.destroy', $jenis->id_jenis_pinjaman) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus jenis pinjaman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <span data-feather="trash-2"></span>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">Belum ada jenis pinjaman. <a href="{{ route('jenis-pinjaman.create') }}">Tambah sekarang</a></div>
@endif

@endsection

@push('scripts')
<script>feather.replace();</script>
@endpush
