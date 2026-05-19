@extends('layouts.app')

@section('title', 'Edit Bill of Materials')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit BOM: {{ $bom->kode_bom }}</h1>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('manufacturing.bom.update', $bom->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama BOM</label>
                            <input type="text" name="nama_bom" class="form-control" value="{{ old('nama_bom', $bom->nama_bom) }}" required placeholder="Contoh: Resep Meja Belajar">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barang Jadi (Output)</label>
                            <select name="barang_jadi_id" class="form-select" required>
                                <option value="">-- Pilih Barang Jadi --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id_barang }}" {{ $p->id_barang == $bom->barang_jadi_id ? 'selected' : '' }}>
                                        {{ $p->kode_barang }} - {{ $p->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kuantitas Hasil</label>
                        <input type="number" name="kuantitas_hasil" class="form-control" value="{{ old('kuantitas_hasil', $bom->kuantitas_hasil) }}" required>
                        <small class="text-muted">Jumlah barang jadi yang dihasilkan dari resep ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $bom->deskripsi) }}</textarea>
                    </div>

                    <h5 class="mt-4">Bahan Baku (Raw Materials)</h5>
                    <table class="table table-bordered" id="materialTable">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Kuantitas Dibutuhkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bom->details as $index => $detail)
                            <tr>
                                <td>
                                    <select name="details[{{ $index }}][material_id]" class="form-select" required>
                                        <option value="">-- Pilih Material --</option>
                                        @foreach($materials as $m)
                                            <option value="{{ $m->id_barang }}" {{ $m->id_barang == $detail->material_id ? 'selected' : '' }}>
                                                {{ $m->kode_barang }} - {{ $m->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="details[{{ $index }}][kuantitas]" class="form-control" value="{{ $detail->kuantitas }}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" onclick="this.closest('tr').remove()" {{ count($bom->details) <= 1 ? 'disabled' : '' }}>Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>
                                    <select name="details[0][material_id]" class="form-select" required>
                                        <option value="">-- Pilih Material --</option>
                                        @foreach($materials as $m)
                                            <option value="{{ $m->id_barang }}">{{ $m->kode_barang }} - {{ $m->nama_barang }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.0001" name="details[0][kuantitas]" class="form-control" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled>Hapus</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm mb-3" id="addMaterial">Tambah Material</button>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan BOM</button>
                        <a href="{{ route('manufacturing.bom.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let rowCount = {{ max(1, count($bom->details)) }};
    
    document.getElementById('addMaterial').addEventListener('click', function() {
        const table = document.getElementById('materialTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow(table.rows.length);
        
        row.innerHTML = `
            <td>
                <select name="details[${rowCount}][material_id]" class="form-select" required>
                    <option value="">-- Pilih Material --</option>
                    @foreach($materials as $m)
                        <option value="{{ $m->id_barang }}">{{ $m->kode_barang }} - {{ $m->nama_barang }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.0001" name="details[${rowCount}][kuantitas]" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row" onclick="this.closest('tr').remove()">Hapus</button>
            </td>
        `;
        rowCount++;
        
        // Enable all remove buttons if there are more than 1 rows
        toggleRemoveButtons();
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            toggleRemoveButtons();
        }
    });

    function toggleRemoveButtons() {
        const table = document.getElementById('materialTable').getElementsByTagName('tbody')[0];
        const removeButtons = table.querySelectorAll('.remove-row');
        if (removeButtons.length <= 1) {
            removeButtons.forEach(btn => btn.setAttribute('disabled', 'disabled'));
        } else {
            removeButtons.forEach(btn => btn.removeAttribute('disabled'));
        }
    }
</script>
@endsection
