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

    <!-- Custom styles using global searchable-select.css -->
    <style>
        /* Fix clipping in table-responsive */
        .table-responsive { overflow: visible !important; }
        .data-table td { overflow: visible !important; position: relative; }
        .data-table tr:hover { background-color: transparent !important; }
    </style>

    <form action="{{ route('penerimaan.store') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="app-alert app-alert-danger">
                <ul class="mb-0" style="padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
                        <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_cabang') is-invalid @enderror" id="id_cabang" name="id_cabang" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}" {{ old('id_cabang', session('active_cabang') ?: auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_unit_usaha" class="form-label">Unit Usaha</label>
                        <select class="form-select @error('id_unit_usaha') is-invalid @enderror" id="id_unit_usaha" name="id_unit_usaha">
                            <option value="">-- Pilih Unit Usaha --</option>
                            @foreach($unitUsaha as $u)
                                <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha', session('active_unit')) == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pelanggan" class="form-label">Diterima Dari (Pelanggan) - Opsional</label>
                        <select class="form-select" id="id_pelanggan" name="id_pelanggan" onchange="updateSaldoDisplay()">
                            <option value="" data-saldo="0">-- Umum --</option>
                            @foreach($pelanggan as $p)
                                <option value="{{ $p->id_pelanggan }}" 
                                    data-saldo="{{ $p->saldo_terkini_piutang }}"
                                    {{ request('id_pelanggan') == $p->id_pelanggan ? 'selected' : '' }}>
                                    {{ $p->nama_pelanggan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Saldo Piutang Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="saldo_piutang_display" readonly style="background: #fff8f8; color: #dc3545; font-weight: bold;">
                        </div>
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
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="data-table" style="overflow: visible !important;">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Akun</th>
                            <th style="width: 30%;">Nominal</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_detail" style="overflow: visible !important;">
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
                        <input type="hidden" name="details[${currentRow}][kode_akun]" id="ss_input_${currentRow}">
                        <div class="searchable-select-trigger" id="ss_trigger_${currentRow}">
                            <div class="trigger-text ss-placeholder">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.8;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
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

    function hapusBaris(id) {
        document.getElementById(`row_${id}`).remove();
        hitungTotal();
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.input-jumlah').forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('total_display').value = formatRupiah(total);
    }

    function updateSaldoDisplay() {
        const select = document.getElementById('id_pelanggan');
        const display = document.getElementById('saldo_piutang_display');
        const selectedOption = select.options[select.selectedIndex];
        const saldo = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
        
        display.value = new Intl.NumberFormat('id-ID').format(saldo);
    }

    // Handle Unit Usaha filter based on Cabang
    document.getElementById('id_cabang').addEventListener('change', function(e) {
        let cabangId = this.value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let options = unitSelect.querySelectorAll('option[data-cabang]');
        
        let found = false;
        options.forEach(opt => {
            if (opt.getAttribute('data-cabang') === cabangId) {
                opt.style.display = '';
                if(opt.value == unitSelect.getAttribute('data-old-value')) found = true;
            } else {
                opt.style.display = 'none';
            }
        });
        
        if(!found && unitSelect.value !== "") {
           // unitSelect.value = '';
        }
    });

    // Trigger initial filter
    if (document.getElementById('id_cabang').value) {
        document.getElementById('id_cabang').dispatchEvent(new Event('change'));
    }

    // Init rows
    tambahBaris();
    tambahBaris();
    updateSaldoDisplay();
</script>
@endpush
