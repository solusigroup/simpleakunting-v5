@extends('layouts.app')

@section('title', 'Detail Anggota - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Anggota</h1>
    <div class="btn-toolbar">
        <a href="{{ route('anggota.kartu', $anggota->id_anggota) }}" class="btn btn-success me-2">
            <span data-feather="credit-card"></span> Kartu Anggota
        </a>
        <a href="{{ route('anggota.edit', $anggota->id_anggota) }}" class="btn btn-warning me-2">
            <span data-feather="edit"></span> Edit
        </a>
        <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
            <span data-feather="arrow-left"></span> Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Info Anggota -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Informasi Anggota</strong>
            </div>
            <div class="card-body text-center">
                @if($anggota->foto)
                    <img src="{{ asset('storage/anggota/' . $anggota->foto) }}" alt="Foto" 
                         class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px;">
                        <span data-feather="user" style="width: 80px; height: 80px; color: white;"></span>
                    </div>
                @endif
                
                <h4>{{ $anggota->nama_lengkap }}</h4>
                <p class="text-muted mb-2">{{ $anggota->no_anggota }}</p>
                
                @if($anggota->status == 'aktif')
                    <span class="badge bg-success fs-6">Aktif</span>
                @elseif($anggota->status == 'non_aktif')
                    <span class="badge bg-warning fs-6">Non-Aktif</span>
                @else
                    <span class="badge bg-danger fs-6">Keluar</span>
                @endif
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>NIK:</span>
                    <strong>{{ $anggota->nik }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Jenis Kelamin:</span>
                    <strong>{{ $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Telepon:</span>
                    <strong>{{ $anggota->telepon ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Email:</span>
                    <strong>{{ $anggota->email ?? '-' }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Pekerjaan:</span>
                    <strong>{{ $anggota->pekerjaan ?? '-' }}</strong>
                </li>
                <li class="list-group-item">
                    <span>Alamat:</span><br>
                    <strong>{{ $anggota->alamat }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tgl Daftar:</span>
                    <strong>{{ $anggota->tanggal_daftar->format('d/m/Y') }}</strong>
                </li>
                @if($anggota->tanggal_keluar)
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tgl Keluar:</span>
                    <strong>{{ $anggota->tanggal_keluar->format('d/m/Y') }}</strong>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Ringkasan Simpanan & Pinjaman -->
    <div class="col-md-8">
        <!-- Simpanan -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <strong>Riwayat Simpanan</strong>
                <a href="{{ route('simpanan.index', ['anggota' => $anggota->id_anggota]) }}" class="btn btn-sm btn-light">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($anggota->simpanan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Transaksi</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggota->simpanan->take(5) as $s)
                            <tr>
                                <td>{{ $s->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $s->jenisSimpanan->nama_simpanan }}</td>
                                <td>
                                    @if($s->jenis_transaksi == 'setor')
                                        <span class="badge bg-success">Setor</span>
                                    @else
                                        <span class="badge bg-danger">Tarik</span>
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($s->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada data simpanan</p>
                @endif
            </div>
        </div>

        <!-- Pinjaman -->
        <div class="card mb-4">
            <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                <strong>Riwayat Pinjaman</strong>
                <a href="{{ route('pinjaman.index', ['anggota' => $anggota->id_anggota]) }}" class="btn btn-sm btn-light">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($anggota->pinjaman->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. Pinjaman</th>
                                <th>Jenis</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Sisa Pokok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggota->pinjaman->take(5) as $p)
                            <tr>
                                <td>{{ $p->no_pinjaman }}</td>
                                <td>{{ $p->jenisPinjaman->nama_pinjaman }}</td>
                                <td class="text-end">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($p->sisa_pokok, 0, ',', '.') }}</td>
                                <td>
                                    @switch($p->status)
                                        @case('active')
                                            <span class="badge bg-primary">Aktif</span>
                                            @break
                                        @case('lunas')
                                            <span class="badge bg-success">Lunas</span>
                                            @break
                                        @case('pending_approval')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($p->status) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Belum ada data pinjaman</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
