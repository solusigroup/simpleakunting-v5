@extends('layouts.app')

@section('title', 'Edit RFQ Pembelian - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit RFQ Pembelian #{{ $rfq->no_rfq }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('rfq.show', $rfq->id_rfq) }}" class="btn btn-sm btn-secondary">
                Batal
            </a>
        </div>
    </div>

    <form action="{{ route('rfq.update', $rfq->id_rfq) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Info RFQ -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Info RFQ</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No RFQ</label>
                                <input type="text" class="form-control" name="no_rfq" value="{{ $rfq->no_rfq }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal_rfq" value="{{ $rfq->tanggal_rfq }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Pemasok</label>
                                <select class="form-select" name="id_pemasok" required>
                                    <option value="">-- Pilih Pemasok --</option>
                                    @foreach($pemasok as $p)
                                        <option value="{{ $p->id_pemasok }}" {{ $rfq->id_pemasok == $p->id_pemasok ? 'selected' : '' }}>{{ $p->nama_pemasok }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cabang <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_cabang" name="id_cabang" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($cabang as $c)
                                        <option value="{{ $c->id }}" {{ $rfq->id_cabang == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_unit_usaha" name="id_unit_usaha" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($unitUsaha as $u)
                                        <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ $rfq->id_unit_usaha == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Proyek / Program (Opsional)</label>
                                <select class="form-select" id="id_project" name="id_project">
                                    <option value="">-- Tanpa Proyek --</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->id_project }}" data-unit="{{ $proj->id_unit_usaha }}" {{ $rfq->id_project == $proj->id_project ? 'selected' : '' }}>{{ $proj->kode_project }} - {{ $proj->nama_project }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status RFQ</label>
                            <select class="form-select" name="status" required>
                                <option value="Draft" {{ $rfq->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Dikirim" {{ $rfq->status == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                <option value="Disetujui" {{ $rfq->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan / Catatan</label>
                            <textarea class="form-control" name="keterangan" rows="2">{{ $rfq->keterangan }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total -->
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center d-flex flex-column justify-content-center" style="min-height: 200px;">
                        <h3>Total Estimasi</h3>
                        <h1 class="display-5 fw-bold text-primary" id="display_total">Rp {{ number_format($rfq->total, 0, ',', '.') }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Rincian Barang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="45%">Barang</th>
                                <th width="15%">Harga Beli Estimasi</th>
                                <th width="10%">Qty</th>
                                <th width="20%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="container_barang">
                            <!-- Rows loaded via script -->
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
            <button type="submit" class="btn btn-lg btn-primary w-100">SIMPAN PERUBAHAN</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let rowCount = 0;
    const rawBarangData = @json($barang);
    let barangData = Array.isArray(rawBarangData) ? rawBarangData : Object.values(rawBarangData || {});
    const existingDetails = @json($rfq->details);

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }

    function tambahBaris(data = null) {
        const idx = rowCount;
        let optionsHtml = barangData.map(b => {
            const label = `${b.kode_barang || ''} - ${b.nama_barang || ''}`;
            const formatNum = (val) => new Intl.NumberFormat('id-ID').format(val || 0);
            
            const stok = formatNum(b.stok_saat_ini);
            const beli = formatNum(b.harga_beli);
            const barcode = b.barcode ? String(b.barcode).trim() : '-';
            const satuan = b.satuan ? String(b.satuan).trim() : '-';

            return `<div class="searchable-select-option" 
                data-value="${b.id_barang}" 
                data-harga="${b.harga_beli || 0}" 
                data-kode="${b.kode_barang || ''}" 
                data-label="${label}">
                <div class="d-flex justify-content-between align-items-center w-100" style="gap: 15px;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; color: var(--color-text, #333);">
                            <span class="text-primary" style="font-weight: 700;">${b.kode_barang || ''}</span> - ${b.nama_barang || ''}
                        </div>
                        <div class="text-muted" style="font-size: 0.72rem; margin-top: 3px;">
                            Barcode: ${barcode} | Satuan: ${satuan}
                        </div>
                    </div>
                    <div class="text-end" style="font-size: 0.72rem; line-height: 1.35; flex-shrink: 0; min-width: 140px; border-left: 1px solid #f0f0f0; padding-left: 10px;">
                        <div>Stok: <strong class="text-success">${stok}</strong></div>
                        <div class="text-muted" style="font-size: 0.68rem; margin-top: 1px;">Beli: Rp ${beli}</div>
                    </div>
                </div>
            </div>`;
        }).join('');

        const html = `
            <tr id="row_${idx}">
                <td>
                    <div class="searchable-select" id="ss_${idx}">
                        <input type="hidden" name="details[${idx}][id_barang]" id="ss_input_${idx}" required value="${data ? data.id_barang : ''}">
                        <div class="searchable-select-trigger" id="ss_trigger_${idx}">
                            <span class="trigger-text ">${data ? (data.barang ? `${data.barang.kode_barang} - ${data.barang.nama_barang}` : '🔍 Cari atau pilih barang...') : '🔍 Cari atau pilih barang...'}</span>
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
                    <input type="number" class="form-control form-control-sm" name="details[${idx}][harga_beli]" id="harga_${idx}" value="${data ? data.harga : ''}" onkeyup="hitungSubtotal(${idx})" onchange="hitungSubtotal(${idx})" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="details[${idx}][kuantitas]" id="qty_${idx}" min="1" value="${data ? data.kuantitas : 1}" onkeyup="hitungSubtotal(${idx})" onchange="hitungSubtotal(${idx})">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" id="subtotal_display_${idx}" readonly value="${data ? formatRupiah(data.subtotal) : ''}">
                    <input type="hidden" class="subtotal-input" id="subtotal_${idx}" value="${data ? data.subtotal : 0}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(${idx})">X</button>
                </td>
            </tr>
        `;
        document.getElementById('container_barang').insertAdjacentHTML('beforeend', html);
        initSearchableSelect(idx);
        
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

    // Cabang & Unit & Project cascade
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

    // Load existing items
    if (existingDetails && existingDetails.length > 0) {
        existingDetails.forEach(d => tambahBaris(d));
    } else {
        tambahBaris();
    }
</script>
@endpush
