@extends('layouts.app')

@section('title', 'Laporan Shift Kasir - Simple Akunting')

@section('content')
<style>
    .shift-report { max-width: 900px; margin: 0 auto; }

    /* Force light backgrounds for readability */
    .report-card { background: #ffffff; border-radius: 12px; padding: 24px; margin-bottom: 20px; color: #0f172a; }
    .report-header { text-align: center; margin-bottom: 20px; color: #0f172a; }
    .report-header h4 { font-weight: 700; margin-bottom: 2px; color: #0f172a; }
    .report-header h5 { font-weight: 600; color: #334155; margin-bottom: 8px; }
    .report-header .meta { color: #475569; font-size: 0.9rem; line-height: 1.8; }
    .report-header .meta strong { color: #0f172a; }

    .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-bottom: 20px; }
    .summary-card { background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 10px; padding: 14px; text-align: center; }
    .summary-card .label { font-size: 0.75rem; color: #475569; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-card .value { font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-top: 4px; }
    .summary-card .value.positive { color: #059669; }
    .summary-card .value.negative { color: #dc2626; }
    .summary-card .value.purple { color: #7c3aed; }
    .summary-card .value.selisih-ok { color: #059669; }
    .summary-card .value.selisih-bad { color: #dc2626; }

    .section-title { font-weight: 700; font-size: 1rem; margin: 16px 0 8px; padding-bottom: 6px; border-bottom: 2px solid #cbd5e1; color: #0f172a; }
    .section-title .badge { font-size: 0.75rem; font-weight: 600; }

    .trx-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; background: #ffffff; }
    .trx-table th { background: #e2e8f0; padding: 8px 12px; text-align: left; font-weight: 700; color: #0f172a; border-bottom: 2px solid #cbd5e1; }
    .trx-table td { padding: 6px 12px; border-bottom: 1px solid #e2e8f0; color: #0f172a; }
    .trx-table .text-end { text-align: right; }
    .trx-table .fw-bold { font-weight: 700; }
    .trx-table .subtotal-row { background: #f1f5f9; }
    .trx-table .subtotal-row td { color: #0f172a; font-weight: 600; }

    .cash-summary { background: #ffffff; border: 2px solid #86efac; border-radius: 10px; padding: 20px; margin-top: 16px; }
    .cash-summary h6 { font-weight: 700; margin-bottom: 12px; color: #166534; font-size: 1rem; }
    .cash-row { display: flex; justify-content: space-between; padding: 5px 0; color: #0f172a; font-size: 0.95rem; }
    .cash-row.total { border-top: 2px solid #86efac; margin-top: 8px; padding-top: 8px; font-weight: 800; font-size: 1.1rem; color: #0f172a; }
    .cash-row.selisih { font-weight: 700; font-size: 1rem; }
    .cash-row .green { color: #059669; }
    .cash-row .red { color: #dc2626; }
    .cash-row .gray { color: #64748b; }
    .cash-row .bold { font-weight: 700; }

    .btn-actions { display: flex; gap: 8px; margin-bottom: 16px; }

    @media print {
        .no-print { display: none !important; }
        .shift-report { max-width: 100%; }
        body, .main-content, .content-wrapper { background: #fff !important; color: #000 !important; }
        .report-card { box-shadow: none; border: 1px solid #ccc; }
    }
</style>

@php
    // Recalculate selisih correctly (fixes stale DB value from old bug)
    $selisihBenar = $session->closed_at ? ($session->saldo_akhir - $expectedCash) : null;
@endphp

<div class="shift-report">
    <!-- Actions -->
    <div class="btn-actions no-print">
        <a href="{{ route('pos.session.create') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">🖨️ Cetak Laporan</button>
    </div>

    <div class="report-card">
        <!-- Header -->
        <div class="report-header">
            <h4>{{ $perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h4>
            <h5>LAPORAN SHIFT KASIR</h5>
            <div class="meta">
                <strong>Kasir:</strong> {{ $session->user->nama_user ?? '-' }}<br>
                <strong>Dibuka:</strong> {{ $session->opened_at->format('d M Y H:i') }} —
                <strong>Ditutup:</strong> {{ $session->closed_at ? $session->closed_at->format('d M Y H:i') : 'Masih Aktif' }}
                @if($session->closed_at)
                    ({{ $session->opened_at->diff($session->closed_at)->format('%Hj %Im') }})
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="label">Saldo Awal</div>
                <div class="value">Rp {{ number_format($session->saldo_awal, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Penjualan ({{ $jumlahTransaksiSales }} trx)</div>
                <div class="value positive">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Pembelian Tunai</div>
                <div class="value negative">Rp {{ number_format($pembelianTunai, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Kas Seharusnya</div>
                <div class="value purple">Rp {{ number_format($expectedCash, 0, ',', '.') }}</div>
            </div>
            @if($session->closed_at)
            <div class="summary-card">
                <div class="label">Saldo Akhir Aktual</div>
                <div class="value">Rp {{ number_format($session->saldo_akhir, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Selisih</div>
                <div class="value {{ $selisihBenar == 0 ? 'selisih-ok' : 'selisih-bad' }}">
                    Rp {{ number_format(abs($selisihBenar), 0, ',', '.') }}
                    @if($selisihBenar == 0) ✅ @elseif($selisihBenar > 0) ⬆️ Lebih @else ⬇️ Kurang @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sales Detail -->
        <div class="section-title">
            💰 Detail Penjualan <span class="badge bg-success">{{ $jumlahTransaksiSales }} transaksi · {{ $jumlahItemSales }} item</span>
        </div>
        @if($penjualan->count() > 0)
        <table class="trx-table">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Waktu</th>
                    <th>Barang</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan as $p)
                    @foreach($p->details as $idx => $d)
                    <tr>
                        @if($idx === 0)
                        <td rowspan="{{ $p->details->count() }}" style="font-weight: 600;">{{ $p->no_faktur }}</td>
                        <td rowspan="{{ $p->details->count() }}">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                        @endif
                        <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td class="text-end">{{ number_format($d->kuantitas, 0) }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($p->diskon_total > 0)
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-end"><em>Diskon</em></td>
                        <td class="text-end" style="color: #dc2626;">-Rp {{ number_format($p->diskon_total, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-end fw-bold">Total Faktur</td>
                        <td class="text-end fw-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #dcfce7;">
                    <td colspan="5" class="text-end fw-bold" style="font-size: 1rem; color: #0f172a;">TOTAL PENJUALAN</td>
                    <td class="text-end fw-bold" style="font-size: 1rem; color: #059669;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align: center; padding: 20px; color: #64748b;">Tidak ada transaksi penjualan pada shift ini.</p>
        @endif

        <!-- Purchase Detail -->
        @if($pembelian->count() > 0)
        <div class="section-title">
            📥 Detail Pembelian <span class="badge bg-primary">{{ $jumlahTransaksiBuy }} transaksi</span>
        </div>
        <table class="trx-table">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Pemasok</th>
                    <th>Bayar</th>
                    <th>Barang</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembelian as $b)
                    @foreach($b->details as $idx => $d)
                    <tr>
                        @if($idx === 0)
                        <td rowspan="{{ $b->details->count() }}" style="font-weight: 600;">{{ $b->no_faktur_pembelian }}</td>
                        <td rowspan="{{ $b->details->count() }}">{{ $b->pemasok->nama_pemasok ?? '-' }}</td>
                        <td rowspan="{{ $b->details->count() }}">
                            <span class="badge {{ $b->metode_pembayaran == 'Tunai' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $b->metode_pembayaran }}</span>
                        </td>
                        @endif
                        <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td class="text-end">{{ number_format($d->kuantitas, 0) }}</td>
                        <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="5" class="text-end fw-bold">Total Faktur</td>
                        <td class="text-end fw-bold">Rp {{ number_format($b->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #dbeafe;">
                    <td colspan="5" class="text-end fw-bold" style="font-size: 1rem; color: #0f172a;">TOTAL PEMBELIAN</td>
                    <td class="text-end fw-bold" style="font-size: 1rem; color: #2563eb;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Cash Summary -->
        <div class="cash-summary">
            <h6>💵 Ringkasan Kas</h6>
            <div class="cash-row">
                <span class="bold">Saldo Awal Laci</span>
                <span class="bold">Rp {{ number_format($session->saldo_awal, 0, ',', '.') }}</span>
            </div>
            <div class="cash-row">
                <span class="green">(+) Penjualan Tunai</span>
                <span class="green bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
            </div>
            @if($totalDiskon > 0)
            <div class="cash-row">
                <span class="gray">&nbsp;&nbsp;&nbsp;&nbsp;Diskon diberikan</span>
                <span class="gray">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($pembelianTunai > 0)
            <div class="cash-row">
                <span class="red">(−) Pembelian Tunai</span>
                <span class="red bold">Rp {{ number_format($pembelianTunai, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($pembelianKredit > 0)
            <div class="cash-row">
                <span class="gray">(i) Pembelian Kredit (tidak mempengaruhi kas)</span>
                <span class="gray">Rp {{ number_format($pembelianKredit, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="cash-row total">
                <span>Kas Yang Seharusnya</span>
                <span>Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
            </div>
            @if($session->closed_at)
            <div class="cash-row">
                <span class="bold">Saldo Akhir Aktual</span>
                <span class="bold">Rp {{ number_format($session->saldo_akhir, 0, ',', '.') }}</span>
            </div>
            <div class="cash-row selisih" style="color: {{ $selisihBenar == 0 ? '#059669' : '#dc2626' }};">
                <span>Selisih</span>
                <span>
                    Rp {{ number_format(abs($selisihBenar), 0, ',', '.') }}
                    @if($selisihBenar == 0) ✅ Pas
                    @elseif($selisihBenar > 0) ⬆️ Lebih
                    @else ⬇️ Kurang
                    @endif
                </span>
            </div>
            @endif
        </div>

        <div style="text-align: center; color: #94a3b8; margin-top: 16px; font-size: 0.8rem;">
            <p>Kasir: <strong style="color: #334155;">{{ $session->user->nama_user ?? '-' }}</strong> · Dicetak: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</div>
@endsection
