@extends('layouts.app')

@section('title', 'Daftar Pembelian - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Pembelian</h1>
            <p class="page-subtitle">Daftar semua transaksi pembelian</p>
        </div>
        <div>
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary btn-sm">
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
                        <th>Pemasok</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembelian as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_faktur)->format('d/m/Y') }}</td>
                            <td><strong>{{ $p->no_faktur }}</strong></td>
                            <td>{{ $p->pemasok->nama_pemasok ?? '-' }}</td>
                            <td style="font-weight: 600; color: var(--color-danger);">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
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
                                    <a href="{{ route('pembelian.show', $p->id_pembelian) }}" class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="table-empty">
                                    <div class="table-empty-icon">🛍️</div>
                                    <p>Belum ada transaksi pembelian.</p>
                                    <a href="{{ route('pembelian.create') }}" class="btn btn-primary btn-sm">Buat Faktur Pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
