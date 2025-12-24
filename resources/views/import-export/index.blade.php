@extends('layouts.app')

@section('title', 'Import & Export Data - Simple Akunting')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <span data-feather="upload-cloud"></span> Import & Export Data
    </h1>
    <div class="btn-toolbar">
        <a href="{{ route('import-export.export-all') }}" class="btn btn-info">
            <span data-feather="download-cloud"></span> Export Semua Data
        </a>
    </div>
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

<div class="row">
    @foreach($modules as $key => $module)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <strong>{{ $module['label'] }}</strong>
                <span class="badge bg-light text-dark">{{ number_format($counts[$key]) }} data</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Export Section -->
                    <div class="col-md-6 border-end">
                        <h6><span data-feather="download"></span> Export</h6>
                        <p class="text-muted small">Download data dalam format CSV</p>
                        <a href="{{ route('import-export.export', $key) }}" class="btn btn-sm btn-success w-100 mb-2">
                            <span data-feather="download"></span> Export CSV
                        </a>
                        <a href="{{ route('import-export.template', $key) }}" class="btn btn-sm btn-outline-secondary w-100">
                            <span data-feather="file"></span> Download Template
                        </a>
                    </div>

                    <!-- Import Section -->
                    <div class="col-md-6">
                        <h6><span data-feather="upload"></span> Import</h6>
                        <p class="text-muted small">Upload file CSV untuk import</p>
                        <form action="{{ route('import-export.import', $key) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <input type="file" name="file" class="form-control form-control-sm" accept=".csv,.txt" required>
                            </div>
                            <div class="mb-2">
                                <select name="mode" class="form-select form-select-sm">
                                    <option value="append">Tambahkan ke data existing</option>
                                    <option value="replace">Ganti semua data (HATI-HATI!)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning w-100" onclick="return confirm('Yakin import data {{ $module['label'] }}?')">
                                <span data-feather="upload"></span> Import
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <strong>Kolom:</strong> {{ implode(', ', $module['columns']) }}
                </small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Instructions -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <strong><span data-feather="info"></span> Panduan Import/Export</strong>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Langkah Import:</h6>
                <ol class="small">
                    <li>Download template CSV untuk modul yang diinginkan</li>
                    <li>Buka file dengan Microsoft Excel atau aplikasi spreadsheet</li>
                    <li>Isi data sesuai kolom yang tersedia</li>
                    <li>Simpan file sebagai CSV (UTF-8)</li>
                    <li>Upload file CSV melalui form import</li>
                    <li>Pilih mode import (Tambah/Ganti)</li>
                    <li>Klik Import</li>
                </ol>
            </div>
            <div class="col-md-6">
                <h6>Catatan Penting:</h6>
                <ul class="small">
                    <li>Format file harus CSV dengan separator koma (,)</li>
                    <li>Baris pertama harus berisi nama kolom</li>
                    <li>Untuk data relasi (ID), pastikan data referensi sudah ada</li>
                    <li>Mode "Ganti" akan <strong class="text-danger">menghapus semua data existing</strong></li>
                    <li>Backup data sebelum import dengan mode "Ganti"</li>
                    <li>Maksimal ukuran file: 10MB</li>
                </ul>
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
