@extends('layouts.app')

@section('title', 'Edit Anggota - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Anggota</h1>
    <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('anggota.update', $anggota->id_anggota) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">No. Anggota</label>
                        <input type="text" class="form-control" value="{{ $anggota->no_anggota }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Daftar</label>
                        <input type="date" class="form-control" value="{{ $anggota->tanggal_daftar->format('Y-m-d') }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required id="statusSelect">
                            <option value="aktif" {{ old('status', $anggota->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non_aktif" {{ old('status', $anggota->status) == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="keluar" {{ old('status', $anggota->status) == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row" id="tanggalKeluarRow" style="{{ $anggota->status == 'keluar' ? '' : 'display:none;' }}">
                <div class="col-md-4 offset-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror" 
                               value="{{ old('tanggal_keluar', $anggota->tanggal_keluar?->format('Y-m-d')) }}">
                        @error('tanggal_keluar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                               value="{{ old('nik', $anggota->nik) }}" maxlength="16" pattern="[0-9]{16}" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
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
                            <option value="L" {{ old('jenis_kelamin', $anggota->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $anggota->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                               value="{{ old('pekerjaan', $anggota->pekerjaan) }}">
                        @error('pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                          rows="3" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" 
                               value="{{ old('telepon', $anggota->telepon) }}">
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $anggota->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Foto Baru</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    @if($anggota->foto)
                    <div class="mb-3">
                        <label class="form-label">Foto Saat Ini</label>
                        <div>
                            <img src="{{ asset('storage/anggota/' . $anggota->foto) }}" alt="Foto Anggota" 
                                 class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('anggota.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <span data-feather="save"></span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
    
    document.getElementById('statusSelect').addEventListener('change', function() {
        var tanggalKeluarRow = document.getElementById('tanggalKeluarRow');
        if (this.value === 'keluar') {
            tanggalKeluarRow.style.display = '';
        } else {
            tanggalKeluarRow.style.display = 'none';
        }
    });
</script>
@endpush
