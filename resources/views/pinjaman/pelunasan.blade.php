@extends('layouts.app')

@section('title', 'Pelunasan Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pelunasan Pinjaman</h1>
    <a href="{{ route('pinjaman.show', $pinjaman->id_pinjaman) }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Pinjaman</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Pinjaman:</span>
                    <strong>{{ $pinjaman->no_pinjaman }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Anggota:</span>
                    <strong>{{ $pinjaman->anggota->nama_lengkap }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Pokok Awal:</span>
                    <strong>Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sisa Pokok:</span>
                    <strong class="text-danger fs-5">Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sisa Bunga:</span>
                    <strong>Rp {{ number_format($pinjaman->sisa_bunga, 0, ',', '.') }}</strong>
                </li>
            </ul>
        </div>

        <!-- Summary Pelunasan -->
        <div class="card bg-light">
            <div class="card-header">
                <strong>Kalkulasi Pelunasan</strong>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td>Sisa Pokok</td>
                        <td class="text-end">Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        // Simple early settlement calculation - 50% discount on remaining interest for flat method
                        $sisaBunga = $pinjaman->sisa_bunga;
                        $diskonBunga = $pinjaman->metode_bunga == 'flat' ? $sisaBunga * 0.5 : 0;
                        $bungaYangDibayar = $sisaBunga - $diskonBunga;
                        $totalPelunasan = $pinjaman->sisa_pokok + $bungaYangDibayar;
                    @endphp
                    <tr>
                        <td>Sisa Bunga</td>
                        <td class="text-end">Rp {{ number_format($sisaBunga, 0, ',', '.') }}</td>
                    </tr>
                    @if($diskonBunga > 0)
                    <tr class="text-success">
                        <td>Diskon Pelunasan Dipercepat</td>
                        <td class="text-end">- Rp {{ number_format($diskonBunga, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td><strong>Total Pelunasan</strong></td>
                        <td class="text-end text-primary"><strong class="fs-5">Rp {{ number_format($totalPelunasan, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <strong>Form Pelunasan</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pinjaman.lunasi', $pinjaman->id_pinjaman) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pelunasan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                               value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                        @error('tanggal_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Akun Kas/Bank <span class="text-danger">*</span></label>
                        <select name="akun_kas_bank" class="form-select @error('akun_kas_bank') is-invalid @enderror" required>
                            <option value="">Pilih Akun...</option>
                            @foreach($akunKasBank as $akun)
                                <option value="{{ $akun->kode_akun }}" {{ old('akun_kas_bank') == $akun->kode_akun ? 'selected' : '' }}>
                                    {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                        @error('akun_kas_bank')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pokok Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="pokok_dibayar" class="form-control" 
                                           value="{{ old('pokok_dibayar', $pinjaman->sisa_pokok) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bunga Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="bunga_dibayar" class="form-control" 
                                           value="{{ old('bunga_dibayar', $bungaYangDibayar) }}">
                                </div>
                                <small class="text-muted">Dapat disesuaikan sesuai kebijakan</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Denda (jika ada)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="denda" class="form-control" 
                                   value="{{ old('denda', 0) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', 'Pelunasan dipercepat') }}</textarea>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong><br>
                        Setelah pelunasan, pinjaman akan berubah status menjadi LUNAS dan tidak dapat diubah kembali.
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Yakin akan melunasi pinjaman ini?')">
                            <span data-feather="check-circle"></span> Proses Pelunasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
