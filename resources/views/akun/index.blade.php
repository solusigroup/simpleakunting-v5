@extends('layouts.app')

@section('title', 'Daftar Akun - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Akun (Chart of Accounts)</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#duplicatePanel" aria-expanded="false">
                <span data-feather="search"></span> Cek Duplikasi
                @if($duplicates->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $duplicates->count() }}</span>
                @endif
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span data-feather="upload-cloud"></span> Import/Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('import-export.export', 'akun') }}"><span data-feather="download"></span> Export CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.template', 'akun') }}"><span data-feather="file"></span> Download Template</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('import-export.index') }}"><span data-feather="upload"></span> Import Data</a></li>
                </ul>
            </div>
            <a href="{{ route('akun.create') }}" class="btn btn-sm btn-primary">
                Tambah Akun
            </a>
        </div>
    </div>

    {{-- Panel Hasil Cek Duplikasi --}}
    <div class="collapse mb-3" id="duplicatePanel">
        @if($duplicates->count() > 0)
            <div class="alert alert-warning border-warning mb-0">
                <h6 class="fw-bold mb-2"><span data-feather="alert-triangle" style="width:16px;height:16px;"></span> Ditemukan {{ $duplicates->count() }} Nama Akun Duplikat</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 small bg-white">
                        <thead class="table-warning">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Nama Akun</th>
                                <th>Kode Akun Terkait</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($duplicates as $namaAkun => $group)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-danger">{{ $group->first()->nama_akun }}</td>
                                    <td>
                                        @foreach($group as $item)
                                            <span class="badge bg-secondary me-1">{{ $item->kode_akun }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center"><span class="badge bg-danger">{{ $group->count() }}x</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted mt-2 d-block">Periksa dan perbaiki akun-akun di atas agar setiap nama akun bersifat unik untuk menghindari kekeliruan pada laporan keuangan.</small>
            </div>
        @else
            <div class="alert alert-success border-success mb-0">
                <span data-feather="check-circle" style="width:16px;height:16px;"></span> <strong>Tidak ada duplikasi.</strong> Semua nama akun sudah unik.
            </div>
        @endif
    </div>

    <div class="mb-3">
        <div class="input-group input-group-sm" style="max-width: 400px;">
            <span class="input-group-text bg-white"><span data-feather="search" style="width:14px;height:14px;"></span></span>
            <input type="text" id="searchAkun" class="form-control" placeholder="Cari kode akun atau nama akun..." autofocus>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm" id="tabelAkun">
            <thead>
                <tr>
                    <th scope="col">Kode Akun</th>
                    <th scope="col">Nama Akun</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Saldo Normal</th>
                    <th scope="col" class="text-end">Saldo Awal</th>
                    <th scope="col" class="text-end">Saldo Terkini</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($akun as $a)
                    <tr>
                        <td>{{ $a->kode_akun }}</td>
                        <td>{{ $a->nama_akun }}</td>
                        <td>{{ $a->tipe_akun }}</td>
                        <td>
                            <span class="badge bg-{{ $a->saldo_normal == 'Debit' ? 'success' : 'danger' }}">
                                {{ $a->saldo_normal }}
                            </span>
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($a->saldo_awal ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold {{ $a->saldo_terkini < 0 ? 'text-danger' : '' }}">
                            Rp {{ number_format($a->saldo_terkini, 0, ',', '.') }}
                        </td>
                        <td>
                            <a href="{{ route('akun.edit', $a->kode_akun) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('akun.destroy', $a->kode_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data akun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('searchAkun');
        const table = document.getElementById('tabelAkun');
        const rows = table.querySelectorAll('tbody tr');

        input.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                const kode = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
                const nama = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                row.style.display = (kode.includes(keyword) || nama.includes(keyword)) ? '' : 'none';
            });
        });
    });
</script>
@endsection
