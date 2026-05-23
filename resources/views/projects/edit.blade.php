@extends('layouts.app')

@section('title', 'Edit Proyek/Program - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Proyek / Program</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('projects.update', $project->id_project) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_unit_usaha') is-invalid @enderror" id="id_unit_usaha" name="id_unit_usaha" required>
                                <option value="">-- Pilih Unit Usaha --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ old('id_unit_usaha', $project->id_unit_usaha) == $u->id ? 'selected' : '' }}>
                                        {{ $u->nama_unit }} ({{ $u->cabang->nama_cabang ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_unit_usaha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kode_project" class="form-label">Kode Proyek / Program <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_project') is-invalid @enderror" id="kode_project" name="kode_project" value="{{ old('kode_project', $project->kode_project) }}" required>
                            @error('kode_project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama_project" class="form-label">Nama Proyek / Program <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_project') is-invalid @enderror" id="nama_project" name="nama_project" value="{{ old('nama_project', $project->nama_project) }}" required>
                            @error('nama_project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Aktif" {{ old('status', $project->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Selesai" {{ old('status', $project->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $project->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
