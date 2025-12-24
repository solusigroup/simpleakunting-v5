@extends('layouts.app')

@section('title', 'Data Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Pinjaman</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('pinjaman.create') }}" class="btn btn-primary">
            <span data-feather="plus"></span> Pengajuan Baru
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('pinjaman.index') }}" class="row g-3">
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
            <div class="col-md-2">
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
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <span data-feather="search"></span> Filter
                </button>
                <a href="{{ route('pinjaman.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>No. Pinjaman</th>
                <th>Anggota</th>
                <th>Jenis</th>
                <th class="text-end">Pokok</th>
                <th class="text-end">Sisa Pokok</th>
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
                    <a href="{{ route('anggota.show', $p->id_anggota) }}">
                        {{ $p->anggota->nama_lengkap }}
                    </a>
                </td>
                <td>{{ $p->jenisPinjaman->nama_pinjaman }}</td>
                <td class="text-end">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                <td class="text-end">
                    <strong class="{{ $p->sisa_pokok > 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}
                    </strong>
                </td>
                <td>{{ $p->tenor }} bln</td>
                <td>
                    @switch($p->status)
                        @case('draft')
                            <span class="badge bg-secondary">Draft</span>
                            @break
                        @case('pending_approval')
                            <span class="badge bg-warning">Pending</span>
                            @break
                        @case('approved')
                            <span class="badge bg-info">Approved</span>
                            @break
                        @case('active')
                        @case('disbursed')
                            <span class="badge bg-primary">Aktif</span>
                            @break
                        @case('lunas')
                            <span class="badge bg-success">Lunas</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Ditolak</span>
                            @break
                        @default
                            <span class="badge bg-dark">{{ $p->status }}</span>
                    @endswitch
                </td>
                <td>
                    @switch($p->kolektibilitas)
                        @case('1')
                            <span class="badge bg-success">1</span>
                            @break
                        @case('2')
                            <span class="badge bg-info">2</span>
                            @break
                        @case('3')
                            <span class="badge bg-warning">3</span>
                            @break
                        @case('4')
                            <span class="badge bg-orange text-dark" style="background-color: #fd7e14;">4</span>
                            @break
                        @case('5')
                            <span class="badge bg-danger">5</span>
                            @break
                    @endswitch
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('pinjaman.show', $p->id_pinjaman) }}" class="btn btn-info" title="Detail">
                            <span data-feather="eye"></span>
                        </a>
                        @if($p->status == 'approved')
                            <a href="{{ route('pinjaman.pencairan', $p->id_pinjaman) }}" class="btn btn-success" title="Cairkan">
                                <span data-feather="dollar-sign"></span>
                            </a>
                        @endif
                        @if(in_array($p->status, ['active', 'disbursed']))
                            <a href="{{ route('pinjaman.angsuran', $p->id_pinjaman) }}" class="btn btn-warning" title="Bayar Angsuran">
                                <span data-feather="credit-card"></span>
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Belum ada data pinjaman</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Menampilkan {{ $pinjaman->firstItem() ?? 0 }} - {{ $pinjaman->lastItem() ?? 0 }} dari {{ $pinjaman->total() }} pinjaman
    </div>
    {{ $pinjaman->appends(request()->query())->links() }}
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
