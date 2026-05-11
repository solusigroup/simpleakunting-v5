@extends('layouts.app')

@section('title', 'Buat Faktur Pembelian - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Faktur Pembelian</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('pembelian.index') }}" class="btn btn-sm btn-secondary">
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

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Informasi Faktur -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Info Faktur</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="no_faktur" class="form-label">No Faktur</label>
                            <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="{{ $noFaktur }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_faktur" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_faktur" name="tanggal_faktur" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_pemasok" class="form-label">Pemasok</label>
                            <select class="form-select" id="id_pemasok" name="id_pemasok" required>
                                <option value="">-- Pilih Pemasok --</option>
                                @foreach($pemasok as $p)
                                    <option value="{{ $p->id_pemasok }}">{{ $p->nama_pemasok }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_cabang" name="id_cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabang as $c)
                                    <option value="{{ $c->id }}" {{ old('id_cabang', auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_unit_usaha" name="id_unit_usaha" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($unitUsaha as $u)
                                    <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha') == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembayaran -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Pembayaran</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required onchange="toggleAkunKas()">
                                <option value="Tunai">Tunai</option>
                                <option value="Kredit">Kredit</option>
                            </select>
                        </div>
                        <div class="mb-3" id="div_akun_kas">
                            <label for="akun_kas_bank" class="form-label">Akun Kas/Bank</label>
                            <select class="form-select" id="akun_kas_bank" name="akun_kas_bank">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
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
                    <span class="input-group-text"><i class="bi bi-upc-scan"></i> Scan Barcode</span>
                    <input type="text" class="form-control" id="scan_barcode" placeholder="Scan barcode atau ketik kode barang disini..." autofocus>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="tabel_barang">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Barang</th>
                                <th width="15%">Harga Beli</th>
                                <th width="15%">Qty</th>
                                <th width="20%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="container_barang">
                            <!-- Rows will be added here via JS -->
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

        <div class="mt-3 mb-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Simpan Transaksi</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Ensure barangData is an array
    let barangData = {!! json_encode($barang) !!};
    let rowCount = 0;

    // Pre-calculate options HTML once for performance
    let globalOptionsHtml = '';
    if (Array.isArray(barangData)) {
        globalOptionsHtml = barangData.map(b => {
            const label = `${b.kode_barang} - ${b.nama_barang}`;
            return `<div class="searchable-select-option" 
                        data-value="${b.id_barang}" 
                        data-label="${b.kode_barang} - ${b.nama_barang}"
                        data-harga="${b.harga_beli || 0}"
                        data-barcode="${b.barcode || ''}"
                        data-kode="${b.kode_barang || ''}">${label}</div>`;
        }).join('');
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        const currentRow = rowCount;
        let html = `
            <tr id="row_${currentRow}">
                <td>
                    <div class="searchable-select" id="ss_${currentRow}">
                        <input type="hidden" name="details[${currentRow}][id_barang]" id="ss_input_${currentRow}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${currentRow}">
                            <div class="trigger-text placeholder">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.8;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span>Pilih Barang...</span>
                            </div>
                            <svg class="trigger-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="searchable-select-dropdown" id="ss_dropdown_${currentRow}">
                            <div class="searchable-select-search">
                                <input type="text" placeholder="Ketik kode atau nama barang..." id="ss_search_${currentRow}" autocomplete="off">
                            </div>
                            <div class="searchable-select-options" id="ss_options_${currentRow}">
                                ${globalOptionsHtml}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${currentRow}][harga_beli]" id="harga_${currentRow}" onchange="hitungSubtotal(${currentRow})" onkeyup="hitungSubtotal(${currentRow})" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${currentRow}][kuantitas]" id="qty_${currentRow}" min="1" value="1" onchange="hitungSubtotal(${currentRow})" onkeyup="hitungSubtotal(${currentRow})" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="subtotal_display_${currentRow}" readonly>
                    <input type="hidden" class="subtotal-input" id="subtotal_${currentRow}" value="0">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${currentRow})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_barang').insertAdjacentHTML('beforeend', html);
        initSearchableSelect(currentRow);
        rowCount++;
    }

    function initSearchableSelect(rowId) {
        const trigger = document.getElementById(`ss_trigger_${rowId}`);
        const dropdown = document.getElementById(`ss_dropdown_${rowId}`);
        const searchInput = document.getElementById(`ss_search_${rowId}`);
        const optionsContainer = document.getElementById(`ss_options_${rowId}`);
        const hiddenInput = document.getElementById(`ss_input_${rowId}`);

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
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
                filterOptions(rowId, '');
                setTimeout(() => searchInput.focus(), 50);
            }
        });

        searchInput.addEventListener('input', function(e) {
            e.stopPropagation();
            filterOptions(rowId, this.value);
        });

        optionsContainer.addEventListener('click', function(e) {
            const option = e.target.closest('.searchable-select-option');
            if (!option) return;
            e.stopPropagation();

            selectOption(rowId, option);
        });
    }

    function selectOption(rowId, option) {
        const trigger = document.getElementById(`ss_trigger_${rowId}`);
        const dropdown = document.getElementById(`ss_dropdown_${rowId}`);
        const hiddenInput = document.getElementById(`ss_input_${rowId}`);
        const optionsContainer = document.getElementById(`ss_options_${rowId}`);

        optionsContainer.querySelectorAll('.selected').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');

        hiddenInput.value = option.dataset.value;
        const triggerText = trigger.querySelector('.trigger-text');
        triggerText.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>${option.dataset.label}</span>
        `;
        triggerText.classList.remove('placeholder');

        dropdown.classList.remove('show');
        trigger.classList.remove('open');

        // Update Price
        const harga = option.dataset.harga || 0;
        document.getElementById(`harga_${rowId}`).value = harga;
        hitungSubtotal(rowId);
    }

    function filterOptions(rowId, query) {
        const optionsContainer = document.getElementById(`ss_options_${rowId}`);
        const options = optionsContainer.querySelectorAll('.searchable-select-option');
        const q = query.toLowerCase().trim();
        let visibleCount = 0;

        options.forEach(option => {
            const label = option.textContent.toLowerCase();
            const barcode = (option.dataset.barcode || '').toLowerCase();
            if (!q || label.includes(q) || barcode.includes(q)) {
                option.classList.remove('hidden');
                visibleCount++;
            } else {
                option.classList.add('hidden');
            }
        });

        let emptyMsg = optionsContainer.querySelector('.searchable-select-empty');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'searchable-select-empty';
                emptyMsg.textContent = 'Barang tidak ditemukan';
                optionsContainer.appendChild(emptyMsg);
            }
            emptyMsg.style.display = '';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
    }

    document.addEventListener('click', function() {
        document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
            d.classList.remove('show');
            d.closest('.searchable-select').querySelector('.searchable-select-trigger').classList.remove('open');
        });
    });

    // Barcode Scanner Logic
    document.getElementById('scan_barcode').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let code = this.value.trim();
            if (code) {
                processBarcode(code);
                this.value = '';
            }
        }
    });

    function processBarcode(code) {
        if (!Array.isArray(barangData)) return;
        let barang = barangData.find(b => (b.barcode === code) || (b.kode_barang === code));
        
        if (barang) {
            let existingRow = -1;
            for (let i = 0; i < rowCount; i++) {
                let input = document.getElementById(`ss_input_${i}`);
                if (input && input.value == barang.id_barang) {
                    existingRow = i;
                    break;
                }
            }

            if (existingRow !== -1) {
                let qtyInput = document.getElementById(`qty_${existingRow}`);
                qtyInput.value = parseInt(qtyInput.value) + 1;
                hitungSubtotal(existingRow);
            } else {
                let emptyRow = -1;
                for (let i = 0; i < rowCount; i++) {
                    let input = document.getElementById(`ss_input_${i}`);
                    if (input && input.value === "") {
                        emptyRow = i;
                        break;
                    }
                }

                if (emptyRow === -1) {
                    tambahBaris();
                    emptyRow = rowCount - 1;
                }

                // Programmatically select the option
                const option = document.querySelector(`#ss_options_${emptyRow} [data-value="${barang.id_barang}"]`);
                if (option) selectOption(emptyRow, option);
            }
        } else {
            alert('Barang tidak ditemukan!');
        }
    }

    function hitungSubtotal(id) {
        let harga = parseFloat(document.getElementById(`harga_${id}`).value) || 0;
        let qty = parseFloat(document.getElementById(`qty_${id}`).value) || 0;
        let subtotal = harga * qty;
        
        document.getElementById(`subtotal_${id}`).value = subtotal;
        document.getElementById(`subtotal_display_${id}`).value = formatRupiah(subtotal);
        
        hitungTotalSemua();
    }

    function hitungTotalSemua() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('display_total').innerText = formatRupiah(total);
    }

    function hapusBaris(id) {
        let row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        hitungTotalSemua();
    }

    function toggleAkunKas() {
        let metode = document.getElementById('metode_pembayaran').value;
        let div = document.getElementById('div_akun_kas');
        let input = document.getElementById('akun_kas_bank');
        
        if (metode === 'Tunai') {
            div.style.display = 'block';
            input.setAttribute('required', 'required');
        } else {
            div.style.display = 'none';
            input.removeAttribute('required');
            input.value = '';
        }
    }

    // Cascade Cabang -> Unit Usaha
    document.getElementById('id_cabang').addEventListener('change', function() {
        let cabangId = this.value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let units = unitSelect.querySelectorAll('option');
        
        unitSelect.value = "";
        units.forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-cabang') == cabangId || !cabangId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });
    });

    // Init
    tambahBaris();
    toggleAkunKas();
    if (document.getElementById('id_cabang').value) {
        document.getElementById('id_cabang').dispatchEvent(new Event('change'));
    }
</script>
@endpush
