<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk #{{ $penjualan->no_faktur }}</title>
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
    <div class="center" style="font-size: 10px;">{{ $perusahaan->alamat ?? '' }}</div>
    <div class="center" style="font-size: 10px;">{{ $perusahaan->telepon ?? '' }}</div>
    
    <div class="line"></div>
    
    <div class="row"><span>No:</span><span>{{ $penjualan->no_faktur }}</span></div>
    <div class="row"><span>Tanggal:</span><span>{{ $penjualan->tanggal_faktur }}</span></div>
    <div class="row"><span>Kasir:</span><span>{{ auth()->user()->nama_user ?? '-' }}</span></div>
    
    <div class="line"></div>
    
    <div class="items">
        @foreach($penjualan->details as $d)
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
    
    @if($penjualan->diskon_total > 0)
    <div class="row"><span>Subtotal:</span><span>Rp {{ number_format($penjualan->total + $penjualan->diskon_total, 0, ',', '.') }}</span></div>
    <div class="row"><span>Diskon:</span><span>-Rp {{ number_format($penjualan->diskon_total, 0, ',', '.') }}</span></div>
    @endif
    
    <div class="total-row">
        <span>TOTAL</span>
        <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
    </div>
    
    <div class="line"></div>
    
    <div class="row"><span>Pembayaran:</span><span>{{ $penjualan->metode_pembayaran }}</span></div>
    
    <div class="line"></div>
    
    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
