@extends('layouts.app')

@section('title', 'Tambah Anggota - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Anggota Baru</h1>
    <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('anggota.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">No. Anggota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $noAnggota }}" readonly>
                        <small class="text-muted">Nomor anggota dibuat otomatis</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Daftar <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_daftar" class="form-control @error('tanggal_daftar') is-invalid @enderror" 
                               value="{{ old('tanggal_daftar', date('Y-m-d')) }}" required>
                        @error('tanggal_daftar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                               value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}" 
                               placeholder="16 digit NIK" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">Pilih...</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" 
                               value="{{ old('pekerjaan') }}">
                        @error('pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                          rows="3" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx">
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/jpg">
                <small class="text-muted">Format: JPG, PNG. Maks 2MB</small>
                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('anggota.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span data-feather="save"></span> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
