@extends('layouts.app')

@section('title', 'Profil Perusahaan - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Profil Perusahaan</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('perusahaan.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan ?? '') }}" required>
                            @error('nama_perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $perusahaan->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $perusahaan->telepon ?? '') }}">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $perusahaan->email ?? '') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_mulai_pemakaian" class="form-label">Tanggal Pertama Kali Pemakaian Aplikasi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_mulai_pemakaian') is-invalid @enderror" id="tanggal_mulai_pemakaian" name="tanggal_mulai_pemakaian" value="{{ old('tanggal_mulai_pemakaian', $perusahaan->tanggal_mulai_pemakaian ?? '') }}">
                            @error('tanggal_mulai_pemakaian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tidak diperkenankan menginput transaksi sebelum tanggal ini. Digunakan untuk mencegah backdate.</small>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_usaha" class="form-label">Jenis Usaha / Tipe COA <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_usaha') is-invalid @enderror" id="jenis_usaha" name="jenis_usaha" required>
                                <option value="dagang" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? 'dagang') == 'dagang' ? 'selected' : '' }}>
                                    Usaha Dagang (COA Dagang)
                                </option>
                                <option value="simpan_pinjam" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'simpan_pinjam' ? 'selected' : '' }}>
                                    Koperasi Simpan Pinjam (COA Simpan Pinjam)
                                </option>
                                <option value="serba_usaha" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'serba_usaha' ? 'selected' : '' }}>
                                    Koperasi Serba Usaha (COA Dagang + Simpan Pinjam)
                                </option>
                                <option value="jasa" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'jasa' ? 'selected' : '' }}>
                                    Usaha Jasa (Tanpa HPP)
                                </option>
                                <option value="manufaktur" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'manufaktur' ? 'selected' : '' }}>
                                    Perusahaan Manufaktur (BOM, Produksi, HPP)
                                </option>
                                <option value="pertanian" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'pertanian' ? 'selected' : '' }}>
                                    Usaha Pertanian/Peternakan (PSAK 69 Aset Biologis)
                                </option>
                                <option value="multi" {{ old('jenis_usaha', $perusahaan->jenis_usaha ?? '') == 'multi' ? 'selected' : '' }}>
                                    Multi Usaha (Gabungan Semuanya)
                                </option>
                            </select>
                            @error('jenis_usaha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Pilih jenis usaha untuk menentukan Chart of Accounts (COA) yang digunakan.
                            </small>
                        </div>

                        <h5 class="mt-4 mb-3">Pengaturan Akun <small class="text-muted">(Mapping COA)</small></h5>
                        <p class="text-muted small mb-3">Pilih akun default yang digunakan untuk setiap jenis transaksi. Biarkan kosong untuk menggunakan nilai default.</p>

                        <h6 class="text-primary">Akun Umum</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="akun_piutang" class="form-label">Piutang Usaha</label>
                                <select class="form-select" id="akun_piutang" name="akun_piutang">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_piutang ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="akun_utang" class="form-label">Utang Usaha</label>
                                <select class="form-select" id="akun_utang" name="akun_utang">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_utang ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="akun_pendapatan" class="form-label">Pendapatan Default</label>
                                <select class="form-select" id="akun_pendapatan" name="akun_pendapatan">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_pendapatan ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="akun_persediaan" class="form-label">Persediaan Default</label>
                                <select class="form-select" id="akun_persediaan" name="akun_persediaan">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_persediaan ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-primary mt-3">Tutup Buku</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="akun_ikhtisar_laba_rugi" class="form-label">Ikhtisar Laba Rugi</label>
                                <select class="form-select" id="akun_ikhtisar_laba_rugi" name="akun_ikhtisar_laba_rugi">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_ikhtisar_laba_rugi ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="akun_laba_ditahan" class="form-label">Laba Ditahan / Saldo Laba</label>
                                <select class="form-select" id="akun_laba_ditahan" name="akun_laba_ditahan">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_laba_ditahan ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-primary mt-3">Pertanian / Aset Biologis (PSAK 69)</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="akun_aset_biologis" class="form-label">Aset Biologis</label>
                                <select class="form-select" id="akun_aset_biologis" name="akun_aset_biologis">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_aset_biologis ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="akun_keuntungan_revaluasi" class="form-label">Keuntungan Revaluasi</label>
                                <select class="form-select" id="akun_keuntungan_revaluasi" name="akun_keuntungan_revaluasi">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_keuntungan_revaluasi ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="akun_kerugian_revaluasi" class="form-label">Kerugian Revaluasi</label>
                                <select class="form-select" id="akun_kerugian_revaluasi" name="akun_kerugian_revaluasi">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_kerugian_revaluasi ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-primary mt-3">Koperasi / Simpan Pinjam</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="akun_pendapatan_provisi" class="form-label">Pendapatan Provisi</label>
                                <select class="form-select" id="akun_pendapatan_provisi" name="akun_pendapatan_provisi">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_pendapatan_provisi ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="akun_pendapatan_admin" class="form-label">Pendapatan Admin</label>
                                <select class="form-select" id="akun_pendapatan_admin" name="akun_pendapatan_admin">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->akun_pendapatan_admin ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-primary mt-3">Point of Sales (POS)</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="pos_akun_kas_default" class="form-label">Kas POS Default</label>
                                <select class="form-select" id="pos_akun_kas_default" name="pos_akun_kas_default">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->pos_akun_kas_default ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pos_akun_pendapatan_default" class="form-label">Pendapatan POS Default</label>
                                <select class="form-select" id="pos_akun_pendapatan_default" name="pos_akun_pendapatan_default">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->pos_akun_pendapatan_default ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pos_akun_hpp_default" class="form-label">HPP POS Default</label>
                                <select class="form-select" id="pos_akun_hpp_default" name="pos_akun_hpp_default">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->pos_akun_hpp_default ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pos_akun_persediaan_default" class="form-label">Persediaan POS Default</label>
                                <select class="form-select" id="pos_akun_persediaan_default" name="pos_akun_persediaan_default">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->pos_akun_persediaan_default ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pos_akun_utang_default" class="form-label">Utang POS Default</label>
                                <select class="form-select" id="pos_akun_utang_default" name="pos_akun_utang_default">
                                    <option value="">-- Default --</option>
                                    @foreach($akun as $a)
                                        <option value="{{ $a->kode_akun }}" {{ ($perusahaan->pos_akun_utang_default ?? '') == $a->kode_akun ? 'selected' : '' }}>{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
