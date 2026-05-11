@extends('layouts.app')

@section('title', 'Buat Penerimaan Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Penerimaan Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('penerimaan.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('penerimaan.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="no_transaksi" class="form-label">No Transaksi</label>
                <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly>
            </div>
            <div class="col-md-4">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="akun_kas" class="form-label">Masuk ke Akun (Debit)</label>
                <select class="form-select" id="akun_kas" name="akun_kas" required>
                    <option value="">-- Pilih Kas/Bank --</option>
                    @foreach($akunKas as $a)
                        <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="id_pelanggan" class="form-label">Diterima Dari (Pelanggan) - Opsional</label>
                <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                    <option value="">-- Umum --</option>
                    @foreach($pelanggan as $p)
                        <option value="{{ $p->id_pelanggan }}">{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Rincian Penerimaan (Kredit)</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60%">Akun Pendapatan/Piutang</th>
                            <th width="30%">Jumlah</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_detail">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end fw-bold">Total Penerimaan</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_display" readonly>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+ Tambah Baris</button>
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
        let optionsHtml = akunData.map(a =>
            `<div class="searchable-select-option" data-value="${a.kode_akun}" data-label="${a.kode_akun} - ${a.nama_akun}">${a.kode_akun} - ${a.nama_akun}</div>`
        ).join('');

        let html = `
            <tr id="row_${currentRow}">
                <td>
                    <div class="searchable-select" id="ss_${currentRow}">
                        <input type="hidden" name="details[${currentRow}][kode_akun]" id="ss_input_${currentRow}" required>
                        <div class="searchable-select-trigger" id="ss_trigger_${currentRow}">
                            <span class="trigger-text placeholder">🔍 Cari akun...</span>
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
                    <input type="number" class="form-control form-control-sm input-jumlah" name="details[${currentRow}][jumlah]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${currentRow})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_detail').insertAdjacentHTML('beforeend', html);
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
        document.querySelectorAll('.input-jumlah').forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('total_display').value = formatRupiah(total);
    }

    tambahBaris();
</script>
@endpush
