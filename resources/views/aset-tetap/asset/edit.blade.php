@extends('layouts.app')

@section('title', 'Edit Aset Tetap - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Data Aset Tetap</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('aset-tetap.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('aset-tetap.update', $asset->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kode_aset" class="form-label">Kode Aset</label>
                                <input type="text" class="form-control" id="kode_aset" value="{{ $asset->kode_aset }}" disabled>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="nama_aset" class="form-label">Nama Aset</label>
                                <input type="text" class="form-control @error('nama_aset') is-invalid @enderror" id="nama_aset" name="nama_aset" value="{{ old('nama_aset', $asset->nama_aset) }}" required>
                                @error('nama_aset')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kelompok_aset_id" class="form-label">Kelompok Aset</label>
                                <select class="form-select @error('kelompok_aset_id') is-invalid @enderror" id="kelompok_aset_id" name="kelompok_aset_id" required>
                                    <option value="">-- Pilih Kelompok --</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" data-umur="{{ $g->umur_ekonomis }}" {{ old('kelompok_aset_id', $asset->kelompok_aset_id) == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama_kelompok }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelompok_aset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
                                <input type="date" class="form-control @error('tanggal_perolehan') is-invalid @enderror" id="tanggal_perolehan" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', $asset->tanggal_perolehan) }}" required>
                                @error('tanggal_perolehan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="harga_perolehan" class="form-label">Harga Perolehan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_perolehan') is-invalid @enderror" id="harga_perolehan" name="harga_perolehan" value="{{ old('harga_perolehan', (int)$asset->harga_perolehan) }}" required min="0">
                                </div>
                                @error('harga_perolehan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nilai_residu" class="form-label">Nilai Residu</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('nilai_residu') is-invalid @enderror" id="nilai_residu" name="nilai_residu" value="{{ old('nilai_residu', (int)$asset->nilai_residu) }}" required min="0">
                                </div>
                                @error('nilai_residu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="umur_ekonomis_bulan" class="form-label">Umur Ekonomis (Bulan)</label>
                                <input type="number" class="form-control @error('umur_ekonomis_bulan') is-invalid @enderror" id="umur_ekonomis_bulan" name="umur_ekonomis_bulan" value="{{ old('umur_ekonomis_bulan', $asset->umur_ekonomis_bulan) }}" required min="1">
                                @error('umur_ekonomis_bulan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nilai Buku Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" value="{{ number_format($asset->nilai_buku_saat_ini, 0, ',', '.') }}" disabled>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kelompokSelect = document.getElementById('kelompok_aset_id');
        const umurInput = document.getElementById('umur_ekonomis_bulan');

        kelompokSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const umur = selectedOption.getAttribute('data-umur');
            // Only update if the user hasn't modified it manually or wants to sync
            if (umur) {
                umurInput.value = umur;
            }
        });
    });
</script>
@endsection
