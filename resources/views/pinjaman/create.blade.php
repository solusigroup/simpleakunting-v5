@extends('layouts.app')

@section('title', 'Pengajuan Pinjaman - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pengajuan Pinjaman Baru</h1>
    <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary">
        <span data-feather="arrow-left"></span> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <strong>Form Pengajuan</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pinjaman.store') }}" id="formPinjaman">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Anggota <span class="text-danger">*</span></label>
                        <select name="id_anggota" class="form-select @error('id_anggota') is-invalid @enderror" required>
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
                        <label class="form-label">Jenis Pinjaman <span class="text-danger">*</span></label>
                        <select name="id_jenis_pinjaman" class="form-select @error('id_jenis_pinjaman') is-invalid @enderror" required id="jenisPinjamanSelect">
                            <option value="">Pilih Jenis Pinjaman...</option>
                            @foreach($jenisPinjaman as $jp)
                                <option value="{{ $jp->id_jenis_pinjaman }}" 
                                        data-bunga="{{ $jp->bunga_pertahun }}"
                                        data-metode="{{ $jp->metode_bunga }}"
                                        data-tenor-max="{{ $jp->tenor_max }}"
                                        data-plafon-max="{{ $jp->plafon_max }}"
                                        data-provisi="{{ $jp->provisi_persen }}"
                                        data-admin="{{ $jp->admin_fee }}"
                                        {{ old('id_jenis_pinjaman') == $jp->id_jenis_pinjaman ? 'selected' : '' }}>
                                    {{ $jp->nama_pinjaman }} ({{ ucfirst($jp->kategori) }}) - {{ $jp->bunga_pertahun }}% / {{ ucfirst($jp->metode_bunga) }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenis_pinjaman')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="jenisPinjamanInfo"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pinjaman <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_pinjaman" class="form-control @error('jumlah_pinjaman') is-invalid @enderror" 
                                           value="{{ old('jumlah_pinjaman') }}" min="1" step="1" required id="jumlahPinjaman">
                                    @error('jumlah_pinjaman')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted" id="plafonInfo"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tenor (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" name="tenor" class="form-control @error('tenor') is-invalid @enderror" 
                                       value="{{ old('tenor') }}" min="1" required id="tenorInput">
                                @error('tenor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="tenorInfo"></small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Bunga <span class="text-danger">*</span></label>
                        <select name="metode_bunga" class="form-select @error('metode_bunga') is-invalid @enderror" required id="metodeBungaSelect">
                            <option value="flat" {{ old('metode_bunga') == 'flat' ? 'selected' : '' }}>Flat (Bunga Tetap)</option>
                            <option value="anuitas" {{ old('metode_bunga') == 'anuitas' ? 'selected' : '' }}>Anuitas (Angsuran Tetap)</option>
                            <option value="efektif" {{ old('metode_bunga') == 'efektif' ? 'selected' : '' }}>Efektif / Sliding Rate</option>
                        </select>
                        @error('metode_bunga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Provisi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="provisi" class="form-control @error('provisi') is-invalid @enderror" 
                                           value="{{ old('provisi', 0) }}" min="0" step="1" required id="provisiInput">
                                    @error('provisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya Administrasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="biaya_admin" class="form-control @error('biaya_admin') is-invalid @enderror" 
                                           value="{{ old('biaya_admin', 0) }}" min="0" step="1" required id="biayaAdminInput">
                                    @error('biaya_admin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info" id="btnSimulasi">
                            <span data-feather="calculator"></span> Simulasi Angsuran
                        </button>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span data-feather="save"></span> Simpan Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <!-- Simulasi Result -->
        <div class="card mb-3" id="simulasiCard" style="display: none;">
            <div class="card-header bg-success text-white">
                <strong>Hasil Simulasi</strong>
            </div>
            <div class="card-body" id="simulasiResult">
                <!-- Will be filled by JS -->
            </div>
        </div>

        <!-- Info -->
        <div class="card bg-light">
            <div class="card-header">
                <strong>Informasi Metode Bunga</strong>
            </div>
            <div class="card-body small">
                <h6 class="text-primary">1. Metode Flat</h6>
                <p>Bunga dihitung dari pokok awal. Angsuran pokok dan bunga sama setiap bulan.</p>
                
                <h6 class="text-primary">2. Metode Anuitas</h6>
                <p>Angsuran tetap setiap bulan. Di awal bunga lebih besar, makin lama pokok makin besar.</p>
                
                <h6 class="text-primary">3. Metode Efektif (Sliding Rate)</h6>
                <p>Bunga dihitung dari sisa pokok. Angsuran menurun setiap bulan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
    
    // Update info when jenis pinjaman selected
    document.getElementById('jenisPinjamanSelect').addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        var bunga = opt.getAttribute('data-bunga');
        var metode = opt.getAttribute('data-metode');
        var tenorMax = opt.getAttribute('data-tenor-max');
        var plafonMax = opt.getAttribute('data-plafon-max');
        var provisi = opt.getAttribute('data-provisi');
        var admin = opt.getAttribute('data-admin');
        
        if (bunga) {
            document.getElementById('jenisPinjamanInfo').innerHTML = 
                'Bunga: ' + bunga + '% p.a. | Provisi: ' + provisi + '% | Admin: Rp ' + parseInt(admin).toLocaleString('id-ID');
            document.getElementById('plafonInfo').textContent = 'Plafon maks: Rp ' + parseInt(plafonMax).toLocaleString('id-ID');
            document.getElementById('tenorInfo').textContent = 'Tenor maks: ' + tenorMax + ' bulan';
            document.getElementById('metodeBungaSelect').value = metode;
        }
    });
    
    // Simulasi button
    document.getElementById('btnSimulasi').addEventListener('click', function() {
        var jumlah = document.getElementById('jumlahPinjaman').value;
        var tenor = document.getElementById('tenorInput').value;
        var metode = document.getElementById('metodeBungaSelect').value;
        var jenisPinjaman = document.getElementById('jenisPinjamanSelect');
        var opt = jenisPinjaman.options[jenisPinjaman.selectedIndex];
        var bunga = opt.getAttribute('data-bunga');
        
        if (!jumlah || !tenor || !bunga) {
            alert('Lengkapi data pinjaman terlebih dahulu');
            return;
        }
        
        // AJAX call to simulasi
        fetch('{{ route("pinjaman.simulasi") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                jumlah_pinjaman: parseFloat(jumlah),
                bunga_pertahun: parseFloat(bunga),
                tenor: parseInt(tenor),
                metode_bunga: metode
            })
        })
        .then(response => response.json())
        .then(data => {
            var html = '<table class="table table-sm table-bordered">';
            html += '<tr><td>Pokok Pinjaman</td><td class="text-end"><strong>Rp ' + parseInt(data.pokok).toLocaleString('id-ID') + '</strong></td></tr>';
            html += '<tr><td>Total Bunga</td><td class="text-end">Rp ' + parseInt(data.total_bunga).toLocaleString('id-ID') + '</td></tr>';
            html += '<tr><td>Total Angsuran</td><td class="text-end"><strong>Rp ' + parseInt(data.total_angsuran).toLocaleString('id-ID') + '</strong></td></tr>';
            html += '<tr><td>Angsuran/Bulan</td><td class="text-end text-primary"><strong>± Rp ' + parseInt(data.angsuran_per_bulan).toLocaleString('id-ID') + '</strong></td></tr>';
            html += '</table>';
            
            html += '<h6 class="mt-3">Jadwal Angsuran:</h6>';
            html += '<div style="max-height: 300px; overflow-y: auto;">';
            html += '<table class="table table-sm table-striped">';
            html += '<thead class="table-dark sticky-top"><tr><th>Ke</th><th class="text-end">Pokok</th><th class="text-end">Bunga</th><th class="text-end">Total</th></tr></thead>';
            html += '<tbody>';
            data.jadwal.forEach(function(j) {
                html += '<tr>';
                html += '<td>' + j.angsuran_ke + '</td>';
                html += '<td class="text-end">' + parseInt(j.pokok).toLocaleString('id-ID') + '</td>';
                html += '<td class="text-end">' + parseInt(j.bunga).toLocaleString('id-ID') + '</td>';
                html += '<td class="text-end"><strong>' + parseInt(j.total_angsuran).toLocaleString('id-ID') + '</strong></td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            
            document.getElementById('simulasiResult').innerHTML = html;
            document.getElementById('simulasiCard').style.display = 'block';
        })
        .catch(error => {
            alert('Gagal melakukan simulasi: ' + error.message);
        });
    });
</script>
@endpush
