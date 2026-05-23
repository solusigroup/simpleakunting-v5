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

    <form action="{{ route('jurnal.storeKas') }}" method="POST" id="formJurnal" enctype="multipart/form-data">
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
                    <div class="form-group" style="flex: 1;">
                        <label for="id_project" class="form-label">Proyek / Program (Opsional)</label>
                        <select class="form-select @error('id_project') is-invalid @enderror" id="id_project" name="id_project">
                            <option value="">-- Tanpa Proyek --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id_project }}" data-unit="{{ $proj->id_unit_usaha }}" {{ old('id_project') == $proj->id_project ? 'selected' : '' }}>{{ $proj->kode_project }} - {{ $proj->nama_project }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mt-3 flex-wrap">
                    <div class="form-group" style="flex: 1; min-width: 250px;">
                        <label for="deskripsi" class="form-label">Keterangan/Deskripsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan keterangan transaksi" required>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 250px;">
                        <label for="foto_bukti" class="form-label">Foto Bukti Transaksi (Opsional)</label>
                        <input type="file" class="form-control @error('foto_bukti') is-invalid @enderror" id="foto_bukti" name="foto_bukti" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 5MB. Dapat dipilih dari galeri atau kamera smartphone.</small>
                        @error('foto_bukti')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

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

    document.getElementById('id_cabang').addEventListener('change', function(e) {
        let cabangId = this.value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let units = unitSelect.querySelectorAll('option');
        if (e.isTrusted || !unitSelect.value) {
            unitSelect.value = "";
        }
        units.forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-cabang') == cabangId || !cabangId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });
        unitSelect.dispatchEvent(new Event('change'));
    });

    document.getElementById('id_unit_usaha').addEventListener('change', function(e) {
        let unitId = this.value;
        let projectSelect = document.getElementById('id_project');
        if (!projectSelect) return;
        let projects = projectSelect.querySelectorAll('option');
        if (e.isTrusted || !projectSelect.value) {
            projectSelect.value = "";
        }
        projects.forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-unit') == unitId || !unitId) {
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
