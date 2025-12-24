@extends('layouts.app')

@section('title', 'Manajemen Database - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <span data-feather="database"></span> Manajemen Database
    </h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="alert alert-danger">
    <strong><span data-feather="alert-triangle"></span> PERINGATAN!</strong><br>
    Fitur ini hanya untuk superuser. Operasi di halaman ini bersifat <strong>PERMANEN</strong> dan <strong>TIDAK DAPAT DIBATALKAN</strong>.
</div>

<div class="row">
    <!-- Database Info -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong><span data-feather="info"></span> Informasi Database</strong>
            </div>
            <div class="card-body">
                <p><strong>Nama Database:</strong> {{ $dbName }}</p>
                <p><strong>Total Tabel:</strong> {{ count($tableInfo) }}</p>
                
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Tabel</th>
                                <th class="text-end">Jumlah Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tableInfo as $table)
                            <tr>
                                <td>{{ $table['name'] }}</td>
                                <td class="text-end">{{ number_format($table['rows']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="col-md-6">
        <!-- Run Seeder -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <strong><span data-feather="play"></span> Jalankan Seeder</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">Menjalankan seeder untuk mengisi data awal.</p>
                <form method="POST" action="{{ route('database.seed') }}">
                    @csrf
                    <div class="input-group">
                        <select name="seeder" class="form-select">
                            <option value="DatabaseSeeder">DatabaseSeeder (Semua)</option>
                            <option value="CoaDagangSeeder">COA Dagang (Usaha Dagang)</option>
                            <option value="CoaSimpanPinjamSeeder">COA Simpan Pinjam (Koperasi)</option>
                            <option value="JenisSimpanPinjamSeeder">Jenis Simpanan & Pinjaman</option>
                        </select>
                        <button type="submit" class="btn btn-success">
                            <span data-feather="play"></span> Jalankan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Truncate Data -->
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning">
                <strong><span data-feather="trash"></span> Hapus Semua Data</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">Menghapus semua data dari tabel, tetapi struktur tabel tetap ada. Tabel users tidak akan dihapus.</p>
                <form method="POST" action="{{ route('database.truncate') }}" onsubmit="return confirmAction(this, 'HAPUS DATA')">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="confirmation" class="form-control" 
                               placeholder="Ketik 'HAPUS DATA' untuk konfirmasi">
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <span data-feather="trash"></span> Hapus Semua Data
                    </button>
                </form>
            </div>
        </div>

        <!-- Fresh Migration -->
        <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
                <strong><span data-feather="refresh-cw"></span> Reset Database (Migrate Fresh)</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">Menghapus semua tabel dan menjalankan ulang semua migration. <strong>SEMUA DATA AKAN HILANG!</strong></p>
                <form method="POST" action="{{ route('database.fresh') }}" onsubmit="return confirmAction(this, 'RESET DATABASE')">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="confirmation" class="form-control" 
                               placeholder="Ketik 'RESET DATABASE' untuk konfirmasi">
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <span data-feather="refresh-cw"></span> Reset Database
                    </button>
                </form>
            </div>
        </div>

        <!-- Drop Database -->
        <div class="card mb-3 border-dark">
            <div class="card-header bg-dark text-white">
                <strong><span data-feather="x-circle"></span> Hapus Database</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">Menghapus database sepenuhnya dan membuat ulang dari awal. <strong>GUNAKAN DENGAN SANGAT HATI-HATI!</strong></p>
                <form method="POST" action="{{ route('database.drop') }}" onsubmit="return confirmDrop()">
                    @csrf
                    <div class="mb-2">
                        <input type="text" name="confirmation" class="form-control" 
                               placeholder="Ketik 'HAPUS DATABASE' untuk konfirmasi">
                    </div>
                    <div class="mb-2">
                        <input type="password" name="password" class="form-control" 
                               placeholder="Masukkan password Anda">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">
                        <span data-feather="x-circle"></span> Hapus Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    feather.replace();
    
    function confirmAction(form, expectedText) {
        var input = form.querySelector('input[name="confirmation"]').value;
        if (input !== expectedText) {
            alert('Konfirmasi tidak sesuai. Ketik "' + expectedText + '" untuk melanjutkan.');
            return false;
        }
        return confirm('PERINGATAN: Tindakan ini tidak dapat dibatalkan!\n\nApakah Anda yakin ingin melanjutkan?');
    }
    
    function confirmDrop() {
        var form = event.target;
        var confirmInput = form.querySelector('input[name="confirmation"]').value;
        var passwordInput = form.querySelector('input[name="password"]').value;
        
        if (confirmInput !== 'HAPUS DATABASE') {
            alert('Konfirmasi tidak sesuai. Ketik "HAPUS DATABASE" untuk melanjutkan.');
            return false;
        }
        
        if (!passwordInput) {
            alert('Masukkan password Anda untuk konfirmasi.');
            return false;
        }
        
        return confirm('⚠️ PERINGATAN SERIUS! ⚠️\n\nAnda akan menghapus SELURUH DATABASE!\nTindakan ini TIDAK DAPAT DIBATALKAN!\n\nApakah Anda BENAR-BENAR yakin?');
    }
</script>
@endpush
