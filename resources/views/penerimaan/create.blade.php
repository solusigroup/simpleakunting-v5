@extends('layouts.app')

@section('title', 'Buat Penerimaan Kas - Simple Akunting')

@section('content')
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Buat Penerimaan Kas</h1>
            <p class="page-subtitle">Tambah transaksi penerimaan kas atau bank baru</p>
        </div>
        <div>
            <a href="{{ route('penerimaan.index') }}" class="btn btn-outline btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('penerimaan.store') }}" method="POST">
        @csrf
        <!-- Header Form -->
        <div class="form-card mb-4">
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly style="background: var(--color-bg);">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="akun_kas" class="form-label">Masuk ke Akun (Debit) <span class="text-danger">*</span></label>
                        <select class="form-select @error('akun_kas') is-invalid @enderror" id="akun_kas" name="akun_kas" required>
                            <option value="">-- Pilih Kas/Bank --</option>
                            @foreach($akunKas as $a)
                                <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pelanggan" class="form-label">Diterima Dari (Pelanggan) - Opsional</label>
                        <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                            <option value="">-- Umum --</option>
                            @foreach($pelanggan as $p)
                                <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Contoh: Penerimaan Setoran Modal" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h3 class="form-card-title">Rincian Penerimaan (Kredit)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Akun</th>
                            <th style="width: 30%;">Nominal</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_detail">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--color-bg);">
                            <td class="text-right fw-bold" style="text-align: right;">Total Penerimaan</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_display" readonly style="background: var(--color-bg); font-weight: 700; font-size: 1rem; color: var(--color-primary);">
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

        <div class="mt-3 mb-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Simpan Transaksi</button>
        </div>
    </form>
@endsection

@push('styles')
<style>
    .searchable-select { position: relative; width: 100%; }
    .searchable-select-trigger { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0.5rem 1rem; font-size: 0.875rem; border: 1px solid var(--color-border, #dee2e6); border-radius: 8px; background: var(--color-bg, #fff); color: var(--color-text, #333); cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-height: 42px; position: relative; }
    .searchable-select-trigger:hover { border-color: var(--color-primary, #8b5cf6); background: var(--color-bg-hover, #fcfaff); }
    .searchable-select-trigger.open { border-color: var(--color-primary, #8b5cf6); box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); background: #fff; z-index: 1051; }
    .searchable-select-trigger .trigger-text { flex: 1; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 8px; }
    .searchable-select-trigger .trigger-text.placeholder { color: var(--color-text-muted, #999); }
    .searchable-select-trigger .trigger-chevron { margin-left: 8px; transition: transform 0.3s; flex-shrink: 0; color: var(--color-text-muted, #666); }
    .searchable-select-trigger.open .trigger-chevron { transform: rotate(180deg); color: var(--color-primary, #8b5cf6); }
    
    .searchable-select-dropdown { display: none; position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #fff; border: 1px solid var(--color-border, #dee2e6); border-radius: 10px; box-shadow: 0 12px 30px rgba(0,0,0,0.15); z-index: 1050; max-height: 320px; overflow: hidden; animation: selectFadeIn 0.2s ease-out; }
    @keyframes selectFadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .searchable-select-dropdown.show { display: block; }
    
    .searchable-select-search { padding: 12px; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
    .searchable-select-search input { width: 100%; padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; outline: none; transition: all 0.2s; background: #fff; color: var(--color-text, #333); }
    .searchable-select-search input:focus { border-color: var(--color-primary, #8b5cf6); box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); background: #fff; }
    
    .searchable-select-options { max-height: 240px; overflow-y: auto; padding: 6px 0; scrollbar-width: thin; scrollbar-color: #e0e0e0 transparent; }
    .searchable-select-options::-webkit-scrollbar { width: 6px; }
    .searchable-select-options::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
    
    .searchable-select-option { padding: 10px 16px; cursor: pointer; font-size: 0.875rem; color: var(--color-text, #444); transition: all 0.15s; display: flex; align-items: center; justify-content: space-between; }
    .searchable-select-option:hover { background: #f5f3ff; color: var(--color-primary, #8b5cf6); padding-left: 20px; }
    .searchable-select-option.selected { background: #f5f3ff; color: var(--color-primary, #8b5cf6); font-weight: 600; }
    .searchable-select-option.hidden { display: none; }
    .searchable-select-empty { padding: 20px; text-align: center; color: var(--color-text-muted, #999); font-size: 0.875rem; }

    /* Fix clipping in table-responsive */
    .table-responsive { overflow: visible !important; }
    .data-table td { overflow: visible !important; position: relative; padding: 12px 8px !important; }
    .data-table tr:hover { background-color: transparent !important; }
</style>
@endpush

@push('scripts')
<script>
    let akunData = @json($akunPendapatan);
    let rowCount = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        const currentRow = rowCount;
        let optionsHtml = akunData.map(a => {
            const label = `${a.kode_akun} - ${a.nama_akun}`;
            const escapedLabel = label.replace(/"/g, '&quot;');
            return `<div class="searchable-select-option" data-value="${a.kode_akun}" data-label="${escapedLabel}">${label}</div>`;
        }).join('');

        let html = `
            <tr id="row_${currentRow}">
                <td>
                    <div class="searchable-select" id="ss_${currentRow}">
                        <input type="hidden" name="details[${currentRow}][kode_akun]" id="ss_input_${currentRow}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${currentRow}">
                            <div class="trigger-text placeholder">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #8b5cf6; opacity: 0.7;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span>Cari akun...</span>
                            </div>
                            <svg class="trigger-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
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
                    <input type="number" class="form-control form-control-sm input-jumlah" name="details[${currentRow}][jumlah]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="hapusBaris(${currentRow})">
                        <span data-feather="x" style="width: 14px; height: 14px;"></span>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('container_detail').insertAdjacentHTML('beforeend', html);
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
            triggerText.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #8b5cf6;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>${option.dataset.label}</span>
            `;
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
        document.querySelectorAll('.input-jumlah').forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('total_display').value = formatRupiah(total);
    }

    tambahBaris();
    tambahBaris();
    feather.replace();
</script>
@endpush
