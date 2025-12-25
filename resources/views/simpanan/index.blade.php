@extends('layouts.app')

@section('title', 'Data Simpanan - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Transaksi Simpanan</h1>
            <p class="page-subtitle">Kelola transaksi simpanan anggota</p>
        </div>
        <div>
            <a href="{{ route('simpanan.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="form-card mb-4">
        <div class="form-card-body">
            <form method="GET" action="{{ route('simpanan.index') }}" class="form-row" style="margin-bottom: 0;">
                <div class="form-group" style="margin-bottom: 0;">
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
                <div class="form-group" style="margin-bottom: 0;">
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
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div class="form-group d-flex align-items-end gap-2" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span data-feather="search" style="width: 14px; height: 14px; margin-right: 4px;"></span>
                        Filter
                    </button>
                    <a href="{{ route('simpanan.index') }}" class="btn btn-outline btn-sm">Reset</a>
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
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th>Transaksi</th>
                        <th style="text-align: right;">Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simpanan as $s)
                    <tr>
                        <td><strong>{{ $s->no_transaksi }}</strong></td>
                        <td>{{ $s->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('anggota.show', $s->id_anggota) }}" style="color: var(--color-primary); text-decoration: none;">
                                {{ $s->anggota->nama_lengkap }}
                            </a>
                        </td>
                        <td>{{ $s->jenisSimpanan->nama_simpanan }}</td>
                        <td>
                            @if($s->jenis_transaksi == 'setor')
                                <span class="badge badge-success">Setor</span>
                            @else
                                <span class="badge badge-danger">Tarik</span>
                            @endif
                        </td>
                        <td style="text-align: right; font-weight: 600; color: {{ $s->jenis_transaksi == 'setor' ? 'var(--color-success)' : 'var(--color-danger)' }};">
                            {{ $s->jenis_transaksi == 'setor' ? '+' : '-' }} Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('simpanan.show', $s->id_simpanan) }}" class="btn btn-sm btn-primary" title="Detail">
                                    <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                </a>
                                <form action="{{ route('simpanan.destroy', $s->id_simpanan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus transaksi ini?')">
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
                                <div class="table-empty-icon">💰</div>
                                <p>Belum ada data transaksi simpanan</p>
                                <a href="{{ route('simpanan.create') }}" class="btn btn-primary btn-sm">Tambah Transaksi</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($simpanan->hasPages())
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="text-muted" style="font-size: var(--font-size-sm);">
                    Menampilkan {{ $simpanan->firstItem() ?? 0 }} - {{ $simpanan->lastItem() ?? 0 }} dari {{ $simpanan->total() }} transaksi
                </div>
                {{ $simpanan->appends(request()->query())->links() }}
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
