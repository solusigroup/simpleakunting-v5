@extends('layouts.app')

@section('title', 'Sesi Kasir - Simple Akunting')

@section('content')
<style>
    .shift-status { border-radius: 12px; padding: 20px; margin-bottom: 24px; }
    .shift-open { background: linear-gradient(135deg, #dcfce7, #bbf7d0); border: 2px solid #86efac; }
    .shift-closed { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border: 2px solid #cbd5e1; }
    .shift-status-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .shift-status-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
    .shift-status-dot.active { background: #22c55e; box-shadow: 0 0 8px rgba(34,197,94,0.5); animation: pulse 2s infinite; }
    .shift-status-dot.inactive { background: #94a3b8; }
    @keyframes pulse { 0%,100% { box-shadow: 0 0 4px rgba(34,197,94,0.3); } 50% { box-shadow: 0 0 12px rgba(34,197,94,0.7); } }
    .shift-status-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; }
    .shift-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; }
    .shift-stat { background: rgba(255,255,255,0.8); border-radius: 8px; padding: 10px 14px; }
    .shift-stat .label { font-size: 0.75rem; color: #64748b; font-weight: 500; text-transform: uppercase; }
    .shift-stat .value { font-size: 1.1rem; font-weight: 700; color: #0f172a; }
    .shift-actions { display: flex; gap: 8px; margin-top: 16px; flex-wrap: wrap; }
    .open-form .card { border: 2px dashed #cbd5e1; background: #fafafa; }
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h4 mb-0">⏱️ Manajemen Shift Kasir</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@php
    $openSession = \App\Models\PosSession::where('id_user', auth()->id())
        ->whereNull('closed_at')
        ->first();
@endphp

{{-- ===== STATUS SHIFT AKTIF ===== --}}
@if($openSession)
<div class="shift-status shift-open">
    <div class="shift-status-header">
        <div class="shift-status-dot active"></div>
        <div class="shift-status-title">🟢 Shift AKTIF — dibuka {{ $openSession->opened_at->format('d M Y \p\u\k\u\l H:i') }}</div>
    </div>

    @php
        $totalSales = \App\Models\Penjualan::where('id_pos_session', $openSession->id)->sum('total');
        $totalPurchases = \App\Models\Pembelian::where('id_pos_session', $openSession->id)->sum('total');
        $purchasesTunai = \App\Models\Pembelian::where('id_pos_session', $openSession->id)
            ->where('metode_pembayaran', 'Tunai')->sum('total');
        $purchasesKredit = \App\Models\Pembelian::where('id_pos_session', $openSession->id)
            ->where('metode_pembayaran', 'Kredit')->sum('total');
        $jumlahTrxSales = \App\Models\Penjualan::where('id_pos_session', $openSession->id)->count();
        $jumlahTrxBuy = \App\Models\Pembelian::where('id_pos_session', $openSession->id)->count();
        // Only Tunai purchases affect cash in the drawer
        $expectedCash = $openSession->saldo_awal + $totalSales - $purchasesTunai;
        $durasi = $openSession->opened_at->diffForHumans(null, true);
    @endphp

    <div class="shift-stats">
        <div class="shift-stat">
            <div class="label">Durasi</div>
            <div class="value">{{ $durasi }}</div>
        </div>
        <div class="shift-stat">
            <div class="label">Saldo Awal</div>
            <div class="value">Rp {{ number_format($openSession->saldo_awal, 0, ',', '.') }}</div>
        </div>
        <div class="shift-stat">
            <div class="label">Penjualan ({{ $jumlahTrxSales }} trx)</div>
            <div class="value" style="color: #059669;">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
        </div>
        @if($purchasesTunai > 0)
        <div class="shift-stat">
            <div class="label">Pembelian Tunai</div>
            <div class="value" style="color: #dc2626;">Rp {{ number_format($purchasesTunai, 0, ',', '.') }}</div>
        </div>
        @endif
        @if($purchasesKredit > 0)
        <div class="shift-stat">
            <div class="label">Pembelian Kredit (tidak pengaruh kas)</div>
            <div class="value" style="color: #94a3b8;">Rp {{ number_format($purchasesKredit, 0, ',', '.') }}</div>
        </div>
        @endif
        <div class="shift-stat">
            <div class="label">Kas Seharusnya</div>
            <div class="value" style="color: #7c3aed;">Rp {{ number_format($expectedCash, 0, ',', '.') }}</div>
        </div>
    </div>

    <hr style="border-color: #86efac;">

    <form action="{{ route('pos.session.close') }}" method="POST" class="d-flex align-items-end gap-3 flex-wrap">
        @csrf
        <div>
            <label for="saldo_akhir" class="form-label fw-bold" style="color: #0f172a;">Saldo Akhir Aktual (Rp)</label>
            <input type="number" class="form-control" id="saldo_akhir" name="saldo_akhir" min="0" required style="max-width: 220px; color: #0f172a; background: #fff;">
            <small class="text-muted">Hitung uang di laci kas sekarang</small>
        </div>
        <div class="shift-actions">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin tutup shift ini?')">🔴 Tutup Shift</button>
            <a href="{{ route('pos.shift.report', $openSession->id) }}" class="btn btn-outline-dark">📊 Lihat Laporan</a>
            <a href="{{ route('pos.index') }}" class="btn btn-success">🛒 Kembali ke Kasir</a>
        </div>
    </form>
</div>

@else
{{-- ===== TIDAK ADA SHIFT AKTIF ===== --}}
<div class="shift-status shift-closed">
    <div class="shift-status-header">
        <div class="shift-status-dot inactive"></div>
        <div class="shift-status-title">⚪ Tidak Ada Shift Aktif</div>
    </div>
    <p style="color: #475569; margin-bottom: 16px;">Buka shift baru untuk mulai transaksi POS.</p>

    <form action="{{ route('pos.session.open') }}" method="POST" class="d-flex align-items-end gap-3 flex-wrap">
        @csrf
        <div>
            <label for="saldo_awal" class="form-label fw-bold" style="color: #0f172a;">Saldo Awal Laci Kas (Rp)</label>
            <input type="number" class="form-control @error('saldo_awal') is-invalid @enderror"
                   id="saldo_awal" name="saldo_awal" value="{{ old('saldo_awal', 0) }}" min="0" required
                   style="max-width: 220px; color: #0f172a; background: #fff;">
            @error('saldo_awal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Modal awal di laci kas sebelum mulai shift</small>
        </div>
        <div>
            <button type="submit" class="btn btn-success btn-lg">🟢 Buka Shift Baru</button>
        </div>
    </form>
</div>
@endif

{{-- ===== RIWAYAT SHIFT ===== --}}
<div class="card mt-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📋 Riwayat Shift</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Buka</th>
                        <th>Tutup</th>
                        <th>Durasi</th>
                        <th class="text-end">Saldo Awal</th>
                        <th class="text-end">Penjualan</th>
                        <th class="text-end">Pembelian</th>
                        <th class="text-end">Saldo Akhir</th>
                        <th class="text-end">Selisih</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $riwayat = \App\Models\PosSession::where('id_user', auth()->id())
                            ->whereNotNull('closed_at')
                            ->orderByDesc('closed_at')
                            ->limit(20)
                            ->get();
                    @endphp
                    @forelse($riwayat as $s)
                    <tr>
                        <td>{{ $s->opened_at->format('d M Y') }}</td>
                        <td>{{ $s->opened_at->format('H:i') }}</td>
                        <td>{{ $s->closed_at->format('H:i') }}</td>
                        <td>{{ $s->opened_at->diff($s->closed_at)->format('%Hj %Im') }}</td>
                        <td class="text-end">Rp {{ number_format($s->saldo_awal, 0, ',', '.') }}</td>
                        <td class="text-end" style="color: #059669; font-weight: 600;">Rp {{ number_format($s->total_penjualan, 0, ',', '.') }}</td>
                        <td class="text-end" style="color: #dc2626;">Rp {{ number_format($s->total_pembelian, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($s->saldo_akhir, 0, ',', '.') }}</td>
                        <td class="text-end {{ $s->selisih != 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                            Rp {{ number_format($s->selisih, 0, ',', '.') }}
                            @if($s->selisih == 0) ✅ @endif
                        </td>
                        <td>
                            <a href="{{ route('pos.shift.report', $s->id) }}" class="btn btn-sm btn-outline-primary">📊 Laporan</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada riwayat shift.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
