@extends('layouts.app')

@section('title', 'Detail Penawaran - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Penawaran #{{ $penawaran->no_penawaran }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            @if($penawaran->status !== 'Dikonversi')
                <form action="{{ route('penawaran.convert', $penawaran->id_penawaran) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <span data-feather="check-square"></span> Konversi ke Faktur
                    </button>
                </form>
                <a href="{{ route('penawaran.edit', $penawaran->id_penawaran) }}" class="btn btn-sm btn-warning">
                    <span data-feather="edit"></span> Edit
                </a>
            @endif
            <a href="{{ route('penawaran.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title text-muted">Informasi Penawaran</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><strong>No Penawaran</strong></td>
                            <td>: {{ $penawaran->no_penawaran }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($penawaran->tanggal_penawaran)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: 
                                @if($penawaran->status === 'Dikonversi')
                                    <span class="badge badge-success">Dikonversi</span>
                                @elseif($penawaran->status === 'Diterima')
                                    <span class="badge badge-primary">Diterima</span>
                                @elseif($penawaran->status === 'Ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @elseif($penawaran->status === 'Dikirim')
                                    <span class="badge badge-info">Dikirim</span>
                                @else
                                    <span class="badge badge-secondary">{{ $penawaran->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Keterangan</strong></td>
                            <td>: {{ $penawaran->keterangan ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 border-start">
                    <h5 class="card-title text-muted">Pelanggan & Lokasi</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><strong>Pelanggan</strong></td>
                            <td>: <strong>{{ $penawaran->pelanggan->nama_pelanggan ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Cabang</strong></td>
                            <td>: {{ $penawaran->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Unit Usaha</strong></td>
                            <td>: {{ $penawaran->unitUsaha->nama_unit ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Rincian Item Penawaran</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th class="text-end" width="15%">Harga</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th class="text-end" width="20%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penawaran->details as $detail)
                            <tr>
                                <td>{{ $detail->barang->kode_barang ?? '-' }}</td>
                                <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($detail->harga, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($detail->kuantitas, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($detail->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold">Total Penawaran</td>
                            <td class="text-end fw-bold" style="font-size: 1.1rem; color: var(--color-primary);">Rp {{ number_format($penawaran->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
