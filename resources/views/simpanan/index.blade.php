@extends('layouts.app')

@section('title', 'Data Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaksi Simpanan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('simpanan.create') }}" class="btn btn-primary">
            <span data-feather="plus"></span> Transaksi Baru
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('simpanan.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Anggota</label>
                <select name="anggota" class="form-select">
                    <option value="">Semua Anggota</option>
                    @foreach($anggotaList as $a)
                        <option value="{{ $a->id_anggota }}" {{ request('anggota') == $a->id_anggota ? 'selected' : '' }}>
                            {{ $a->no_anggota }} - {{ $a->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Simpanan</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisSimpanan as $js)
                        <option value="{{ $js->id_jenis_simpanan }}" {{ request('jenis') == $js->id_jenis_simpanan ? 'selected' : '' }}>
                            {{ $js->nama_simpanan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <span data-feather="search"></span> Filter
                </button>
                <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Anggota</th>
                <th>Jenis Simpanan</th>
                <th>Transaksi</th>
                <th class="text-end">Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($simpanan as $s)
            <tr>
                <td><strong>{{ $s->no_transaksi }}</strong></td>
                <td>{{ $s->tanggal->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('anggota.show', $s->id_anggota) }}">
                        {{ $s->anggota->nama_lengkap }}
                    </a>
                </td>
                <td>{{ $s->jenisSimpanan->nama_simpanan }}</td>
                <td>
                    @if($s->jenis_transaksi == 'setor')
                        <span class="badge bg-success">Setor</span>
                    @else
                        <span class="badge bg-danger">Tarik</span>
                    @endif
                </td>
                <td class="text-end">Rp {{ number_format($s->jumlah, 0, ',', '.') }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('simpanan.show', $s->id_simpanan) }}" class="btn btn-info" title="Detail">
                            <span data-feather="eye"></span>
                        </a>
                        <form action="{{ route('simpanan.destroy', $s->id_simpanan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus transaksi ini?')">
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
                <td colspan="7" class="text-center text-muted py-4">Belum ada data transaksi simpanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Menampilkan {{ $simpanan->firstItem() ?? 0 }} - {{ $simpanan->lastItem() ?? 0 }} dari {{ $simpanan->total() }} transaksi
    </div>
    {{ $simpanan->appends(request()->query())->links() }}
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
