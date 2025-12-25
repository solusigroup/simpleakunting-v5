@extends('layouts.app')

@section('title', 'Tambah Jenis Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Jenis Simpanan</h1>
    <a href="{{ route('jenis-simpanan.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('jenis-simpanan.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kode_simpanan" class="form-label">Kode Simpanan</label>
                            <input type="text" class="form-control @error('kode_simpanan') is-invalid @enderror" 
                                id="kode_simpanan" name="kode_simpanan" value="{{ old('kode_simpanan') }}" required>
                            @error('kode_simpanan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama_simpanan" class="form-label">Nama Simpanan</label>
                            <input type="text" class="form-control @error('nama_simpanan') is-invalid @enderror" 
                                id="nama_simpanan" name="nama_simpanan" value="{{ old('nama_simpanan') }}" required>
                            @error('nama_simpanan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipe" class="form-label">Tipe Simpanan</label>
                            <select class="form-select @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
                                <option value="pokok" {{ old('tipe') == 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
                                <option value="wajib" {{ old('tipe') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
                                <option value="sukarela" {{ old('tipe') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
                                <option value="deposito" {{ old('tipe') == 'deposito' ? 'selected' : '' }}>Deposito</option>
                            </select>
                            @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bunga_pertahun" class="form-label">Bunga/Tahun (%)</label>
                            <input type="number" step="0.01" class="form-control @error('bunga_pertahun') is-invalid @enderror" 
                                id="bunga_pertahun" name="bunga_pertahun" value="{{ old('bunga_pertahun', 0) }}">
                            @error('bunga_pertahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Hanya untuk sukarela/deposito berbunga</small>
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary">Pengaturan Akun COA</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="akun_simpanan" class="form-label">Akun Simpanan (Kewajiban) <span class="text-danger">*</span></label>
                            <select class="form-select @error('akun_simpanan') is-invalid @enderror" 
                                id="akun_simpanan" name="akun_simpanan" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKewajiban as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_simpanan') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_simpanan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="akun_bunga" class="form-label">Akun Biaya Bunga</label>
                            <select class="form-select @error('akun_bunga') is-invalid @enderror" 
                                id="akun_bunga" name="akun_bunga">
                                <option value="">-- Pilih Akun (Opsional) --</option>
                                @foreach($akunBiaya as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_bunga') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_bunga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Untuk simpanan berbunga</small>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span data-feather="save"></span> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>feather.replace();</script>
@endpush
