@extends('layouts.app')

@section('title', 'Transaksi Simpanan Baru - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Transaksi Simpanan Baru</h1>
    <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('simpanan.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                                <select name="jenis_transaksi" class="form-select @error('jenis_transaksi') is-invalid @enderror" required id="jenisTransaksi">
                                    <option value="setor" {{ old('jenis_transaksi') == 'setor' ? 'selected' : '' }}>Setor</option>
                                    <option value="tarik" {{ old('jenis_transaksi') == 'tarik' ? 'selected' : '' }}>Tarik</option>
                                </select>
                                @error('jenis_transaksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anggota <span class="text-danger">*</span></label>
                        <select name="id_anggota" class="form-select @error('id_anggota') is-invalid @enderror" required id="anggotaSelect">
                            <option value="">Pilih Anggota...</option>
                            @foreach($anggotaList as $a)
                                <option value="{{ $a->id_anggota }}" {{ old('id_anggota') == $a->id_anggota ? 'selected' : '' }}>
                                    {{ $a->no_anggota }} - {{ $a->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Simpanan <span class="text-danger">*</span></label>
                        <select name="id_jenis_simpanan" class="form-select @error('id_jenis_simpanan') is-invalid @enderror" required id="jenisSimpananSelect">
                            <option value="">Pilih Jenis Simpanan...</option>
                            @foreach($jenisSimpanan as $js)
                                <option value="{{ $js->id_jenis_simpanan }}" 
                                        data-tipe="{{ $js->tipe }}"
                                        {{ old('id_jenis_simpanan') == $js->id_jenis_simpanan ? 'selected' : '' }}>
                                    {{ $js->nama_simpanan }} ({{ ucfirst($js->tipe) }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenis_simpanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="tipeSimpananInfo"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                           value="{{ old('jumlah') }}" min="1" step="1" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Akun Kas/Bank <span class="text-danger">*</span></label>
                                <select name="akun_kas_bank" class="form-select @error('akun_kas_bank') is-invalid @enderror" required>
                                    <option value="">Pilih Akun...</option>
                                    @foreach($akunKasBank as $akun)
                                        <option value="{{ $akun->kode_akun }}" {{ old('akun_kas_bank') == $akun->kode_akun ? 'selected' : '' }}>
                                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('akun_kas_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="2">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span data-feather="save"></span> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <strong>Informasi</strong>
            </div>
            <div class="card-body">
                <h6>Jenis-jenis Simpanan:</h6>
                <ul class="small">
                    <li><strong>Simpanan Pokok:</strong> Dibayar satu kali saat pendaftaran</li>
                    <li><strong>Simpanan Wajib:</strong> Dibayar rutin setiap bulan</li>
                    <li><strong>Simpanan Sukarela:</strong> Dapat disetor/ditarik kapan saja</li>
                    <li><strong>Deposito:</strong> Simpanan berjangka dengan bunga</li>
                </ul>
                
                <div class="alert alert-warning small mt-3">
                    <strong>Catatan:</strong><br>
                    - Simpanan Pokok dan Wajib tidak dapat ditarik<br>
                    - Penarikan hanya untuk Simpanan Sukarela dan Deposito<br>
                    - Jurnal akan dibuat otomatis
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
    
    document.getElementById('jenisTransaksi').addEventListener('change', function() {
        var jenisSimpananSelect = document.getElementById('jenisSimpananSelect');
        var options = jenisSimpananSelect.querySelectorAll('option');
        
        if (this.value === 'tarik') {
            // Disable non-withdrawable types
            options.forEach(function(opt) {
                var tipe = opt.getAttribute('data-tipe');
                if (tipe === 'pokok' || tipe === 'wajib') {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        } else {
            options.forEach(function(opt) {
                opt.disabled = false;
            });
        }
    });
    
    document.getElementById('jenisSimpananSelect').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        var tipe = selected.getAttribute('data-tipe');
        var info = document.getElementById('tipeSimpananInfo');
        
        if (tipe === 'pokok') {
            info.textContent = 'Simpanan Pokok: Dibayar satu kali saat pendaftaran';
        } else if (tipe === 'wajib') {
            info.textContent = 'Simpanan Wajib: Dibayar rutin setiap bulan';
        } else if (tipe === 'sukarela') {
            info.textContent = 'Simpanan Sukarela: Dapat disetor dan ditarik kapan saja';
        } else if (tipe === 'deposito') {
            info.textContent = 'Deposito: Simpanan berjangka dengan bunga';
        } else {
            info.textContent = '';
        }
    });
</script>
@endpush
