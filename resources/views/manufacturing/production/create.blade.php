@extends('layouts.app')

@section('title', 'Input Produksi Baru')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Input Produksi Baru</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('manufacturing.production.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih BOM / Resep</label>
                            <select name="bom_id" id="bom_id" class="form-select" required>
                                <option value="">-- Pilih BOM --</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->id }}" data-qty="{{ $bom->kuantitas_hasil }}" data-product="{{ $bom->barangJadi->nama_barang ?? '' }}">
                                        {{ $bom->nama_bom }} (Output: {{ $bom->kuantitas_hasil }} {{ $bom->barangJadi->nama_barang ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Produksi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kuantitas Produksi</label>
                            <input type="number" name="kuantitas_produksi" id="kuantitas_produksi" class="form-control" min="1" required>
                            <small class="text-muted" id="bom_hint"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cabang</label>
                            <select name="id_cabang" class="form-select">
                                <option value="">-- Pilih Cabang (Opsional) --</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i> Stok bahan baku akan otomatis berkurang dan stok barang jadi akan bertambah saat produksi disimpan.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan & Proses Produksi</button>
                    <a href="{{ route('manufacturing.production.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
