@extends('layouts.app')

@section('title', 'Daftar RFQ Pembelian - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Request for Quotation (RFQ) Pembelian</h1>
            <p class="page-subtitle">Daftar permintaan penawaran harga kepada pemasok</p>
        </div>
        <div>
            <a href="{{ route('rfq.create') }}" class="btn btn-primary btn-sm">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Buat RFQ Baru
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
                        <th>No RFQ</th>
                        <th>Pemasok</th>
                        <th>Total Estimasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rfq as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal_rfq)->format('d/m/Y') }}</td>
                            <td><strong>{{ $r->no_rfq }}</strong></td>
                            <td>{{ $r->pemasok->nama_pemasok ?? '-' }}</td>
                            <td style="font-weight: 600;">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                            <td>
                                @switch($r->status)
                                    @case('Dikonversi')
                                        <span class="badge badge-success">Dikonversi</span>
                                        @break
                                    @case('Disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                        @break
                                    @case('Dikirim')
                                        <span class="badge badge-info">Dikirim</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $r->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('rfq.show', $r->id_rfq) }}" class="btn btn-sm btn-icon btn-light" title="Detail">
                                        <span data-feather="eye" style="width: 14px; height: 14px;"></span>
                                    </a>
                                    @if($r->status !== 'Dikonversi')
                                        <a href="{{ route('rfq.edit', $r->id_rfq) }}" class="btn btn-sm btn-icon btn-light" title="Edit">
                                            <span data-feather="edit-2" style="width: 14px; height: 14px;"></span>
                                        </a>
                                        <form action="{{ route('rfq.destroy', $r->id_rfq) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus RFQ ini?')">
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
                                    <div class="table-empty-icon">✉️</div>
                                    <p>Belum ada transaksi RFQ pembelian.</p>
                                    <a href="{{ route('rfq.create') }}" class="btn btn-primary btn-sm">Buat RFQ Pertama</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
