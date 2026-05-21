@extends('layouts.app')

@section('title', 'Buat Faktur Penjualan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Faktur Penjualan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <!-- Custom styles using global searchable-select.css -->

    <form action="{{ route('penjualan.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Info Faktur -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Info Faktur</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">No Faktur</label>
                            <input type="text" class="form-control" name="no_faktur" value="{{ $noFaktur }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal_faktur" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <select class="form-select" name="id_pelanggan" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cabang <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_cabang" name="id_cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabang as $c)
                                    <option value="{{ $c->id }}" {{ old('id_cabang', auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_unit_usaha" name="id_unit_usaha" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($unitUsaha as $u)
                                    <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}">{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Pembayaran</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Metode</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required onchange="toggleAkunKas()">
                                <option value="Tunai">Tunai</option>
                                <option value="Kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="mb-3" id="div_akun_kas">
                            <label class="form-label">Akun Kas/Bank</label>
                            <select class="form-select" name="akun_kas_bank">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total -->
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <h3>Total Faktur</h3>
                        <h1 class="display-4 fw-bold text-primary" id="display_total">Rp 0</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Detail Barang</span>
                <div class="input-group w-50">
                    <span class="input-group-text"><i class="bi bi-upc-scan"></i> SCAN BARCODE</span>
                    <input type="text" class="form-control" id="scan_barcode" placeholder="Arahkan kursor kesini untuk scan..." autofocus>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="45%">Barang</th>
                                <th width="15%">Harga</th>
                                <th width="10%">Qty</th>
                                <th width="20%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="container_barang">
                            <!-- Rows -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="p-3">
                                    <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+ Tambah Baris</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">SIMPAN TRANSAKSI</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let rowCount = 0;
    const rawBarangData = @json($barang);
    let barangData = Array.isArray(rawBarangData) ? rawBarangData : Object.values(rawBarangData || {});

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        const idx = rowCount;
        let optionsHtml = barangData.map(b => {
            const label = `${b.kode_barang || ''} - ${b.nama_barang || ''}`;
            const formatNum = (val) => new Intl.NumberFormat('id-ID').format(val || 0);
            
            const stok = formatNum(b.stok_saat_ini);
            const beli = formatNum(b.harga_beli);
            const jual = formatNum(b.harga_jual);
            const barcodeStr = b.barcode ? String(b.barcode).trim() : '';
            const barcode = barcodeStr !== '' ? barcodeStr : '-';
            const satuanStr = b.satuan ? String(b.satuan).trim() : '';
            const satuan = satuanStr !== '' ? satuanStr : '-';

            return `<div class="searchable-select-option" 
                data-value="${b.id_barang}" 
                data-harga="${b.harga_jual || 0}" 
                data-barcode="${b.barcode || ''}" 
                data-kode="${b.kode_barang || ''}" 
                data-search="${b.barcode || ''} ${b.kode_barang || ''}"
                data-label="${label}">
                <div class="d-flex justify-content-between align-items-center w-100" style="gap: 15px;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; color: var(--color-text, #333);">
                            <span class="text-primary" style="font-weight: 700;">${b.kode_barang || ''}</span> - ${b.nama_barang || ''}
                        </div>
                        <div class="text-muted style-barcode-satuan" style="font-size: 0.72rem; margin-top: 3px;">
                            Barcode: <span style="color: #666; font-weight: 500;">${barcode}</span> | Satuan: <span style="color: #666; font-weight: 500;">${satuan}</span>
                        </div>
                    </div>
                    <div class="text-end" style="font-size: 0.72rem; line-height: 1.35; flex-shrink: 0; min-width: 140px; border-left: 1px solid #f0f0f0; padding-left: 10px;">
                        <div>Stok: <strong class="text-success">${stok}</strong></div>
                        <div class="text-muted" style="font-size: 0.68rem; margin-top: 1px;">Beli: Rp ${beli} | Jual: Rp ${jual}</div>
                    </div>
                </div>
            </div>`;
        }).join('');

        const html = `
            <tr id="row_${idx}">
                <td>
                    <div class="searchable-select" id="ss_${idx}">
                        <input type="hidden" name="details[${idx}][id_barang]" id="ss_input_${idx}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${idx}">
                            <span class="trigger-text placeholder">🔍 Cari atau pilih barang...</span>
                            <svg class="trigger-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="searchable-select-dropdown" id="ss_dropdown_${idx}">
                            <div class="searchable-select-search">
                                <input type="text" placeholder="Ketik kode atau nama barang..." id="ss_search_${idx}" autocomplete="off">
                            </div>
                            <div class="searchable-select-options" id="ss_options_${idx}">
                                ${optionsHtml || '<div class="searchable-select-empty">Data Barang Kosong</div>'}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${idx}][harga]" id="harga_${idx}" onkeyup="hitungSubtotal(${idx})" onchange="hitungSubtotal(${idx})" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${idx}][kuantitas]" id="qty_${idx}" min="1" value="1" onkeyup="hitungSubtotal(${idx})" onchange="hitungSubtotal(${idx})">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="subtotal_display_${idx}" readonly>
                    <input type="hidden" class="subtotal-input" id="subtotal_${idx}" value="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${idx})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_barang').insertAdjacentHTML('beforeend', html);
        initSearchableSelect(idx);
        
        // Listen to change event from searchable-select.js
        document.getElementById(`ss_input_${idx}`).addEventListener('change', function(e) {
            const val = this.value;
            if (!val) return;
            const opt = document.querySelector(`#ss_options_${idx} .searchable-select-option[data-value="${val}"]`);
            if (opt) {
                document.getElementById(`harga_${idx}`).value = opt.dataset.harga || 0;
                hitungSubtotal(idx);
            }
        });
        
        rowCount++;
    }

    function hitungSubtotal(idx) {
        const h = parseFloat(document.getElementById(`harga_${idx}`).value) || 0;
        const q = parseFloat(document.getElementById(`qty_${idx}`).value) || 0;
        const sub = h * q;
        document.getElementById(`subtotal_${idx}`).value = sub;
        document.getElementById(`subtotal_display_${idx}`).value = formatRupiah(sub);
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(i => total += parseFloat(i.value) || 0);
        document.getElementById('display_total').innerText = formatRupiah(total);
    }

    function hapusBaris(idx) {
        const r = document.getElementById(`row_${idx}`);
        if (r) r.remove();
        hitungTotal();
    }

    function toggleAkunKas() {
        const m = document.getElementById('metode_pembayaran').value;
        const d = document.getElementById('div_akun_kas');
        if (m === 'Tunai') d.style.display = 'block';
        else d.style.display = 'none';
    }

    // Barcode Logic
    document.getElementById('scan_barcode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code) {
                const b = barangData.find(x => x.barcode === code || x.kode_barang === code);
                if (b) {
                    let targetIdx = -1;
                    for (let i=0; i<rowCount; i++) {
                        if (document.getElementById(`ss_input_${i}`) && document.getElementById(`ss_input_${i}`).value == "") {
                            targetIdx = i; break;
                        }
                    }
                    if (targetIdx === -1) { tambahBaris(); targetIdx = rowCount-1; }
                    
                    const input = document.getElementById(`ss_input_${targetIdx}`);
                    input.value = b.id_barang;
                    
                    const triggerText = document.querySelector(`#ss_trigger_${targetIdx} .trigger-text`);
                    triggerText.textContent = `${b.kode_barang} - ${b.nama_barang}`;
                    triggerText.classList.remove('placeholder');
                    
                    document.getElementById(`harga_${targetIdx}`).value = b.harga_jual || 0;
                    hitungSubtotal(targetIdx);
                } else {
                    alert('Barang tidak ditemukan!');
                }
                this.value = '';
            }
        }
    });

    // Init
    tambahBaris();
    toggleAkunKas();
</script>
@endpush
