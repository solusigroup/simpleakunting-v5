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

    <!-- Searchable Dropdown Styles -->
    <style>
        .searchable-select { position: relative; width: 100%; }
        .searchable-select-trigger { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0.5rem 1rem; font-size: 0.875rem; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; color: #333; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-height: 40px; position: relative; }
        .searchable-select-trigger:hover { border-color: #8b5cf6; background: #fcfaff; }
        .searchable-select-trigger.open { border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); background: #fff; z-index: 1051; }
        .searchable-select-trigger .trigger-text { flex: 1; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 10px; }
        .searchable-select-trigger .trigger-text.placeholder { color: #999; }
        .searchable-select-trigger .trigger-chevron { margin-left: 8px; transition: transform 0.3s; flex-shrink: 0; color: #666; }
        .searchable-select-trigger.open .trigger-chevron { transform: rotate(180deg); color: #8b5cf6; }
        
        .searchable-select-dropdown { display: none; position: absolute; top: calc(100% + 8px); left: 0; min-width: 100%; width: max-content; max-width: 450px; background: #fff; border: 1px solid #dee2e6; border-radius: 12px; box-shadow: 0 15px 50px rgba(0,0,0,0.2); z-index: 9999; overflow: hidden; }
        .searchable-select-dropdown.show { display: block; }
        
        .searchable-select-search { padding: 12px; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
        .searchable-select-search input { width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; outline: none; transition: all 0.2s; background: #fff; color: #333; }
        .searchable-select-search input:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); }
        
        .searchable-select-options { max-height: 260px; overflow-y: auto; padding: 6px 0; scrollbar-width: thin; }
        .searchable-select-options::-webkit-scrollbar { width: 6px; }
        .searchable-select-options::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        
        .searchable-select-option { padding: 10px 16px; cursor: pointer; font-size: 0.875rem; color: #444; transition: all 0.15s; white-space: nowrap; }
        .searchable-select-option:hover { background: #f5f3ff; color: #8b5cf6; padding-left: 20px; }
        .searchable-select-option.selected { background: #f5f3ff; color: #8b5cf6; font-weight: 600; }
        .searchable-select-option.hidden { display: none; }
        .searchable-select-empty { padding: 20px; text-align: center; color: #999; font-size: 0.875rem; }

        .table-responsive { overflow: visible !important; }
        td { position: relative; }
    </style>

    @php
        // Pre-build options in PHP to ensure data integrity and bypass JS mapping issues
        $barangOptionsHtml = '';
        foreach($barang as $b) {
            $label = ($b->kode_barang ?? '') . ' - ' . ($b->nama_barang ?? '');
            $fullLabel = $label . ' (Stok: ' . ($b->stok_saat_ini ?? 0) . ')';
            $barcode = $b->barcode ?? '';
            $kode = $b->kode_barang ?? '';
            
            $barangOptionsHtml .= '<div class="searchable-select-option" 
                data-value="'.$b->id_barang.'" 
                data-harga="'.($b->harga_jual ?? 0).'" 
                data-barcode="'.htmlspecialchars($barcode).'" 
                data-kode="'.htmlspecialchars($kode).'" 
                data-label="'.htmlspecialchars($label).'">'.htmlspecialchars($fullLabel).'</div>';
        }
    @endphp

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
    const barangOptionsHtml = `{!! $barangOptionsHtml !!}`;
    const rawBarangData = @json($barang);
    let barangData = Array.isArray(rawBarangData) ? rawBarangData : Object.values(rawBarangData || {});

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        const idx = rowCount;
        const html = `
            <tr id="row_${idx}">
                <td>
                    <div class="searchable-select" id="ss_${idx}">
                        <input type="hidden" name="details[${idx}][id_barang]" id="ss_input_${idx}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${idx}">
                            <div class="trigger-text placeholder">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span>Cari atau Pilih Barang...</span>
                            </div>
                            <svg class="trigger-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="searchable-select-dropdown" id="ss_dropdown_${idx}">
                            <div class="searchable-select-search">
                                <input type="text" placeholder="Ketik kode atau nama barang..." id="ss_search_${idx}" autocomplete="off">
                            </div>
                            <div class="searchable-select-options" id="ss_options_${idx}">
                                ${barangOptionsHtml || '<div class="searchable-select-empty">Data Barang Kosong</div>'}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" id="harga_${idx}" readonly>
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
        rowCount++;
    }

    function initSearchableSelect(idx) {
        const trigger = document.getElementById(`ss_trigger_${idx}`);
        const dropdown = document.getElementById(`ss_dropdown_${idx}`);
        const searchInput = document.getElementById(`ss_search_${idx}`);
        const optionsContainer = document.getElementById(`ss_options_${idx}`);
        const hiddenInput = document.getElementById(`ss_input_${idx}`);

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close others
            document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('show');
                    d.closest('.searchable-select').querySelector('.searchable-select-trigger').classList.remove('open');
                }
            });
            const isOpen = dropdown.classList.toggle('show');
            trigger.classList.toggle('open', isOpen);
            if (isOpen) {
                searchInput.value = '';
                filterOptions(idx, '');
                setTimeout(() => searchInput.focus(), 50);
            }
        });

        searchInput.addEventListener('input', function() {
            filterOptions(idx, this.value);
        });

        optionsContainer.addEventListener('click', function(e) {
            const opt = e.target.closest('.searchable-select-option');
            if (!opt) return;
            e.stopPropagation();

            selectOption(idx, opt);
        });
    }

    function selectOption(idx, opt) {
        const trigger = document.getElementById(`ss_trigger_${idx}`);
        const dropdown = document.getElementById(`ss_dropdown_${idx}`);
        const hiddenInput = document.getElementById(`ss_input_${idx}`);

        hiddenInput.value = opt.dataset.value;
        trigger.querySelector('.trigger-text').innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>${opt.dataset.label}</span>
        `;
        trigger.querySelector('.trigger-text').classList.remove('placeholder');
        
        dropdown.classList.remove('show');
        trigger.classList.remove('open');

        // Update Price
        document.getElementById(`harga_${idx}`).value = opt.dataset.harga || 0;
        hitungSubtotal(idx);
    }

    function filterOptions(idx, query) {
        const container = document.getElementById(`ss_options_${idx}`);
        const options = container.querySelectorAll('.searchable-select-option');
        const q = query.toLowerCase().trim();
        let count = 0;

        options.forEach(opt => {
            const label = opt.dataset.label.toLowerCase();
            const barcode = (opt.dataset.barcode || '').toLowerCase();
            const kode = (opt.dataset.kode || '').toLowerCase();
            
            if (!q || label.includes(q) || barcode.includes(q) || kode.includes(q)) {
                opt.classList.remove('hidden');
                count++;
            } else {
                opt.classList.add('hidden');
            }
        });

        let emptyMsg = container.querySelector('.searchable-select-empty');
        if (count === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'searchable-select-empty';
                emptyMsg.textContent = 'Barang tidak ditemukan';
                container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = '';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
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

    document.addEventListener('click', () => {
        document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
            d.classList.remove('show');
            d.closest('.searchable-select').querySelector('.searchable-select-trigger').classList.remove('open');
        });
    });

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
                    
                    const dummyOpt = {
                        dataset: {
                            value: b.id_barang,
                            label: `${b.kode_barang} - ${b.nama_barang}`,
                            harga: b.harga_jual || 0
                        }
                    };
                    selectOption(targetIdx, dummyOpt);
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
