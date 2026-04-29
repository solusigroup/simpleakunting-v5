@extends('layouts.app')

@section('title', 'Pelepasan Aset Tetap - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Pelepasan / Penjualan Aset Tetap</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('aset-tetap.index') }}" class="btn btn-sm btn-secondary">
                Batal & Kembali
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Informasi Aset -->
        <div class="col-md-5 mb-4">
            <div class="card bg-light">
                <div class="card-header fw-bold">Informasi Aset: {{ $asset->kode_aset }}</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Nama Aset</td>
                            <td class="fw-bold">{{ $asset->nama_aset }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kelompok</td>
                            <td class="fw-bold">{{ $asset->group->nama_kelompok ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tgl Perolehan</td>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($asset->tanggal_perolehan)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Perolehan</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($asset->harga_perolehan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nilai Buku Saat Ini</td>
                            <td class="fw-bold text-success">Rp {{ number_format($asset->nilai_buku_saat_ini, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Akumulasi Susut</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($asset->harga_perolehan - $asset->nilai_buku_saat_ini, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Pelepasan -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('aset-tetap.dispose.store', $asset->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_pelepasan" class="form-label">Tanggal Pelepasan</label>
                                <input type="date" class="form-control @error('tanggal_pelepasan') is-invalid @enderror" id="tanggal_pelepasan" name="tanggal_pelepasan" value="{{ old('tanggal_pelepasan', date('Y-m-d')) }}" required>
                                @error('tanggal_pelepasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jenis_pelepasan" class="form-label">Jenis Pelepasan</label>
                                <select class="form-select @error('jenis_pelepasan') is-invalid @enderror" id="jenis_pelepasan" name="jenis_pelepasan" required>
                                    <option value="Dijual" {{ old('jenis_pelepasan') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
                                    <option value="Dibuang" {{ old('jenis_pelepasan') == 'Dibuang' ? 'selected' : '' }}>Dibuang</option>
                                    <option value="Rusak" {{ old('jenis_pelepasan') == 'Rusak' ? 'selected' : '' }}>Rusak / Hilang</option>
                                </select>
                                @error('jenis_pelepasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="penjualan_fields">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="harga_jual" class="form-label">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual" name="harga_jual" value="{{ old('harga_jual', 0) }}" min="0">
                                    </div>
                                    @error('harga_jual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="akun_kas" class="form-label">Masuk ke Akun (Kas/Bank)</label>
                                    <select class="form-select select2 @error('akun_kas') is-invalid @enderror" id="akun_kas" name="akun_kas">
                                        <option value="">-- Pilih Akun Penerimaan --</option>
                                        @foreach($kasAkuns as $akun)
                                            <option value="{{ $akun->kode_akun }}" {{ old('akun_kas') == $akun->kode_akun ? 'selected' : '' }}>
                                                {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('akun_kas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="akun_laba_rugi_pelepasan" class="form-label">Akun Laba/Rugi Pelepasan Aset</label>
                            <select class="form-select select2 @error('akun_laba_rugi_pelepasan') is-invalid @enderror" id="akun_laba_rugi_pelepasan" name="akun_laba_rugi_pelepasan" required>
                                <option value="">-- Pilih Akun Laba/Rugi --</option>
                                @foreach($labaRugiAkuns as $akun)
                                    <option value="{{ $akun->kode_akun }}" {{ old('akun_laba_rugi_pelepasan') == $akun->kode_akun ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_laba_rugi_pelepasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Pilih akun tempat mencatat kerugian atau keuntungan dari penjualan/pelepasan aset ini.</div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <span data-feather="info" class="me-2"></span>
                            <small>Sistem akan secara otomatis membuat jurnal yang menghapus nilai buku aset, mengakui kas masuk (jika dijual), dan mengakui Laba/Rugi pelepasan.</small>
                        </div>

                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin akan melepaskan aset ini? Aksi ini akan mengubah nilai aset menjadi 0 dan membukukan jurnal pelepasan secara permanen.')">
                            Proses Pelepasan Aset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis_pelepasan');
        const penjualanFields = document.getElementById('penjualan_fields');

        function toggleFields() {
            if (jenisSelect.value === 'Dijual') {
                penjualanFields.style.display = 'block';
            } else {
                penjualanFields.style.display = 'none';
            }
        }

        jenisSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial check
    });
</script>
@endsection
