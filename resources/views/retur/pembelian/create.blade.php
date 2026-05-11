@extends('layouts.app')

@section('title', 'Buat Retur Pembelian')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buat Retur Pembelian</h1>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Pilih Faktur Pembelian</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('retur.pembelian.create') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Cari Nomor Faktur / Pemasok</label>
                <select name="id_pembelian" class="form-select select2" required>
                    <option value="">-- Pilih Faktur --</option>
                    @foreach($allPembelian as $p)
                        <option value="{{ $p->id_pembelian }}" {{ (request('id_pembelian') == $p->id_pembelian) ? 'selected' : '' }}>
                            {{ $p->no_faktur_pembelian }} | {{ $p->pemasok->nama_pemasok }} | Rp {{ number_format($p->total, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Muat Data Faktur</button>
            </div>
        </form>
    </div>
</div>

@if($pembelian)
<form action="{{ route('retur.pembelian.store') }}" method="POST">
    @csrf
    <input type="hidden" name="id_pembelian" value="{{ $pembelian->id_pembelian }}">
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">Informasi Retur</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Retur</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Alasan retur..."></textarea>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Nilai Retur:</span>
                        <span id="display-total" class="fw-bold">Rp 0</span>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 mt-2 text-dark fw-bold">Simpan Retur</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Detail Barang (Faktur: {{ $pembelian->no_faktur_pembelian }})</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Qty Beli</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center" style="width: 120px;">Qty Retur</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembelian->details as $index => $detail)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $detail->barang->nama_barang }}</div>
                                    <small class="text-muted">{{ $detail->barang->kode_barang }}</small>
                                    <input type="hidden" name="items[{{ $index }}][id_barang]" value="{{ $detail->id_barang }}">
                                    <input type="hidden" name="items[{{ $index }}][harga]" value="{{ $detail->harga }}" class="item-harga">
                                </td>
                                <td class="text-center">{{ $detail->kuantitas }} {{ $detail->barang->satuan }}</td>
                                <td class="text-end">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][qty_retur]" 
                                           class="form-control form-control-sm text-center item-qty" 
                                           value="0" min="0" max="{{ $detail->kuantitas }}" 
                                           onchange="calculateSubtotal(this)">
                                </td>
                                <td class="text-end">
                                    <span class="item-subtotal">Rp 0</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endif

@endsection

@push('scripts')
<script>
    function calculateSubtotal(input) {
        let row = input.closest('tr');
        let harga = parseFloat(row.querySelector('.item-harga').value);
        let qty = parseFloat(input.value) || 0;
        let subtotal = harga * qty;
        
        row.querySelector('.item-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-qty').forEach(input => {
            let row = input.closest('tr');
            let harga = parseFloat(row.querySelector('.item-harga').value);
            let qty = parseFloat(input.value) || 0;
            total += (harga * qty);
        });
        
        document.getElementById('display-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush
