@extends('layouts.app')

@section('title', 'Edit Jurnal Umum - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Edit Jurnal</h1>
            <p class="page-subtitle">Update deskripsi dan tanggal jurnal</p>
        </div>
        <div>
            <a href="{{ route('jurnal.index') }}" class="btn btn-outline btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('jurnal.update', $jurnal->id_jurnal) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Header Form -->
        <div class="form-card mb-4">
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group" style="flex: 1; margin-right: 15px;">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi" value="{{ $jurnal->no_transaksi }}" readonly style="background: var(--color-bg-light);">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $jurnal->tanggal) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row mt-3" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pelanggan" class="form-label">Pelanggan (Opsional)</label>
                        <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($pelanggan as $p)
                                <option value="{{ $p->id_pelanggan }}" {{ old('id_pelanggan', $jurnal->id_pelanggan) == $p->id_pelanggan ? 'selected' : '' }}>{{ $p->nama_pelanggan }} (Saldo: Rp {{ number_format($p->saldo_terkini_piutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pemasok" class="form-label">Pemasok (Opsional)</label>
                        <select class="form-select" id="id_pemasok" name="id_pemasok">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($pemasok as $v)
                                <option value="{{ $v->id_pemasok }}" {{ old('id_pemasok', $jurnal->id_pemasok) == $v->id_pemasok ? 'selected' : '' }}>{{ $v->nama_pemasok }} (Saldo: Rp {{ number_format($v->saldo_terkini_hutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="deskripsi" class="form-label">Deskripsi Jurnal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" required value="{{ old('deskripsi', $jurnal->deskripsi) }}">
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Jurnal (Read Only) -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h3 class="form-card-title">Detail Jurnal (Tidak dapat diubah)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th style="text-align: right;">Debit</th>
                            <th style="text-align: right;">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalKredit = 0;
                        @endphp
                        @foreach($jurnal->details as $detail)
                            <tr>
                                <td>{{ $detail->kode_akun }} - {{ $detail->akun->nama_akun ?? 'N/A' }}</td>
                                <td style="text-align: right;">{{ number_format($detail->debit, 2, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($detail->kredit, 2, ',', '.') }}</td>
                            </tr>
                            @php
                                $totalDebit += $detail->debit;
                                $totalKredit += $detail->kredit;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--color-bg-light); font-weight: bold;">
                            <td style="text-align: right;">Total</td>
                            <td style="text-align: right;">{{ number_format($totalDebit, 2, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($totalKredit, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="form-card">
            <div class="form-card-body">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 16px; width: 100%;">
                    <span data-feather="save" style="width: 18px; height: 18px; margin-right: 8px;"></span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection
