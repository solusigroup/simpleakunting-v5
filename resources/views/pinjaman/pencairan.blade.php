@extends('layouts.app')

@section('title', 'Pencairan Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pencairan Pinjaman</h1>
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
                    <span>Jenis:</span>
                    <strong>{{ $pinjaman->jenisPinjaman->nama_pinjaman }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Pokok:</span>
                    <strong class="text-primary fs-5">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Provisi:</span>
                    <strong class="text-danger">- Rp {{ number_format($pinjaman->provisi, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Biaya Admin:</span>
                    <strong class="text-danger">- Rp {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-success text-white">
                    <span>Dana Cair (Nett):</span>
                    <strong class="fs-5">Rp {{ number_format($pinjaman->net_disbursement, 0, ',', '.') }}</strong>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <strong>Form Pencairan</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pinjaman.cairkan', $pinjaman->id_pinjaman) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pencairan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pencairan" class="form-control @error('tanggal_pencairan') is-invalid @enderror" 
                               value="{{ old('tanggal_pencairan', date('Y-m-d')) }}" required>
                        @error('tanggal_pencairan')
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

                    <div class="alert alert-info">
                        <strong>Jurnal yang akan dibuat:</strong><br>
                        <small>
                            Dr. Piutang Pinjaman: Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}<br>
                            &nbsp;&nbsp;&nbsp;Cr. Kas/Bank: Rp {{ number_format($pinjaman->net_disbursement, 0, ',', '.') }}<br>
                            @if($pinjaman->provisi > 0)
                            &nbsp;&nbsp;&nbsp;Cr. Pendapatan Provisi: Rp {{ number_format($pinjaman->provisi, 0, ',', '.') }}<br>
                            @endif
                            @if($pinjaman->biaya_admin > 0)
                            &nbsp;&nbsp;&nbsp;Cr. Pendapatan Admin: Rp {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}
                            @endif
                        </small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Yakin akan mencairkan pinjaman ini?')">
                            <span data-feather="check-circle"></span> Proses Pencairan
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
