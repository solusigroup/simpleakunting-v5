@extends('layouts.app')

@section('title', 'Transfer Kas - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Transfer Dana Antar Kas/Bank</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kas.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('kas.storeTransfer') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="no_transaksi" class="form-label">No Transaksi</label>
                            <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" value="{{ $noTransaksi }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_cabang" class="form-label">Cabang <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_cabang') is-invalid @enderror" id="id_cabang" name="id_cabang" required>
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach($cabang as $c)
                                            <option value="{{ $c->id }}" {{ old('id_cabang', session('active_cabang') ?: auth()->user()->id_cabang) == $c->id ? 'selected' : '' }}>{{ $c->kode_cabang }} - {{ $c->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_unit_usaha" class="form-label">Unit Usaha <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_unit_usaha') is-invalid @enderror" id="id_unit_usaha" name="id_unit_usaha" required>
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach($unitUsaha as $u)
                                            <option value="{{ $u->id }}" data-cabang="{{ $u->id_cabang }}" {{ old('id_unit_usaha', session('active_unit')) == $u->id ? 'selected' : '' }}>{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dari_akun" class="form-label">Dari Akun (Sumber)</label>
                            <select class="form-select" id="dari_akun" name="dari_akun" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ke_akun" class="form-label">Ke Akun (Tujuan)</label>
                            <select class="form-select" id="ke_akun" name="ke_akun" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akunKas as $a)
                                    <option value="{{ $a->kode_akun }}">{{ $a->kode_akun }} - {{ $a->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Transfer</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proses Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Cascade Cabang -> Unit Usaha
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
