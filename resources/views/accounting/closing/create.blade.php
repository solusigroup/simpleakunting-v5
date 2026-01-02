@extends('layouts.app')

@section('title', 'Proses Tutup Buku')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Proses Tutup Buku Baru</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                Konfirmasi Tutup Buku
            </div>
            <div class="card-body">
                <p>Proses ini akan:</p>
                <ul>
                    <li>Menghitung Total Pendapatan & Beban periode tersebut.</li>
                    <li>Membuat Jurnal Penutup (Closing Entries) untuk menol-kan akun Nominal.</li>
                    <li>Memindahkan Laba/Rugi Bersih ke Laba Ditahan.</li>
                    <li>Mengunci periode agar tidak ada transaksi baru di tanggal tersebut.</li>
                </ul>
                
                <form action="{{ route('accounting.closing.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup buku periode ini? Aksi ini akan membuat jurnal otomatis.');">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ date('n')-1 == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Closing</label>
                        <input type="date" name="tanggal_tutup" class="form-control" value="{{ date('Y-m-t', strtotime('-1 month')) }}" required>
                        <small class="text-muted">Biasanya tanggal terakhir bulan tersebut.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" placeholder="Catatan closing..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Eksekusi Tutup Buku</button>
                    <a href="{{ route('accounting.closing.index') }}" class="btn btn-link text-center w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
