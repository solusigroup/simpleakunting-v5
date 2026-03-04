@extends('layouts.app')

@section('title', 'Penarikan Simpanan - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Penarikan Simpanan</h1>
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
                                <label class="form-label">Cabang <span class="text-danger">*</span></label>
                                <select name="id_cabang" id="id_cabang" class="form-select @error('id_cabang') is-invalid @enderror" required>
                                    <option value="">Pilih Cabang...</option>
                                    @foreach($cabang as $c)
                                        <option value="{{ $c->id }}" {{ old('id_cabang', auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                                <select name="id_unit_usaha" id="id_unit_usaha" class="form-select @error('id_unit_usaha') is-invalid @enderror" required>
                                    <option value="">Pilih Unit...</option>
                                    @foreach($unitUsaha as $u)
                                        <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha') == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
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
                                <select name="jenis_transaksi" class="form-select @error('jenis_transaksi') is-invalid @enderror" required id="jenisTransaksi" readonly>
                                    <option value="tarik" selected>Tarik</option>
                                </select>
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                           value="{{ old('jumlah') }}" min="1" step="1" required>
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
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="2">{{ old('keterangan') }}</textarea>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('simpanan.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span data-feather="save"></span> Simpan Penarikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
    
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
</script>
@endpush
