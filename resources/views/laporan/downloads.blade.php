@extends('layouts.app')

@section('title', 'Hasil Unduhan Laporan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Hasil Unduhan Laporan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active">Unduhan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Antrean Laporan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Laporan</th>
                                    <th>Format</th>
                                    <th>Status</th>
                                    <th>Tanggal Request</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($downloads as $idx => $d)
                                    <tr>
                                        <td>{{ $downloads->firstItem() + $idx }}</td>
                                        <td>{{ $d->nama_laporan }}</td>
                                        <td><span class="badge badge-info">{{ strtoupper($d->tipe) }}</span></td>
                                        <td>
                                            @if($d->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($d->status == 'processing')
                                                <span class="badge badge-primary">Processing...</span>
                                            @elseif($d->status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($d->status == 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                                <br>
                                                <small class="text-muted">{{ $d->error_message }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $d->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($d->status == 'completed')
                                                <a href="{{ route('laporan.download_file', $d->id) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-default" disabled>
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada riwayat unduhan laporan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $downloads->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
