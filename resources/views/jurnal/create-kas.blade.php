@extends('layouts.app')

@section('title', 'Buat Jurnal Kas - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Buat Jurnal Kas</h1>
            <p class="page-subtitle">Tambah transaksi penerimaan atau pengeluaran kas/bank</p>
        </div>
        <div>
            <a href="{{ route('jurnal.index') }}" class="btn btn-outline btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('jurnal.storeKas') }}" method="POST" id="formJurnal">
        @csrf
        
        <!-- Header Form -->
        <div class="form-card mb-4">
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                        <div style="display: flex; gap: 20px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; cursor: pointer; color: var(--color-success);">
                                <input type="radio" name="tipe_transaksi" value="masuk" checked style="margin-right: 8px;">
                                <strong>📥 Kas Masuk (KM)</strong>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; color: var(--color-danger);">
                                <input type="radio" name="tipe_transaksi" value="keluar" style="margin-right: 8px;">
                                <strong>📤 Kas Keluar (KK)</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_akun_kas" class="form-label">Akun Kas/Bank Utama <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_akun_kas" name="id_akun_kas" required>
                            <option value="">-- Pilih Akun Kas --</option>
                            @foreach($akunKas as $ak)
                                <option value="{{ $ak->kode_akun }}">{{ $ak->kode_akun }} - {{ $ak->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mt-4">
                    <div class="form-group" style="flex: 1;">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi_display" value="[Otomatis KM/KK]" readonly style="background: var(--color-bg);">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_cabang" name="id_cabang" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}" {{ old('id_cabang', session('active_cabang') ?: auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_unit_usaha" name="id_unit_usaha" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($unitUsaha as $u)
                                <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha', session('active_unit')) == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="deskripsi" class="form-label">Keterangan/Deskripsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan keterangan transaksi" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Searchable Dropdown Styles -->
        <style>
            .searchable-select { position: relative; width: 100%; }
            .searchable-select-trigger { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 6px 12px; font-size: 0.875rem; border: 1px solid var(--color-border, #dee2e6); border-radius: var(--radius-sm, 6px); background: var(--color-bg, #fff); color: var(--color-text, #333); cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-height: 34px; }
            .searchable-select-trigger:hover { border-color: var(--color-primary, #8b5cf6); }
            .searchable-select-trigger.open { border-color: var(--color-primary, #8b5cf6); box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15); }
            .searchable-select-trigger .trigger-text { flex: 1; overflow: hidden; text-overflow: ellipsis; }
            .searchable-select-trigger .trigger-text.placeholder { color: var(--color-text-muted, #999); }
            .searchable-select-trigger .trigger-chevron { margin-left: 8px; transition: transform 0.2s; flex-shrink: 0; }
            .searchable-select-trigger.open .trigger-chevron { transform: rotate(180deg); }
            .searchable-select-dropdown { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: var(--color-bg-card, #fff); border: 1px solid var(--color-border, #dee2e6); border-radius: var(--radius-sm, 6px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 1050; max-height: 280px; overflow: hidden; }
            .searchable-select-dropdown.show { display: block; }
            .searchable-select-search { padding: 8px; border-bottom: 1px solid var(--color-border, #eee); position: sticky; top: 0; background: var(--color-bg-card, #fff); z-index: 1; }
            .searchable-select-search input { width: 100%; padding: 6px 10px; border: 1px solid var(--color-border, #dee2e6); border-radius: var(--radius-sm, 4px); font-size: 0.8125rem; outline: none; transition: border-color 0.2s; background: var(--color-bg, #fff); color: var(--color-text, #333); }
            .searchable-select-options { max-height: 220px; overflow-y: auto; padding: 4px 0; }
            .searchable-select-option { padding: 7px 12px; cursor: pointer; font-size: 0.8125rem; color: var(--color-text, #333); transition: background 0.15s; }
            .searchable-select-option:hover { background: rgba(139, 92, 246, 0.08); }
            .searchable-select-option.selected { background: rgba(139, 92, 246, 0.12); color: var(--color-primary, #8b5cf6); font-weight: 600; }
            .searchable-select-option.hidden { display: none; }
            .searchable-select-empty { padding: 12px; text-align: center; color: var(--color-text-muted, #999); font-size: 0.8125rem; }
        </style>

        <!-- Detail Transaksi -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h3 class="form-card-title">Detail Akun Lawan</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Akun Lawan</th>
                            <th style="width: 40%;">Nominal</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_jurnal">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--color-bg);">
                            <td class="text-right fw-bold" style="text-align: right;">Total Kas</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_nominal_display" readonly style="background: var(--color-bg); font-weight: 700; font-size: 1rem; color: var(--color-primary);">
                                <input type="hidden" id="total_nominal" value="0">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: var(--space-md);">
                                <button type="button" class="btn btn-success btn-sm" onclick="tambahBaris()">
                                    <span data-feather="plus" style="width: 14px; height: 14px; margin-right: 4px;"></span>
                                    Tambah Baris
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="form-card">
            <div class="form-card-body">
                <button type="submit" class="btn btn-primary btn-block" id="btnSubmit" disabled style="padding: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    <span data-feather="save" style="width: 18px; height: 18px; margin-right: 8px;"></span>
                    Simpan Jurnal Kas
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let akunData = @json($akunLawan);
    let rowCount = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        const currentRow = rowCount;
        let optionsHtml = akunData.map(a =>
            `<div class="searchable-select-option" data-value="${a.kode_akun}" data-label="${a.kode_akun} - ${a.nama_akun}">${a.kode_akun} - ${a.nama_akun}</div>`
        ).join('');

        let html = `
            <tr id="row_${currentRow}">
                <td>
                    <div class="searchable-select" id="ss_${currentRow}">
                        <input type="hidden" name="details[${currentRow}][kode_akun]" id="ss_input_${currentRow}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${currentRow}">
                            <span class="trigger-text placeholder">🔍 Cari akun lawan...</span>
                            <svg class="trigger-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="searchable-select-dropdown" id="ss_dropdown_${currentRow}">
                            <div class="searchable-select-search">
                                <input type="text" placeholder="Ketik kode atau nama akun..." id="ss_search_${currentRow}" autocomplete="off">
                            </div>
                            <div class="searchable-select-options" id="ss_options_${currentRow}">
                                ${optionsHtml}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-nominal" name="details[${currentRow}][nominal]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="hapusBaris(${currentRow})">
                        <span data-feather="x" style="width: 14px; height: 14px;"></span>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('container_jurnal').insertAdjacentHTML('beforeend', html);
        feather.replace();
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

        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        optionsContainer.addEventListener('click', function(e) {
            const option = e.target.closest('.searchable-select-option');
            if (!option) return;
            e.stopPropagation();

            optionsContainer.querySelectorAll('.selected').forEach(o => o.classList.remove('selected'));
            option.classList.add('selected');

            hiddenInput.value = option.dataset.value;
            const triggerText = trigger.querySelector('.trigger-text');
            triggerText.textContent = option.dataset.label;
            triggerText.classList.remove('placeholder');

            dropdown.classList.remove('show');
            trigger.classList.remove('open');
        });
    }

    function filterOptions(rowId, query) {
        const optionsContainer = document.getElementById(`ss_options_${rowId}`);
        const options = optionsContainer.querySelectorAll('.searchable-select-option');
        const q = query.toLowerCase().trim();
        let visibleCount = 0;

        options.forEach(option => {
            const label = option.dataset.label.toLowerCase();
            if (!q || label.includes(q)) {
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
                emptyMsg.textContent = 'Akun tidak ditemukan';
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

    function hapusBaris(id) {
        document.getElementById(`row_${id}`).remove();
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.input-nominal').forEach(input => total += parseFloat(input.value) || 0);

        document.getElementById('total_nominal').value = total;
        document.getElementById('total_nominal_display').value = formatRupiah(total);

        let btn = document.getElementById('btnSubmit');
        if (total > 0 && document.getElementById('id_akun_kas').value) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'disabled');
        }
    }

    document.getElementById('id_akun_kas').addEventListener('change', hitungTotal);

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

    if (document.getElementById('id_cabang').value) {
        document.getElementById('id_cabang').dispatchEvent(new Event('change'));
    }

    // Initial rows
    tambahBaris();
</script>
@endpush
