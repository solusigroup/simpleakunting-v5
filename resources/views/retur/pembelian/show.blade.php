@extends('layouts.app')

@section('title', 'Nota Retur Pembelian - ' . $retur->no_retur)

@section('content')
<div class="no-print pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between">
    <h1 class="h2">Detail Retur Pembelian</h1>
    <div>
        <a href="{{ route('retur.pembelian.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary">Cetak Nota Retur</button>
    </div>
</div>

<div class="invoice-box p-4 bg-white shadow-sm rounded border-top border-5 border-warning">
    <div class="row mb-4">
        <div class="col-6">
            <h4 class="text-warning fw-bold mb-0">NOTA RETUR PEMBELIAN</h4>
            <div class="text-muted small">Nomor: {{ $retur->no_retur }}</div>
        </div>
        <div class="col-6 text-end">
            <h5 class="fw-bold mb-0">Simple Akunting</h5>
            <div class="small text-muted">Tanggal: {{ date('d/m/Y', strtotime($retur->tanggal)) }}</div>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-6">
            <div class="small fw-bold text-uppercase text-muted mb-2">Pemasok:</div>
            <div class="fw-bold">{{ $retur->pemasok->nama_pemasok }}</div>
            <div class="small text-muted">{{ $retur->pemasok->alamat }}</div>
        </div>
        <div class="col-6 text-end">
            <div class="small fw-bold text-uppercase text-muted mb-2">Referensi Faktur:</div>
            <div class="fw-bold">#{{ $retur->pembelian->no_faktur_pembelian }}</div>
            <div class="small text-muted">Tanggal Faktur: {{ date('d/m/Y', strtotime($retur->pembelian->tanggal_faktur)) }}</div>
        </div>
    </div>

    <table class="table table-bordered table-sm mb-4">
        <thead class="table-light">
            <tr>
                <th class="px-3">Barang</th>
                <th class="text-center">Kuantitas</th>
                <th class="text-end">Harga</th>
                <th class="text-end px-3">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retur->details as $detail)
            <tr>
                <td class="px-3">
                    <div class="fw-bold">{{ $detail->barang->nama_barang }}</div>
                    <div class="small text-muted">{{ $detail->barang->kode_barang }}</div>
                </td>
                <td class="text-center">{{ $detail->kuantitas }} {{ $detail->barang->satuan }}</td>
                <td class="text-end">Rp {{ number_format($detail->harga, 2, ',', '.') }}</td>
                <td class="text-end px-3">Rp {{ number_format($detail->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="3" class="text-end px-3">TOTAL NILAI RETUR</td>
                <td class="text-end px-3">Rp {{ number_format($retur->total_retur, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-8">
            <div class="small fw-bold text-muted text-uppercase mb-1">Keterangan:</div>
            <div class="small">{{ $retur->keterangan ?: '-' }}</div>
        </div>
        <div class="col-4">
            <div class="text-center small mt-4">
                <div>Disetujui Oleh,</div>
                <div style="margin-top: 60px;" class="fw-bold">( ____________________ )</div>
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
