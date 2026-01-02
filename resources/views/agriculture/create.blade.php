@extends('layouts.app')

@section('title', 'Tambah Aset Biologis')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Aset Biologis</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('agriculture.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Aset</label>
                            <input type="text" name="nama_aset" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="tanaman">Tanaman</option>
                                <option value="hewan">Hewan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Umur (Bulan)</label>
                            <input type="number" name="umur_bulan" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nilai Perolehan (Cost)</label>
                            <input type="number" step="0.01" name="nilai_perolehan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nilai Wajar Awal (Fair Value)</label>
                            <input type="number" step="0.01" name="nilai_wajar" class="form-control" required>
                            <small class="text-muted">Biasanya sama dengan Cost saat pembelian.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estimasi Biaya Jual</label>
                        <input type="number" step="0.01" name="estimasi_biaya_jual" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cabang (Opsional)</label>
                        <select name="id_cabang" class="form-select">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Aset</button>
                    <a href="{{ route('agriculture.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
