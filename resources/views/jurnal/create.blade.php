@extends('layouts.app')

@section('title', 'Buat Jurnal Umum - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Buat Jurnal Manual</h1>
            <p class="page-subtitle">Tambah entri jurnal baru secara manual</p>
        </div>
        <div>
            <a href="{{ route('jurnal.index') }}" class="btn btn-outline btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('jurnal.store') }}" method="POST" id="formJurnal" enctype="multipart/form-data">
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
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
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
                        <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_unit_usaha') is-invalid @enderror" id="id_unit_usaha" name="id_unit_usaha" required>
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
                <div class="form-row mt-3" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pelanggan" class="form-label">Pelanggan (Opsional)</label>
                        <select class="form-select" id="id_pelanggan" name="id_pelanggan">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($pelanggan as $p)
                                <option value="{{ $p->id_pelanggan }}" {{ old('id_pelanggan') == $p->id_pelanggan ? 'selected' : '' }}>{{ $p->nama_pelanggan }} (Saldo: Rp {{ number_format($p->saldo_terkini_piutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pemasok" class="form-label">Pemasok (Opsional)</label>
                        <select class="form-select" id="id_pemasok" name="id_pemasok">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($pemasok as $v)
                                <option value="{{ $v->id_pemasok }}" {{ old('id_pemasok') == $v->id_pemasok ? 'selected' : '' }}>{{ $v->nama_pemasok }} (Saldo: Rp {{ number_format($v->saldo_terkini_hutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row mt-3" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="deskripsi" class="form-label">Deskripsi Jurnal</label>
                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Contoh: Pembayaran Gaji Karyawan" required value="{{ old('deskripsi') }}">
                    </div>
                    <div class="form-group" style="width: 250px;">
                        <label for="sumber_jurnal" class="form-label">Jenis Jurnal</label>
                        <select class="form-select" id="sumber_jurnal" name="sumber_jurnal">
                            <option value="Manual" {{ old('sumber_jurnal') == 'Manual' ? 'selected' : '' }}>Umum / Manual</option>
                            <option value="Penyesuaian" {{ old('sumber_jurnal') == 'Penyesuaian' ? 'selected' : '' }}>Jurnal Penyesuaian</option>
                        </select>
                    </div>
                </div>
                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
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

        <!-- Detail Jurnal -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h3 class="form-card-title">Detail Jurnal</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Akun</th>
                            <th style="width: 25%;">Debit</th>
                            <th style="width: 25%;">Kredit</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="container_jurnal">
                        <!-- Rows via JS -->
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--color-bg);">
                            <td class="text-right fw-bold" style="text-align: right;">Total</td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_debit_display" readonly style="background: var(--color-bg); font-weight: 600;">
                                <input type="hidden" id="total_debit" value="0">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="total_kredit_display" readonly style="background: var(--color-bg); font-weight: 600;">
                                <input type="hidden" id="total_kredit" value="0">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: var(--space-md);">
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
                <div id="balance_alert" class="alert alert-danger" style="display: none;">
                    <span data-feather="alert-circle" style="width: 18px; height: 18px; margin-right: 8px;"></span>
                    Jurnal tidak seimbang (Balance)! Selisih: <strong><span id="selisih_display">0</span></strong>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="btnSubmit" disabled style="padding: 16px;">
                    <span data-feather="save" style="width: 18px; height: 18px; margin-right: 8px;"></span>
                    Simpan Jurnal
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let akunData = @json($akun);
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
                    <input type="number" class="form-control form-control-sm input-debit" name="details[${currentRow}][debit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-kredit" name="details[${currentRow}][kredit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
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
        let totalDebit = 0;
        let totalKredit = 0;

        document.querySelectorAll('.input-debit').forEach(input => totalDebit += parseFloat(input.value) || 0);
        document.querySelectorAll('.input-kredit').forEach(input => totalKredit += parseFloat(input.value) || 0);

        document.getElementById('total_debit').value = totalDebit;
        document.getElementById('total_kredit').value = totalKredit;
        
        document.getElementById('total_debit_display').value = formatRupiah(totalDebit);
        document.getElementById('total_kredit_display').value = formatRupiah(totalKredit);

        let balance = Math.abs(totalDebit - totalKredit) < 0.01; // Tolerance for float
        let btn = document.getElementById('btnSubmit');
        let alert = document.getElementById('balance_alert');

        if (balance && totalDebit > 0) {
            btn.removeAttribute('disabled');
            alert.style.display = 'none';
        } else {
            btn.setAttribute('disabled', 'disabled');
            if (totalDebit > 0 || totalKredit > 0) {
                alert.style.display = 'flex';
                alert.style.alignItems = 'center';
            }
            document.getElementById('selisih_display').innerText = formatRupiah(Math.abs(totalDebit - totalKredit));
        }
    }

    // Cascade Cabang -> Unit Usaha
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

    // Cascade Unit Usaha -> Proyek
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

    // Trigger initial cascade if cabang is pre-selected
    if (document.getElementById('id_cabang').value) {
        document.getElementById('id_cabang').dispatchEvent(new Event('change'));
    }

    // Init 2 rows
    tambahBaris();
    tambahBaris();
</script>
@endpush
