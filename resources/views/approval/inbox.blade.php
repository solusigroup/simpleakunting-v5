@extends('layouts.app')

@section('title', 'Inbox Approval - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Inbox Approval</h1>
</div>

<!-- Pinjaman Pending -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <strong><span data-feather="clock"></span> Pinjaman Menunggu Persetujuan</strong>
        <span class="badge bg-dark">{{ $pendingPinjaman->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Tanggal</th>
                        <th>Anggota</th>
                        <th>Jenis</th>
                        <th class="text-end">Jumlah</th>
                        <th>Tenor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPinjaman as $p)
                    <tr>
                        <td>
                            <strong>{{ $p->no_pinjaman }}</strong>
                        </td>
                        <td>{{ $p->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td>
                            {{ $p->anggota->nama_lengkap }}<br>
                            <small class="text-muted">{{ $p->anggota->no_anggota }}</small>
                        </td>
                        <td>{{ $p->jenisPinjaman->nama_pinjaman }}</td>
                        <td class="text-end">
                            <strong>Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</strong><br>
                            <small class="text-muted">{{ $p->bunga_pertahun }}% {{ ucfirst($p->metode_bunga) }}</small>
                        </td>
                        <td>{{ $p->tenor }} bln</td>
                        <td>
                            <a href="{{ route('pinjaman.show', $p->id_pinjaman) }}" class="btn btn-sm btn-info mb-1" title="Detail">
                                <span data-feather="eye"></span> Detail
                            </a>
                            <button type="button" class="btn btn-sm btn-success mb-1" 
                                    data-bs-toggle="modal" data-bs-target="#approveModal{{ $p->id_pinjaman }}">
                                <span data-feather="check"></span> Approve
                            </button>
                            <button type="button" class="btn btn-sm btn-danger mb-1" 
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id_pinjaman }}">
                                <span data-feather="x"></span> Reject
                            </button>
                        </td>
                    </tr>

                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal{{ $p->id_pinjaman }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('approval.approve', ['module' => 'pinjaman', 'id' => $p->id_pinjaman]) }}">
                                    @csrf
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Approve Pinjaman</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Setujui pinjaman <strong>{{ $p->no_pinjaman }}</strong>?</p>
                                        <p>
                                            <strong>{{ $p->anggota->nama_lengkap }}</strong><br>
                                            Jumlah: Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}<br>
                                            Tenor: {{ $p->tenor }} bulan
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label">Catatan (opsional)</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan persetujuan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">
                                            <span data-feather="check-circle"></span> Setuju
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $p->id_pinjaman }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('approval.reject', ['module' => 'pinjaman', 'id' => $p->id_pinjaman]) }}">
                                    @csrf
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Tolak Pinjaman</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tolak pinjaman <strong>{{ $p->no_pinjaman }}</strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">
                                            <span data-feather="x-circle"></span> Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <span data-feather="check-circle" class="text-success"></span>
                            Tidak ada pinjaman yang menunggu persetujuan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
