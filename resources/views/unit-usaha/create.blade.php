@extends('layouts.app')

@section('title', 'Tambah Unit Usaha - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Unit Usaha</h1>
        <a href="{{ route('unit-usaha.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('unit-usaha.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_cabang') is-invalid @enderror" id="id_cabang" name="id_cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabang as $c)
                                    <option value="{{ $c->id }}" {{ old('id_cabang') == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                            @error('id_cabang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kode_unit" class="form-label">Kode Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_unit') is-invalid @enderror" id="kode_unit" name="kode_unit" value="{{ old('kode_unit') }}" required>
                            @error('kode_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama_unit" class="form-label">Nama Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_unit') is-invalid @enderror" id="nama_unit" name="nama_unit" value="{{ old('nama_unit') }}" required>
                            @error('nama_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                            <select class="form-select @error('jenis_usaha') is-invalid @enderror" id="jenis_usaha" name="jenis_usaha">
                                <option value="">-- Pilih --</option>
                                <option value="dagang" {{ old('jenis_usaha') == 'dagang' ? 'selected' : '' }}>Dagang</option>
                                <option value="jasa" {{ old('jenis_usaha') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="simpan_pinjam" {{ old('jenis_usaha') == 'simpan_pinjam' ? 'selected' : '' }}>Simpan Pinjam</option>
                                <option value="manufaktur" {{ old('jenis_usaha') == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                <option value="pertanian" {{ old('jenis_usaha') == 'pertanian' ? 'selected' : '' }}>Pertanian</option>
                            </select>
                            @error('jenis_usaha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
