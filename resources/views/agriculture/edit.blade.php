@extends('layouts.app')

@section('title', 'Edit Aset Biologis')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Aset Biologis</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('agriculture.update', $asset->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Aset</label>
                            <input type="text" class="form-control" value="{{ $asset->kode_aset }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Aset</label>
                            <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset', $asset->nama_aset) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="tanaman" {{ old('jenis', $asset->jenis) == 'tanaman' ? 'selected' : '' }}>Tanaman</option>
                                <option value="hewan" {{ old('jenis', $asset->jenis) == 'hewan' ? 'selected' : '' }}>Hewan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan', $asset->tanggal_perolehan) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Umur (Bulan)</label>
                            <input type="number" name="umur_bulan" class="form-control" value="{{ old('umur_bulan', $asset->umur_bulan) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $asset->lokasi) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Perolehan (Cost)</label>
                            <input type="number" step="0.01" name="nilai_perolehan" class="form-control" value="{{ old('nilai_perolehan', $asset->nilai_perolehan) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Wajar (Fair Value)</label>
                            <input type="number" step="0.01" name="nilai_wajar" class="form-control" value="{{ old('nilai_wajar', $asset->nilai_wajar) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estimasi Biaya Jual</label>
                        <input type="number" step="0.01" name="estimasi_biaya_jual" class="form-control" value="{{ old('estimasi_biaya_jual', $asset->estimasi_biaya_jual) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cabang (Opsional)</label>
                        <select name="id_cabang" class="form-select">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}" {{ old('id_cabang', $asset->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('agriculture.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
