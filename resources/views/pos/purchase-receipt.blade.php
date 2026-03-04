<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Penerimaan #{{ $pembelian->no_faktur_pembelian }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 8px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; }
        .items { margin: 4px 0; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; padding-left: 8px; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 8px; text-align: center; font-size: 10px; }
        @media print { body { width: 80mm; } @page { margin: 0; size: 80mm auto; } }
    </style>
</head>
<body onload="window.print()">
    <div class="center bold" style="font-size: 14px;">{{ $perusahaan->nama_perusahaan ?? 'Nama Toko' }}</div>
    <div class="center bold">BUKTI PENERIMAAN BARANG</div>
    <div class="center" style="font-size: 10px;">{{ $perusahaan->alamat ?? '' }}</div>
    
    <div class="line"></div>
    
    <div class="row"><span>No:</span><span>{{ $pembelian->no_faktur_pembelian }}</span></div>
    <div class="row"><span>Tanggal:</span><span>{{ $pembelian->tanggal_faktur }}</span></div>
    <div class="row"><span>Pemasok:</span><span>{{ $pembelian->pemasok->nama_pemasok ?? '-' }}</span></div>
    <div class="row"><span>Bayar:</span><span>{{ $pembelian->metode_pembayaran }}</span></div>
    
    <div class="line"></div>
    
    <div class="items">
        @foreach($pembelian->details as $d)
        <div>
            <div class="item-name">{{ $d->barang->nama_barang ?? '-' }}</div>
            <div class="item-detail">
                <span>{{ number_format($d->kuantitas, 0) }} x Rp {{ number_format($d->harga, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="line"></div>
    
    <div class="total-row">
        <span>TOTAL</span>
        <span>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span>
    </div>
    
    <div class="line"></div>
    
    <div class="footer">
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
