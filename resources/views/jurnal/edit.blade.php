@extends('layouts.app')

@section('title', 'Edit Jurnal Umum - Simple Akunting')

@section('content')
    <!-- Page Header -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Edit Jurnal</h1>
            <p class="page-subtitle">Update deskripsi dan tanggal jurnal</p>
        </div>
        <div>
            <a href="{{ route('jurnal.index') }}" class="btn btn-outline btn-sm">
                <span data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('jurnal.update', $jurnal->id_jurnal) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Header Form -->
        <div class="form-card mb-4">
            <div class="form-card-body">
                <div class="form-row">
                    <div class="form-group" style="flex: 1; margin-right: 15px;">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi" value="{{ $jurnal->no_transaksi }}" readonly style="background: var(--color-bg-light);">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $jurnal->tanggal) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1; margin-right: 15px;">
                        <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_cabang') is-invalid @enderror" id="id_cabang" name="id_cabang" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabang as $c)
                                <option value="{{ $c->id }}" {{ old('id_cabang', $jurnal->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1; margin-right: 15px;">
                        <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_unit_usaha') is-invalid @enderror" id="id_unit_usaha" name="id_unit_usaha" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($unitUsaha as $u)
                                <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha', $jurnal->id_unit_usaha) == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_project" class="form-label">Proyek / Program (Opsional)</label>
                        <select class="form-select @error('id_project') is-invalid @enderror" id="id_project" name="id_project">
                            <option value="">-- Tanpa Proyek --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id_project }}" data-unit="{{ $proj->id_unit_usaha }}" {{ old('id_project', $jurnal->id_project) == $proj->id_project ? 'selected' : '' }}>{{ $proj->kode_project }} - {{ $proj->nama_project }}</option>
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
                                <option value="{{ $p->id_pelanggan }}" {{ old('id_pelanggan', $jurnal->id_pelanggan) == $p->id_pelanggan ? 'selected' : '' }}>{{ $p->nama_pelanggan }} (Saldo: Rp {{ number_format($p->saldo_terkini_piutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="id_pemasok" class="form-label">Pemasok (Opsional)</label>
                        <select class="form-select" id="id_pemasok" name="id_pemasok">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($pemasok as $v)
                                <option value="{{ $v->id_pemasok }}" {{ old('id_pemasok', $jurnal->id_pemasok) == $v->id_pemasok ? 'selected' : '' }}>{{ $v->nama_pemasok }} (Saldo: Rp {{ number_format($v->saldo_terkini_hutang, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row mt-3" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="deskripsi" class="form-label">Deskripsi Jurnal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" required value="{{ old('deskripsi', $jurnal->deskripsi) }}">
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group" style="width: 250px;">
                        <label for="sumber_jurnal" class="form-label">Jenis Jurnal</label>
                        <select class="form-select" id="sumber_jurnal" name="sumber_jurnal">
                            <option value="Manual" {{ old('sumber_jurnal', $jurnal->sumber_jurnal) == 'Manual' ? 'selected' : '' }}>Umum / Manual</option>
                            <option value="Penyesuaian" {{ old('sumber_jurnal', $jurnal->sumber_jurnal) == 'Penyesuaian' ? 'selected' : '' }}>Jurnal Penyesuaian</option>
                            @if(!in_array($jurnal->sumber_jurnal, ['Manual', 'Penyesuaian']))
                                <option value="{{ $jurnal->sumber_jurnal }}" selected>{{ $jurnal->sumber_jurnal }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-row mt-3">
                    <div class="form-group" style="flex: 1;">
                        <label for="foto_bukti" class="form-label">Foto Bukti Transaksi (Opsional)</label>
                        @if($jurnal->foto_bukti)
                            <div class="mb-2">
                                <a href="{{ asset('storage/bukti_transaksi/' . $jurnal->foto_bukti) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <span data-feather="image" style="width: 14px; height: 14px; margin-right: 4px;"></span> Lihat Bukti Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('foto_bukti') is-invalid @enderror" id="foto_bukti" name="foto_bukti" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 5MB. Pilih berkas baru untuk mengganti berkas saat ini.</small>
                        @error('foto_bukti')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Jurnal (Read Only) -->
        <div class="form-card mb-4">
            <div class="form-card-header">
                <h3 class="form-card-title">Detail Jurnal (Tidak dapat diubah)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th style="text-align: right;">Debit</th>
                            <th style="text-align: right;">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalKredit = 0;
                        @endphp
                        @foreach($jurnal->details as $detail)
                            <tr>
                                <td>{{ $detail->kode_akun }} - {{ $detail->akun->nama_akun ?? 'N/A' }}</td>
                                <td style="text-align: right;">{{ number_format($detail->debit, 2, ',', '.') }}</td>
                                <td style="text-align: right;">{{ number_format($detail->kredit, 2, ',', '.') }}</td>
                            </tr>
                            @php
                                $totalDebit += $detail->debit;
                                $totalKredit += $detail->kredit;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--color-bg-light); font-weight: bold;">
                            <td style="text-align: right;">Total</td>
                            <td style="text-align: right;">{{ number_format($totalDebit, 2, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($totalKredit, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="form-card">
            <div class="form-card-body">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 16px; width: 100%;">
                    <span data-feather="save" style="width: 18px; height: 18px; margin-right: 8px;"></span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
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
        let cabangId = document.getElementById('id_cabang').value;
        let unitSelect = document.getElementById('id_unit_usaha');
        let selectedUnit = "{{ old('id_unit_usaha', $jurnal->id_unit_usaha) }}";
        
        unitSelect.querySelectorAll('option').forEach(opt => {
            if (opt.value === "") return;
            if (opt.getAttribute('data-cabang') == cabangId) {
                opt.style.display = "";
            } else {
                opt.style.display = "none";
            }
        });
        
        if (selectedUnit) {
            unitSelect.value = selectedUnit;
            // Trigger unit change to show correct project options
            let projectSelect = document.getElementById('id_project');
            let selectedProj = "{{ old('id_project', $jurnal->id_project) }}";
            if (projectSelect) {
                projectSelect.querySelectorAll('option').forEach(opt => {
                    if (opt.value === "") return;
                    if (opt.getAttribute('data-unit') == selectedUnit) {
                        opt.style.display = "";
                    } else {
                        opt.style.display = "none";
                    }
                });
                if (selectedProj) {
                    projectSelect.value = selectedProj;
                }
            }
        }
    }
</script>
@endpush
