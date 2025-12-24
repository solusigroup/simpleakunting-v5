@extends('layouts.app')

@section('title', 'Bayar Angsuran - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pembayaran Angsuran</h1>
    <a href="{{ route('pinjaman.show', $pinjaman->id_pinjaman) }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Informasi Pinjaman</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>No. Pinjaman:</span>
                    <strong>{{ $pinjaman->no_pinjaman }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Anggota:</span>
                    <strong>{{ $pinjaman->anggota->nama_lengkap }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sisa Pokok:</span>
                    <strong class="text-danger">Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sisa Bunga:</span>
                    <strong>Rp {{ number_format($pinjaman->sisa_bunga, 0, ',', '.') }}</strong>
                </li>
            </ul>
        </div>

        <!-- Jadwal yang belum bayar -->
        <div class="card">
            <div class="card-header bg-warning">
                <strong>Angsuran yang Belum Dibayar</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Ke</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pinjaman->jadwal as $j)
                                @if($j->status != 'lunas')
                                <tr class="{{ $j->is_overdue ? 'table-danger' : '' }}">
                                    <td>{{ $j->angsuran_ke }}</td>
                                    <td>
                                        {{ $j->tanggal_jatuh_tempo->format('d/m/Y') }}
                                        @if($j->is_overdue)
                                            <br><small class="text-danger">Telat {{ $j->days_overdue }} hari</small>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($j->total_angsuran, 0, ',', '.') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="selectJadwal({{ $j->id_jadwal }}, {{ $j->pokok }}, {{ $j->bunga }}, {{ $j->total_angsuran }})">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-success text-white">
                <strong>Form Pembayaran</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pinjaman.bayar', $pinjaman->id_pinjaman) }}">
                    @csrf

                    <input type="hidden" name="id_jadwal" id="idJadwal">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                       value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pokok Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="pokok_dibayar" id="pokokDibayar" class="form-control" 
                                           value="{{ old('pokok_dibayar', 0) }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Bunga Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="bunga_dibayar" id="bungaDibayar" class="form-control" 
                                           value="{{ old('bunga_dibayar', 0) }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Denda</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="denda" id="denda" class="form-control" 
                                           value="{{ old('denda', 0) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Total Bayar</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="totalBayar" class="form-control form-control-lg text-end bg-light" 
                                           readonly value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <span data-feather="check-circle"></span> Proses Pembayaran
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
    
    function selectJadwal(id, pokok, bunga, total) {
        document.getElementById('idJadwal').value = id;
        document.getElementById('pokokDibayar').value = pokok;
        document.getElementById('bungaDibayar').value = bunga;
        calculateTotal();
    }
    
    function calculateTotal() {
        var pokok = parseFloat(document.getElementById('pokokDibayar').value) || 0;
        var bunga = parseFloat(document.getElementById('bungaDibayar').value) || 0;
        var denda = parseFloat(document.getElementById('denda').value) || 0;
        var total = pokok + bunga + denda;
        document.getElementById('totalBayar').value = total.toLocaleString('id-ID');
    }
    
    document.getElementById('pokokDibayar').addEventListener('input', calculateTotal);
    document.getElementById('bungaDibayar').addEventListener('input', calculateTotal);
    document.getElementById('denda').addEventListener('input', calculateTotal);
</script>
@endpush
