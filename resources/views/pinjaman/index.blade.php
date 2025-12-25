@extends('layouts.app')

@section('title', 'Data Pinjaman - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Data Pinjaman</h1>
            <p class="page-subtitle">Kelola pinjaman anggota koperasi</p>
        </div>
        <div>
            <a href="{{ route('pinjaman.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Pengajuan Baru
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="form-card mb-4">
        <div class="form-card-body">
            <form method="GET" action="{{ route('pinjaman.index') }}" class="form-row" style="margin-bottom: 0;">
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Kolektibilitas</label>
                    <select name="kolektibilitas" class="form-select">
                        <option value="">Semua</option>
                        <option value="1" {{ request('kolektibilitas') == '1' ? 'selected' : '' }}>1 - Lancar</option>
                        <option value="2" {{ request('kolektibilitas') == '2' ? 'selected' : '' }}>2 - DPK</option>
                        <option value="3" {{ request('kolektibilitas') == '3' ? 'selected' : '' }}>3 - Kurang Lancar</option>
                        <option value="4" {{ request('kolektibilitas') == '4' ? 'selected' : '' }}>4 - Diragukan</option>
                        <option value="5" {{ request('kolektibilitas') == '5' ? 'selected' : '' }}>5 - Macet</option>
                    </select>
                </div>
                <div class="form-group d-flex align-items-end gap-2" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span data-feather="search" style="width: 14px; height: 14px; margin-right: 4px;"></span>
                        Filter
                    </button>
                    <a href="{{ route('pinjaman.index') }}" class="btn btn-outline btn-sm">Reset</a>
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
                        <th>No. Pinjaman</th>
                        <th>Anggota</th>
                        <th>Jenis</th>
                        <th style="text-align: right;">Pokok</th>
                        <th style="text-align: right;">Sisa Pokok</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th>Kol</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pinjaman as $p)
                    <tr>
                        <td><strong>{{ $p->no_pinjaman }}</strong></td>
                        <td>
                            <a href="{{ route('anggota.show', $p->id_anggota) }}" style="color: var(--color-primary); text-decoration: none;">
                                {{ $p->anggota->nama_lengkap }}
                            </a>
                        </td>
                        <td>{{ $p->jenisPinjaman->nama_pinjaman }}</td>
                        <td style="text-align: right;">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td style="text-align: right;">
                            <strong style="color: {{ $p->sisa_pokok > 0 ? 'var(--color-danger)' : 'var(--color-success)' }};">
                                Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>{{ $p->tenor }} bln</td>
                        <td>
                            @switch($p->status)
                                @case('draft')
                                    <span class="badge badge-secondary">Draft</span>
                                    @break
                                @case('pending_approval')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                @case('approved')
                                    <span class="badge badge-info">Approved</span>
                                    @break
                                @case('active')
                                @case('disbursed')
                                    <span class="badge badge-primary">Aktif</span>
                                    @break
                                @case('lunas')
                                    <span class="badge badge-success">Lunas</span>
                                    @break
                                @case('rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $p->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @switch($p->kolektibilitas)
                                @case('1')
                                    <span class="badge badge-success">1</span>
                                    @break
                                @case('2')
                                    <span class="badge badge-info">2</span>
                                    @break
                                @case('3')
                                    <span class="badge badge-warning">3</span>
                                    @break
                                @case('4')
                                    <span class="badge" style="background: rgba(253, 126, 20, 0.1); color: #fd7e14;">4</span>
                                    @break
                                @case('5')
                                    <span class="badge badge-danger">5</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('pinjaman.show', $p->id_pinjaman) }}" class="btn btn-sm btn-primary" title="Detail">
                                    <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                </a>
                                @if($p->status == 'approved')
                                    <a href="{{ route('pinjaman.pencairan', $p->id_pinjaman) }}" class="btn btn-sm btn-success" title="Cairkan">
                                        <span data-feather="dollar-sign" style="width: 14px; height: 14px;"></span>
                                    </a>
                                @endif
                                @if(in_array($p->status, ['active', 'disbursed']))
                                    <a href="{{ route('pinjaman.angsuran', $p->id_pinjaman) }}" class="btn btn-sm btn-outline" title="Bayar Angsuran" style="border-color: var(--color-warning); color: var(--color-warning);">
                                        <span data-feather="credit-card" style="width: 14px; height: 14px;"></span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="table-empty">
                                <div class="table-empty-icon">💳</div>
                                <p>Belum ada data pinjaman</p>
                                <a href="{{ route('pinjaman.create') }}" class="btn btn-primary btn-sm">Ajukan Pinjaman</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pinjaman->hasPages())
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="text-muted" style="font-size: var(--font-size-sm);">
                    Menampilkan {{ $pinjaman->firstItem() ?? 0 }} - {{ $pinjaman->lastItem() ?? 0 }} dari {{ $pinjaman->total() }} pinjaman
                </div>
                {{ $pinjaman->appends(request()->query())->links() }}
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
