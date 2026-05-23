@extends('layouts.app')

@section('title', 'Detail Jurnal - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Jurnal #{{ $jurnal->no_transaksi }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('jurnal.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Deskripsi</strong></td>
                            <td>: {{ $jurnal->deskripsi }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sumber</strong></td>
                            <td>: {{ $jurnal->sumber_jurnal }}</td>
                        </tr>
                        @if($jurnal->id_project)
                        <tr>
                            <td><strong>Proyek / Program</strong></td>
                            <td>: <strong>{{ $jurnal->project->kode_project }}</strong> - {{ $jurnal->project->nama_project }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <h5 class="mt-3">Rincian Akun</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal->details as $detail)
                            <tr>
                                <td>{{ $detail->kode_akun }}</td>
                                <td>{{ $detail->akun->nama_akun ?? 'Akun Terhapus' }}</td>
                                <td class="text-end">Rp {{ number_format($detail->debit, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($detail->kredit, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">Rp {{ number_format($jurnal->details->sum('debit'), 2, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($jurnal->details->sum('kredit'), 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($jurnal->foto_bukti)
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <h5 class="mb-3">
                            <span data-feather="image" class="me-1"></span> Bukti Transaksi
                        </h5>
                        <div class="card bg-light border" style="max-width: 450px;">
                            <div class="card-body p-2 text-center">
                                <img src="{{ asset('storage/bukti_transaksi/' . $jurnal->foto_bukti) }}" alt="Bukti Transaksi" class="img-fluid rounded border mb-2" style="max-height: 350px; width: 100%; object-fit: contain;">
                                <div>
                                    <a href="{{ asset('storage/bukti_transaksi/' . $jurnal->foto_bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <span data-feather="external-link"></span> Buka Gambar Penuh
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
