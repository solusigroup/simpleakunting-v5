@extends('layouts.app')

@section('title', 'Edit Unit Usaha - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Unit Usaha</h1>
        <a href="{{ route('unit-usaha.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('unit-usaha.update', $unit->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_cabang') is-invalid @enderror" id="id_cabang" name="id_cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabang as $c)
                                    <option value="{{ $c->id }}" {{ old('id_cabang', $unit->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                            @error('id_cabang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kode_unit" class="form-label">Kode Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_unit') is-invalid @enderror" id="kode_unit" name="kode_unit" value="{{ old('kode_unit', $unit->kode_unit) }}" required>
                            @error('kode_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama_unit" class="form-label">Nama Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_unit') is-invalid @enderror" id="nama_unit" name="nama_unit" value="{{ old('nama_unit', $unit->nama_unit) }}" required>
                            @error('nama_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                            <select class="form-select @error('jenis_usaha') is-invalid @enderror" id="jenis_usaha" name="jenis_usaha">
                                <option value="">-- Pilih --</option>
                                @php $ju = old('jenis_usaha', $unit->jenis_usaha); @endphp
                                <option value="dagang" {{ $ju == 'dagang' ? 'selected' : '' }}>Dagang</option>
                                <option value="jasa" {{ $ju == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="simpan_pinjam" {{ $ju == 'simpan_pinjam' ? 'selected' : '' }}>Simpan Pinjam</option>
                                <option value="manufaktur" {{ $ju == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                <option value="pertanian" {{ $ju == 'pertanian' ? 'selected' : '' }}>Pertanian</option>
                            </select>
                            @error('jenis_usaha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
