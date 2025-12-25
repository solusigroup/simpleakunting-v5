@extends('layouts.app')

@section('title', 'Edit Jenis Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Jenis Pinjaman</h1>
    <a href="{{ route('jenis-pinjaman.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('jenis-pinjaman.update', $jenisPinjaman->id_jenis_pinjaman) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kode_pinjaman" class="form-label">Kode Pinjaman</label>
                            <input type="text" class="form-control @error('kode_pinjaman') is-invalid @enderror" 
                                id="kode_pinjaman" name="kode_pinjaman" 
                                value="{{ old('kode_pinjaman', $jenisPinjaman->kode_pinjaman) }}" required>
                            @error('kode_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama_pinjaman" class="form-label">Nama Pinjaman</label>
                            <input type="text" class="form-control @error('nama_pinjaman') is-invalid @enderror" 
                                id="nama_pinjaman" name="nama_pinjaman" 
                                value="{{ old('nama_pinjaman', $jenisPinjaman->nama_pinjaman) }}" required>
                            @error('nama_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                                <option value="produktif" {{ old('kategori', $jenisPinjaman->kategori) == 'produktif' ? 'selected' : '' }}>Produktif</option>
                                <option value="konsumtif" {{ old('kategori', $jenisPinjaman->kategori) == 'konsumtif' ? 'selected' : '' }}>Konsumtif</option>
                                <option value="darurat" {{ old('kategori', $jenisPinjaman->kategori) == 'darurat' ? 'selected' : '' }}>Darurat</option>
                            </select>
                            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bunga_pertahun" class="form-label">Bunga/Tahun (%)</label>
                            <input type="number" step="0.01" class="form-control @error('bunga_pertahun') is-invalid @enderror" 
                                id="bunga_pertahun" name="bunga_pertahun" 
                                value="{{ old('bunga_pertahun', $jenisPinjaman->bunga_pertahun) }}" required>
                            @error('bunga_pertahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="metode_bunga" class="form-label">Metode Bunga</label>
                            <select class="form-select @error('metode_bunga') is-invalid @enderror" id="metode_bunga" name="metode_bunga" required>
                                <option value="flat" {{ old('metode_bunga', $jenisPinjaman->metode_bunga) == 'flat' ? 'selected' : '' }}>Flat</option>
                                <option value="anuitas" {{ old('metode_bunga', $jenisPinjaman->metode_bunga) == 'anuitas' ? 'selected' : '' }}>Anuitas</option>
                                <option value="efektif" {{ old('metode_bunga', $jenisPinjaman->metode_bunga) == 'efektif' ? 'selected' : '' }}>Efektif</option>
                            </select>
                            @error('metode_bunga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tenor_max" class="form-label">Tenor Maksimal (Bulan)</label>
                            <input type="number" class="form-control @error('tenor_max') is-invalid @enderror" 
                                id="tenor_max" name="tenor_max" 
                                value="{{ old('tenor_max', $jenisPinjaman->tenor_max) }}" required>
                            @error('tenor_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="plafon_max" class="form-label">Plafon Maksimal</label>
                            <input type="number" class="form-control @error('plafon_max') is-invalid @enderror" 
                                id="plafon_max" name="plafon_max" 
                                value="{{ old('plafon_max', $jenisPinjaman->plafon_max) }}" required>
                            @error('plafon_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="provisi_persen" class="form-label">Provisi (%)</label>
                            <input type="number" step="0.01" class="form-control @error('provisi_persen') is-invalid @enderror" 
                                id="provisi_persen" name="provisi_persen" 
                                value="{{ old('provisi_persen', $jenisPinjaman->provisi_persen) }}">
                            @error('provisi_persen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>
                    <h5 class="text-primary">Pengaturan Akun</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="akun_piutang_pinjaman" class="form-label">Akun Piutang Pinjaman <span class="text-danger">*</span></label>
                            <select class="form-select @error('akun_piutang_pinjaman') is-invalid @enderror" 
                                id="akun_piutang_pinjaman" name="akun_piutang_pinjaman" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunPiutang as $akun)
                                    <option value="{{ $akun->kode_akun }}" 
                                        {{ old('akun_piutang_pinjaman', $jenisPinjaman->akun_piutang_pinjaman) == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_piutang_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($jenisPinjaman->akun_piutang_pinjaman && !$jenisPinjaman->akunPiutang)
                                <div class="text-danger small mt-1">
                                    ⚠️ Akun saat ini ({{ $jenisPinjaman->akun_piutang_pinjaman }}) tidak valid/terhapus!
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="akun_pendapatan_bunga" class="form-label">Akun Pendapatan Bunga <span class="text-danger">*</span></label>
                            <select class="form-select @error('akun_pendapatan_bunga') is-invalid @enderror" 
                                id="akun_pendapatan_bunga" name="akun_pendapatan_bunga" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunPendapatan as $akun)
                                    <option value="{{ $akun->kode_akun }}" 
                                        {{ old('akun_pendapatan_bunga', $jenisPinjaman->akun_pendapatan_bunga) == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_pendapatan_bunga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="akun_pendapatan_provisi" class="form-label">Akun Pendapatan Provisi</label>
                            <select class="form-select @error('akun_pendapatan_provisi') is-invalid @enderror" 
                                id="akun_pendapatan_provisi" name="akun_pendapatan_provisi">
                                <option value="">-- Pilih Akun (Opsional) --</option>
                                @foreach($akunPendapatan as $akun)
                                    <option value="{{ $akun->kode_akun }}" 
                                        {{ old('akun_pendapatan_provisi', $jenisPinjaman->akun_pendapatan_provisi) == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_pendapatan_provisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="akun_pendapatan_admin" class="form-label">Akun Pendapatan Admin</label>
                            <select class="form-select @error('akun_pendapatan_admin') is-invalid @enderror" 
                                id="akun_pendapatan_admin" name="akun_pendapatan_admin">
                                <option value="">-- Pilih Akun (Opsional) --</option>
                                @foreach($akunPendapatan as $akun)
                                    <option value="{{ $akun->kode_akun }}" 
                                        {{ old('akun_pendapatan_admin', $jenisPinjaman->akun_pendapatan_admin) == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_pendapatan_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $jenisPinjaman->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span data-feather="save"></span> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>feather.replace();</script>
@endpush
