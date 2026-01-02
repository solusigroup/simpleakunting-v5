@extends('layouts.app')

@section('title', 'Tutup Buku / Closing')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Closing Periode (Tutup Buku)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('accounting.closing.create') }}" class="btn btn-sm btn-danger">
            <i class="bi bi-lock-fill"></i> Lakukan Tutup Buku
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Bulan/Tahun</th>
                <th>Tanggal Tutup</th>
                <th>Oleh</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($periodes as $p)
            <tr>
                <td>{{ $p->bulan }} / {{ $p->tahun }}</td>
                <td>{{ $p->tanggal_tutup }}</td>
                <td>{{ $p->user->nama_user ?? '-' }}</td>
                <td><span class="badge bg-danger">{{ strtoupper($p->status) }}</span></td>
                <td>{{ $p->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada history tutup buku.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
