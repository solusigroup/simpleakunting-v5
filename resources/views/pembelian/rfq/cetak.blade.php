@extends('layouts.app')

@section('title', 'Cetak RFQ - ' . $rfq->no_rfq)

@section('content')
<div class="no-print pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between">
    <h1 class="h2">Cetak Request for Quotation</h1>
    <div>
        <a href="{{ route('rfq.show', $rfq->id_rfq) }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary">Cetak RFQ</button>
    </div>
</div>

<div class="invoice-box p-4 bg-white shadow-sm rounded border-top border-5 border-primary">
    <div class="row mb-4">
        <div class="col-6">
            <h4 class="text-primary fw-bold mb-0">REQUEST FOR QUOTATION</h4>
            <div class="text-muted small">Nomor: {{ $rfq->no_rfq }}</div>
        </div>
        <div class="col-6 text-end">
            <h5 class="fw-bold mb-0">{{ $perusahaan->nama_perusahaan ?? 'Simple Akunting' }}</h5>
            <div class="small text-muted">{{ $perusahaan->alamat ?? '' }}</div>
            <div class="small text-muted">{{ $perusahaan->telepon ?? '' }}</div>
            <div class="small text-muted mt-2">Tanggal: {{ date('d/m/Y', strtotime($rfq->tanggal_rfq)) }}</div>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-6">
            <div class="small fw-bold text-uppercase text-muted mb-2">Permintaan Kepada:</div>
            <div class="fw-bold">{{ $rfq->pemasok->nama_pemasok ?? 'Pemasok' }}</div>
            <div class="small text-muted">{{ $rfq->pemasok->alamat ?? '-' }}</div>
            <div class="small text-muted">{{ $rfq->pemasok->telepon ?? '-' }}</div>
        </div>
        <div class="col-6 text-end">
            <div class="small fw-bold text-uppercase text-muted mb-2">Status RFQ:</div>
            <div class="fw-bold text-{{ $rfq->status == 'Disetujui' || $rfq->status == 'Dikonversi' ? 'success' : ($rfq->status == 'Ditolak' ? 'danger' : 'secondary') }}">
                {{ strtoupper($rfq->status) }}
            </div>
        </div>
    </div>

    <table class="table table-bordered table-sm mb-4">
        <thead class="table-light">
            <tr>
                <th class="px-3">Barang / Deskripsi</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Estimasi Harga</th>
                <th class="text-end px-3">Subtotal Estimasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rfq->details as $detail)
            <tr>
                <td class="px-3">
                    <div class="fw-bold">{{ $detail->barang->nama_barang ?? 'Item Terhapus' }}</div>
                    <div class="small text-muted">{{ $detail->barang->kode_barang ?? '-' }}</div>
                </td>
                <td class="text-center">{{ $detail->kuantitas }} {{ $detail->barang->satuan ?? '' }}</td>
                <td class="text-end">Rp {{ number_format($detail->harga, 2, ',', '.') }}</td>
                <td class="text-end px-3">Rp {{ number_format($detail->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="3" class="text-end px-3">TOTAL ESTIMASI</td>
                <td class="text-end px-3 text-primary h5 mb-0">Rp {{ number_format($rfq->total, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-8">
            <div class="small fw-bold text-muted text-uppercase mb-1">Catatan / Keterangan:</div>
            <div class="small">{{ $rfq->keterangan ?: '-' }}</div>
        </div>
        <div class="col-4">
            <div class="text-center small mt-4">
                <div>Hormat Kami,</div>
                <div style="margin-top: 60px;" class="fw-bold">( {{ $perusahaan->nama_perusahaan ?? '____________________' }} )</div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .app-header, .app-sidebar, .no-print, .btn-toolbar { display: none !important; }
        .invoice-box { border: none !important; box-shadow: none !important; padding: 0 !important; }
        body { background: white !important; }
        .app-main { margin-left: 0 !important; margin-top: 0 !important; width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection
