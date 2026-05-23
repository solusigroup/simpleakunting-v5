@extends('layouts.app')

@section('title', 'Detail RFQ - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail RFQ #{{ $rfq->no_rfq }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            @if($rfq->status !== 'Dikonversi')
                <form action="{{ route('rfq.convert', $rfq->id_rfq) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <span data-feather="check-square"></span> Konversi ke Pembelian
                    </button>
                </form>
                <a href="{{ route('rfq.edit', $rfq->id_rfq) }}" class="btn btn-sm btn-warning">
                    <span data-feather="edit"></span> Edit
                </a>
            @endif
            <a href="{{ route('rfq.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title text-muted">Informasi RFQ</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><strong>No RFQ</strong></td>
                            <td>: {{ $rfq->no_rfq }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($r->tanggal_rfq ?? $rfq->tanggal_rfq)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: 
                                @if($rfq->status === 'Dikonversi')
                                    <span class="badge badge-success">Dikonversi</span>
                                @elseif($rfq->status === 'Disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($rfq->status === 'Dikirim')
                                    <span class="badge badge-info">Dikirim</span>
                                @else
                                    <span class="badge badge-secondary">{{ $rfq->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Keterangan</strong></td>
                            <td>: {{ $rfq->keterangan ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 border-start">
                    <h5 class="card-title text-muted">Pemasok & Lokasi</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150"><strong>Pemasok</strong></td>
                            <td>: <strong>{{ $rfq->pemasok->nama_pemasok ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Cabang</strong></td>
                            <td>: {{ $rfq->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Unit Usaha</strong></td>
                            <td>: {{ $rfq->unitUsaha->nama_unit ?? '-' }}</td>
                        </tr>
                        @if($rfq->id_project)
                        <tr>
                            <td><strong>Proyek / Program</strong></td>
                            <td>: <strong>{{ $rfq->project->kode_project }}</strong> - {{ $rfq->project->nama_project }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Rincian Item RFQ</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th class="text-end" width="15%">Harga Beli Estimasi</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th class="text-end" width="20%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfq->details as $detail)
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
                            <td colspan="4" class="text-end fw-bold">Total Estimasi</td>
                            <td class="text-end fw-bold" style="font-size: 1.1rem; color: var(--color-primary);">Rp {{ number_format($rfq->total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
