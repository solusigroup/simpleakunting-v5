@extends('layouts.app')

@section('title', 'Jenis Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Jenis Simpanan</h1>
    <a href="{{ route('jenis-simpanan.create') }}" class="btn btn-primary">
        <span data-feather="plus"></span> Tambah Jenis
    </a>
</div>

@if($jenisSimpanan->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Bunga/Tahun</th>
                <th>Akun Simpanan</th>
                <th>Akun Bunga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jenisSimpanan as $jenis)
            <tr>
                <td>{{ $jenis->kode_simpanan }}</td>
                <td>{{ $jenis->nama_simpanan }}</td>
                <td>
                    <span class="badge bg-{{ $jenis->tipe == 'pokok' ? 'primary' : ($jenis->tipe == 'wajib' ? 'info' : ($jenis->tipe == 'sukarela' ? 'success' : 'warning')) }}">
                        {{ ucfirst($jenis->tipe) }}
                    </span>
                </td>
                <td>{{ number_format($jenis->bunga_pertahun ?? 0, 2) }}%</td>
                <td>
                    @if($jenis->akun_simpanan)
                        <span class="badge bg-{{ $jenis->akunSimpanan ? 'success' : 'danger' }}">
                            {{ $jenis->akun_simpanan }}
                            @if(!$jenis->akunSimpanan)
                                (TIDAK VALID!)
                            @endif
                        </span>
                    @else
                        <span class="badge bg-warning">Belum diset</span>
                    @endif
                </td>
                <td>
                    @if($jenis->akun_bunga)
                        <span class="badge bg-{{ $jenis->akunBunga ? 'success' : 'danger' }}">
                            {{ $jenis->akun_bunga }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
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
                    <a href="{{ route('jenis-simpanan.edit', $jenis->id_jenis_simpanan) }}" class="btn btn-sm btn-warning">
                        <span data-feather="edit"></span>
                    </a>
                    <form action="{{ route('jenis-simpanan.destroy', $jenis->id_jenis_simpanan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus jenis simpanan ini?')">
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
<div class="alert alert-info">Belum ada jenis simpanan. <a href="{{ route('jenis-simpanan.create') }}">Tambah sekarang</a></div>
@endif

@endsection

@push('scripts')
<script>feather.replace();</script>
@endpush
