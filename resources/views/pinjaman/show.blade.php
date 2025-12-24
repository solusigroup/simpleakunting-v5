@extends('layouts.app')

@section('title', 'Detail Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Pinjaman</h1>
    <div class="btn-toolbar">
        @if($pinjaman->status == 'draft')
            <form method="POST" action="{{ route('pinjaman.submit', $pinjaman->id_pinjaman) }}" class="d-inline me-2">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('Submit pengajuan untuk approval?')">
                    <span data-feather="send"></span> Submit untuk Approval
                </button>
            </form>
        @endif
        @if($pinjaman->status == 'approved')
            <a href="{{ route('pinjaman.pencairan', $pinjaman->id_pinjaman) }}" class="btn btn-success me-2">
                <span data-feather="dollar-sign"></span> Proses Pencairan
            </a>
        @endif
        @if(in_array($pinjaman->status, ['active', 'disbursed']))
            <a href="{{ route('pinjaman.angsuran', $pinjaman->id_pinjaman) }}" class="btn btn-info me-2">
                <span data-feather="credit-card"></span> Bayar Angsuran
            </a>
            <a href="{{ route('pinjaman.pelunasan', $pinjaman->id_pinjaman) }}" class="btn btn-primary me-2">
                <span data-feather="check-circle"></span> Pelunasan
            </a>
        @endif
        <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary">
            <span data-feather="arrow-left"></span> Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Info Pinjaman -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <strong>Informasi Pinjaman</strong>
                @switch($pinjaman->status)
                    @case('draft')
                        <span class="badge bg-secondary fs-6">DRAFT</span>
                        @break
                    @case('pending_approval')
                        <span class="badge bg-warning fs-6">PENDING APPROVAL</span>
                        @break
                    @case('approved')
                        <span class="badge bg-info fs-6">APPROVED</span>
                        @break
                    @case('active')
                    @case('disbursed')
                        <span class="badge bg-success fs-6">AKTIF</span>
                        @break
                    @case('lunas')
                        <span class="badge bg-success fs-6">LUNAS</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger fs-6">DITOLAK</span>
                        @break
                @endswitch
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Pinjaman:</span>
                    <strong>{{ $pinjaman->no_pinjaman }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jenis Pinjaman:</span>
                    <strong>{{ $pinjaman->jenisPinjaman->nama_pinjaman }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tanggal Pengajuan:</span>
                    <strong>{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</strong>
                </li>
                @if($pinjaman->tanggal_persetujuan)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tanggal Persetujuan:</span>
                    <strong>{{ $pinjaman->tanggal_persetujuan->format('d/m/Y') }}</strong>
                </li>
                @endif
                @if($pinjaman->tanggal_pencairan)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tanggal Pencairan:</span>
                    <strong>{{ $pinjaman->tanggal_pencairan->format('d/m/Y') }}</strong>
                </li>
                @endif
                <li class="list-group-item d-flex justify-content-between">
                    <span>Pokok Pinjaman:</span>
                    <strong class="text-primary fs-5">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Bunga:</span>
                    <strong>{{ $pinjaman->bunga_pertahun }}% / tahun ({{ ucfirst($pinjaman->metode_bunga) }})</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tenor:</span>
                    <strong>{{ $pinjaman->tenor }} bulan</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Provisi:</span>
                    <strong>Rp {{ number_format($pinjaman->provisi, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Biaya Admin:</span>
                    <strong>Rp {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Bunga:</span>
                    <strong>Rp {{ number_format($pinjaman->total_bunga, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <span>Sisa Pokok:</span>
                    <strong class="text-danger fs-5">Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Kolektibilitas:</span>
                    @switch($pinjaman->kolektibilitas)
                        @case('1')
                            <span class="badge bg-success">1 - Lancar</span>
                            @break
                        @case('2')
                            <span class="badge bg-info">2 - Dalam Perhatian Khusus</span>
                            @break
                        @case('3')
                            <span class="badge bg-warning">3 - Kurang Lancar</span>
                            @break
                        @case('4')
                            <span class="badge bg-orange" style="background-color: #fd7e14;">4 - Diragukan</span>
                            @break
                        @case('5')
                            <span class="badge bg-danger">5 - Macet</span>
                            @break
                    @endswitch
                </li>
            </ul>
        </div>
    </div>

    <!-- Info Anggota -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Anggota</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Anggota:</span>
                    <strong>{{ $pinjaman->anggota->no_anggota }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Nama:</span>
                    <strong>{{ $pinjaman->anggota->nama_lengkap }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Telepon:</span>
                    <strong>{{ $pinjaman->anggota->telepon ?? '-' }}</strong>
                </li>
                <li class="list-group-item">
                    <a href="{{ route('anggota.show', $pinjaman->id_anggota) }}" class="btn btn-sm btn-outline-primary">
                        <span data-feather="user"></span> Lihat Profil
                    </a>
                </li>
            </ul>
        </div>

        <!-- Approval History -->
        @if($pinjaman->approvalHistory->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <strong>Riwayat Approval</strong>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($pinjaman->approvalHistory as $history)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-{{ $history->action_color }}">{{ $history->action_label }}</span>
                            <strong>{{ $history->user->name ?? 'System' }}</strong>
                        </div>
                        <small class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    @if($history->notes)
                        <small class="text-muted">{{ $history->notes }}</small>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Agunan -->
        @if($pinjaman->agunan->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <strong>Agunan/Jaminan</strong>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($pinjaman->agunan as $agunan)
                <li class="list-group-item">
                    <strong>{{ $agunan->nama_agunan }}</strong><br>
                    <small class="text-muted">
                        Jenis: {{ ucfirst($agunan->jenis_agunan) }} |
                        Nilai Taksasi: Rp {{ number_format($agunan->nilai_taksasi, 0, ',', '.') }}
                    </small>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<!-- Jadwal Angsuran -->
@if($pinjaman->jadwal->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <strong>Jadwal Angsuran</strong>
        <span class="badge bg-light text-dark">{{ $pinjaman->jadwal->count() }} angsuran</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Ke</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-end">Pokok</th>
                        <th class="text-end">Bunga</th>
                        <th class="text-end">Total Angsuran</th>
                        <th class="text-end">Sisa Pokok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pinjaman->jadwal as $j)
                    <tr class="{{ $j->is_overdue ? 'table-danger' : '' }}">
                        <td class="text-center">{{ $j->angsuran_ke }}</td>
                        <td>{{ $j->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($j->pokok, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($j->bunga, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($j->total_angsuran, 0, ',', '.') }}</strong></td>
                        <td class="text-end">Rp {{ number_format($j->sisa_pokok_setelah, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @switch($j->status)
                                @case('lunas')
                                    <span class="badge bg-success">Lunas</span>
                                    @break
                                @case('sebagian')
                                    <span class="badge bg-warning">Sebagian</span>
                                    @break
                                @default
                                    @if($j->is_overdue)
                                        <span class="badge bg-danger">Tertunggak {{ $j->days_overdue }} hari</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Bayar</span>
                                    @endif
                            @endswitch
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Riwayat Pembayaran -->
@if($pinjaman->angsuran->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <strong>Riwayat Pembayaran</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-success">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal Bayar</th>
                        <th class="text-end">Pokok</th>
                        <th class="text-end">Bunga</th>
                        <th class="text-end">Denda</th>
                        <th class="text-end">Total Bayar</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pinjaman->angsuran as $a)
                    <tr>
                        <td>{{ $a->no_transaksi }}</td>
                        <td>{{ $a->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="text-end">Rp {{ number_format($a->pokok_dibayar, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($a->bunga_dibayar, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($a->denda, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($a->total_bayar, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $a->jenis == 'pelunasan' ? 'primary' : 'info' }}">
                                {{ ucfirst($a->jenis) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
