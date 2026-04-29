@extends('layouts.app')

@section('title', 'Tambah Kelompok Aset Tetap - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Kelompok Aset Tetap</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('aset-tetap-group.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('aset-tetap-group.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_kelompok" class="form-label">Nama Kelompok</label>
                            <input type="text" class="form-control @error('nama_kelompok') is-invalid @enderror" id="nama_kelompok" name="nama_kelompok" value="{{ old('nama_kelompok') }}" required>
                            @error('nama_kelompok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Contoh: Kendaraan Bermotor, Bangunan Kantor</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="umur_ekonomis" class="form-label">Umur Ekonomis (Bulan)</label>
                                <input type="number" class="form-control @error('umur_ekonomis') is-invalid @enderror" id="umur_ekonomis" name="umur_ekonomis" value="{{ old('umur_ekonomis') }}" required min="1">
                                @error('umur_ekonomis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Berapa bulan aset ini akan disusutkan? (Contoh: 4 tahun = 48 bulan)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="metode_penyusutan" class="form-label">Metode Penyusutan</label>
                                <select class="form-select @error('metode_penyusutan') is-invalid @enderror" id="metode_penyusutan" name="metode_penyusutan" required>
                                    <option value="Garis Lurus" {{ old('metode_penyusutan') == 'Garis Lurus' ? 'selected' : '' }}>Garis Lurus (Straight Line)</option>
                                    <option value="Saldo Menurun" {{ old('metode_penyusutan') == 'Saldo Menurun' ? 'selected' : '' }}>Saldo Menurun (Declining Balance)</option>
                                </select>
                                @error('metode_penyusutan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Pemetaan Akun (Untuk Auto-Jurnal)</h5>
                        
                        <div class="mb-3">
                            <label for="akun_aset" class="form-label">Akun Aset Tetap</label>
                            <select class="form-select select2 @error('akun_aset') is-invalid @enderror" id="akun_aset" name="akun_aset">
                                <option value="">-- Pilih Akun Aset --</option>
                                @foreach($akuns as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_aset') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_aset')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="akun_akumulasi_penyusutan" class="form-label">Akun Akumulasi Penyusutan</label>
                            <select class="form-select select2 @error('akun_akumulasi_penyusutan') is-invalid @enderror" id="akun_akumulasi_penyusutan" name="akun_akumulasi_penyusutan">
                                <option value="">-- Pilih Akun Akumulasi Penyusutan --</option>
                                @foreach($akuns as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_akumulasi_penyusutan') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_akumulasi_penyusutan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="akun_beban_penyusutan" class="form-label">Akun Beban Penyusutan</label>
                            <select class="form-select select2 @error('akun_beban_penyusutan') is-invalid @enderror" id="akun_beban_penyusutan" name="akun_beban_penyusutan">
                                <option value="">-- Pilih Akun Beban Penyusutan --</option>
                                @foreach($akuns as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_beban_penyusutan') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_beban_penyusutan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
