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

    <form action="{{ route('jurnal.store') }}" method="POST" id="formJurnal">
        @csrf
        
        <!-- Header Form -->
        <div class="form-card mb-4">
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly style="background: var(--color-bg);">
                    </div>
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi jurnal" required>
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
    let akunData = {!! json_encode($akun) !!};
    let rowCount = 0;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris() {
        let html = `
            <tr id="row_${rowCount}">
                <td>
                    <select class="form-select form-select-sm" name="details[${rowCount}][kode_akun]" required>
                        <option value="">-- Pilih Akun --</option>
                        ${akunData.map(a => `<option value="${a.kode_akun}">${a.kode_akun} - ${a.nama_akun}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-debit" name="details[${rowCount}][debit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm input-kredit" name="details[${rowCount}][kredit]" value="0" min="0" onkeyup="hitungTotal()" onchange="hitungTotal()">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="hapusBaris(${rowCount})">
                        <span data-feather="x" style="width: 14px; height: 14px;"></span>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('container_jurnal').insertAdjacentHTML('beforeend', html);
        feather.replace();
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

    // Init 2 rows
    tambahBaris();
    tambahBaris();
</script>
@endpush
