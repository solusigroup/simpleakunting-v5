@extends('layouts.app')

@section('title', 'Daftar Penjualan - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Penjualan</h1>
            <p class="page-subtitle">Daftar semua transaksi penjualan</p>
        </div>
        <div>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Buat Faktur Baru
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No Faktur</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penjualan as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_faktur)->format('d/m/Y') }}</td>
                            <td><strong>{{ $p->no_faktur }}</strong></td>
                            <td>{{ $p->pelanggan->nama_pelanggan ?? '-' }}</td>
                            <td style="font-weight: 600;">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $p->metode_pembayaran }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $p->status_pembayaran == 'Lunas' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $p->status_pembayaran }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('penjualan.show', $p->id_penjualan) }}" class="btn btn-sm btn-icon btn-light" title="Detail">
                                        <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperuser())
                                        <a href="{{ route('penjualan.edit', $p->id_penjualan) }}" class="btn btn-sm btn-icon btn-light" title="Edit">
                                            <span data-feather="edit-2" style="width: 14px; height: 14px;"></span>
                                        </a>
                                        <form action="{{ route('penjualan.destroy', $p->id_penjualan) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok dan jurnal akan disesuaikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-light text-danger" title="Hapus">
                                                <span data-feather="trash-2" style="width: 14px; height: 14px;"></span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="table-empty">
                                    <div class="table-empty-icon">🛒</div>
                                    <p>Belum ada transaksi penjualan.</p>
                                    <a href="{{ route('penjualan.create') }}" class="btn btn-primary btn-sm">Buat Faktur Pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
