@extends('layouts.app')

@section('title', 'Detail Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Transaksi Simpanan</h1>
    <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Informasi Transaksi</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Transaksi:</span>
                    <strong>{{ $simpanan->no_transaksi }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tanggal:</span>
                    <strong>{{ $simpanan->tanggal->format('d/m/Y') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jenis Transaksi:</span>
                    @if($simpanan->jenis_transaksi == 'setor')
                        <span class="badge bg-success fs-6">SETOR</span>
                    @else
                        <span class="badge bg-danger fs-6">TARIK</span>
                    @endif
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jenis Simpanan:</span>
                    <strong>{{ $simpanan->jenisSimpanan->nama_simpanan }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jumlah:</span>
                    <strong class="fs-5 {{ $simpanan->jenis_transaksi == 'setor' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($simpanan->jumlah, 0, ',', '.') }}
                    </strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Akun Kas/Bank:</span>
                    <strong>{{ $simpanan->akun_kas_bank }}</strong>
                </li>
                @if($simpanan->keterangan)
                <li class="list-group-item">
                    <span>Keterangan:</span><br>
                    <strong>{{ $simpanan->keterangan }}</strong>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Anggota</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Anggota:</span>
                    <strong>{{ $simpanan->anggota->no_anggota }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Nama:</span>
                    <strong>{{ $simpanan->anggota->nama_lengkap }}</strong>
                </li>
                <li class="list-group-item">
                    <a href="{{ route('anggota.show', $simpanan->id_anggota) }}" class="btn btn-sm btn-outline-primary">
                        <span data-feather="user"></span> Lihat Profil Anggota
                    </a>
                    <a href="{{ route('simpanan.kartu', $simpanan->id_anggota) }}" class="btn btn-sm btn-outline-success">
                        <span data-feather="credit-card"></span> Kartu Simpanan
                    </a>
                </li>
            </ul>
        </div>

        @if($simpanan->jurnal)
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <strong>Jurnal Akuntansi</strong>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>No. Jurnal:</strong> {{ $simpanan->jurnal->no_transaksi }}<br>
                    <strong>Tanggal:</strong> {{ $simpanan->jurnal->tanggal->format('d/m/Y') }}
                </p>
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simpanan->jurnal->details as $detail)
                        <tr>
                            <td>{{ $detail->kode_akun }} - {{ $detail->akun->nama_akun ?? '-' }}</td>
                            <td class="text-end">{{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-' }}</td>
                            <td class="text-end">{{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali ke Daftar
    </a>
</div>

@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
