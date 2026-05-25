@extends('layouts.app')

@section('title', 'Invoice Penjualan - ' . $penjualan->no_faktur)

@section('content')
<div class="no-print pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between">
    <h1 class="h2">Cetak Invoice Penjualan</h1>
    <div>
        <a href="{{ route('penjualan.show', $penjualan->id_penjualan) }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary">Cetak Invoice</button>
    </div>
</div>

<div class="invoice-box p-4 bg-white shadow-sm rounded border-top border-5 border-primary">
    <div class="row mb-4">
        <div class="col-6">
            <h4 class="text-primary fw-bold mb-0">INVOICE PENJUALAN</h4>
            <div class="text-muted small">Nomor: {{ $penjualan->no_faktur }}</div>
        </div>
        <div class="col-6 text-end">
            <h5 class="fw-bold mb-0">{{ $perusahaan->nama_perusahaan ?? 'Simple Akunting' }}</h5>
            <div class="small text-muted">{{ $perusahaan->alamat ?? '' }}</div>
            <div class="small text-muted">{{ $perusahaan->telepon ?? '' }}</div>
            <div class="small text-muted mt-2">Tanggal: {{ date('d/m/Y', strtotime($penjualan->tanggal_faktur)) }}</div>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-6">
            <div class="small fw-bold text-uppercase text-muted mb-2">Tagihan Kepada:</div>
            <div class="fw-bold">{{ $penjualan->pelanggan->nama_pelanggan ?? 'Umum' }}</div>
            <div class="small text-muted">{{ $penjualan->pelanggan->alamat ?? '-' }}</div>
            <div class="small text-muted">{{ $penjualan->pelanggan->telepon ?? '-' }}</div>
        </div>
        <div class="col-6 text-end">
            <div class="small fw-bold text-uppercase text-muted mb-2">Status Pembayaran:</div>
            <div class="fw-bold text-{{ $penjualan->status_pembayaran == 'Lunas' ? 'success' : 'danger' }}">
                {{ strtoupper($penjualan->status_pembayaran) }}
            </div>
            @if($penjualan->status_pembayaran != 'Lunas' && $penjualan->sisa_tagihan > 0)
                <div class="small text-muted mt-1">Sisa: Rp {{ number_format($penjualan->sisa_tagihan, 2, ',', '.') }}</div>
            @endif
        </div>
    </div>

    <table class="table table-bordered table-sm mb-4">
        <thead class="table-light">
            <tr>
                <th class="px-3">Barang / Deskripsi</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga</th>
                <th class="text-end px-3">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->details as $detail)
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
                <td colspan="3" class="text-end px-3">TOTAL TAGIHAN</td>
                <td class="text-end px-3 text-primary h5 mb-0">Rp {{ number_format($penjualan->total, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row">
        <div class="col-8">
            <div class="small fw-bold text-muted text-uppercase mb-1">Catatan Tambahan:</div>
            <div class="small">{{ $penjualan->keterangan ?: '-' }}</div>

            @if($penjualan->status_pembayaran != 'Lunas' && ($perusahaan->nama_bank || $perusahaan->rekening_bank))
                <div class="mt-4 p-3 bg-light border rounded">
                    <div class="small fw-bold text-uppercase mb-2 text-primary">Informasi Pembayaran:</div>
                    <div class="small">Pembayaran dapat ditransfer ke rekening berikut:</div>
                    <table class="table table-sm table-borderless mb-0 mt-2 small fw-bold">
                        <tr>
                            <td width="100" class="py-0">Bank</td>
                            <td class="py-0">: {{ $perusahaan->nama_bank ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-0">No. Rekening</td>
                            <td class="py-0">: {{ $perusahaan->rekening_bank ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-0">Atas Nama</td>
                            <td class="py-0">: {{ $perusahaan->nama_pemilik_rekening ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            @endif
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
