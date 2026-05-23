@extends('layouts.app')

@section('title', 'Daftar Penawaran Penjualan - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Penawaran Penjualan (Quotation)</h1>
            <p class="page-subtitle">Daftar penawaran harga kepada pelanggan</p>
        </div>
        <div>
            <a href="{{ route('penawaran.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Buat Penawaran Baru
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
                        <th>No Penawaran</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penawaran as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_penawaran)->format('d/m/Y') }}</td>
                            <td><strong>{{ $p->no_penawaran }}</strong></td>
                            <td>{{ $p->pelanggan->nama_pelanggan ?? '-' }}</td>
                            <td style="font-weight: 600;">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td>
                                @switch($p->status)
                                    @case('Dikonversi')
                                        <span class="badge badge-success">Dikonversi</span>
                                        @break
                                    @case('Diterima')
                                        <span class="badge badge-primary">Diterima</span>
                                        @break
                                    @case('Ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                        @break
                                    @case('Dikirim')
                                        <span class="badge badge-info">Dikirim</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $p->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('penawaran.show', $p->id_penawaran) }}" class="btn btn-sm btn-icon btn-light" title="Detail">
                                        <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                    </a>
                                    @if($p->status !== 'Dikonversi')
                                        <a href="{{ route('penawaran.edit', $p->id_penawaran) }}" class="btn btn-sm btn-icon btn-light" title="Edit">
                                            <span data-feather="edit-2" style="width: 14px; height: 14px;"></span>
                                        </a>
                                        <form action="{{ route('penawaran.destroy', $p->id_penawaran) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penawaran ini?')">
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
                            <td colspan="6">
                                <div class="table-empty">
                                    <div class="table-empty-icon">📝</div>
                                    <p>Belum ada transaksi penawaran penjualan.</p>
                                    <a href="{{ route('penawaran.create') }}" class="btn btn-primary btn-sm">Buat Penawaran Pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
