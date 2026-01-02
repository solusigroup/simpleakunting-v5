@extends('layouts.app')

@section('title', 'Buat Bill of Materials')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buat BOM Baru</h1>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('manufacturing.bom.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama BOM</label>
                            <input type="text" name="nama_bom" class="form-control" required placeholder="Contoh: Resep Meja Belajar">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barang Jadi (Output)</label>
                            <select name="barang_jadi_id" class="form-select" required>
                                <option value="">-- Pilih Barang Jadi --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id_barang }}">{{ $p->kode_barang }} - {{ $p->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kuantitas Hasil</label>
                        <input type="number" name="kuantitas_hasil" class="form-control" value="1" required>
                        <small class="text-muted">Jumlah barang jadi yang dihasilkan dari resep ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
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
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm mb-3" id="addMaterial">Tambah Material</button>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan BOM</button>
                        <a href="{{ route('manufacturing.bom.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('addMaterial').addEventListener('click', function() {
        const table = document.getElementById('materialTable').getElementsByTagName('tbody')[0];
        const rowCount = table.rows.length;
        const row = table.insertRow(rowCount);
        
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
    });
</script>
@endsection
