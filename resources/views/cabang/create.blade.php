@extends('layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Cabang Baru</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('cabang.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Kode Cabang</label>
                        <input type="text" name="kode_cabang" class="form-control @error('kode_cabang') is-invalid @enderror" 
                               value="{{ old('kode_cabang') }}" required placeholder="CBG-001">
                        @error('kode_cabang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Cabang</label>
                        <input type="text" name="nama_cabang" class="form-control @error('nama_cabang') is-invalid @enderror" 
                               value="{{ old('nama_cabang') }}" required placeholder="Cabang Jakarta">
                        @error('nama_cabang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('cabang.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
