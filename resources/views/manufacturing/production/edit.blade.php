@extends('layouts.app')

@section('title', 'Edit Produksi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Produksi: {{ $produksi->no_produksi }}</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('manufacturing.production.update', $produksi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pilih BOM / Resep</label>
                            <select name="bom_id" id="bom_id" class="form-select" required>
                                <option value="">-- Pilih BOM --</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->id }}" 
                                        {{ $produksi->bom_id == $bom->id ? 'selected' : '' }}
                                        data-qty="{{ $bom->kuantitas_hasil }}" 
                                        data-product="{{ $bom->barangJadi->nama_barang ?? '' }}">
                                        {{ $bom->nama_bom }} (Output: {{ $bom->kuantitas_hasil }} {{ $bom->barangJadi->nama_barang ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Produksi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d', strtotime($produksi->tanggal)) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kuantitas Produksi</label>
                            <input type="number" name="kuantitas_produksi" id="kuantitas_produksi" class="form-control" min="1" value="{{ $produksi->kuantitas_produksi }}" required>
                            <small class="text-muted" id="bom_hint"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cabang</label>
                            <select name="id_cabang" class="form-select">
                                <option value="">-- Pilih Cabang (Opsional) --</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}" {{ $produksi->id_cabang == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ $produksi->keterangan }}</textarea>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i> Perubahan kuantitas produksi akan secara otomatis menyinkronkan kembali stok bahan baku dan barang jadi.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update & Sinkronkan</button>
                    <a href="{{ route('manufacturing.production.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">Rincian Material Terpakai Saat Ini</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Material</th>
                            <th class="text-end">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->details as $det)
                        <tr>
                            <td>{{ $det->material->nama_barang ?? 'Unknown' }}</td>
                            <td class="text-end">{{ number_format($det->kuantitas_digunakan, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('bom_id').addEventListener('change', function() {
        updateHint();
    });
    
    function updateHint() {
        const sel = document.getElementById('bom_id');
        const opt = sel.options[sel.selectedIndex];
        if(opt.value) {
            document.getElementById('bom_hint').innerText = `Produksi ini akan menghasilkan kelipatan dari ${opt.dataset.qty} ${opt.dataset.product}`;
        } else {
            document.getElementById('bom_hint').innerText = '';
        }
    }
    
    updateHint();
</script>
@endpush
