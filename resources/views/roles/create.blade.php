@extends('layouts.app')

@section('title', 'Tambah Role - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Role Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">Informasi Role</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Nama Role</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name') }}" required placeholder="Contoh: Staff Gudang">
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Izin Akses (Permissions)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                            <label class="form-check-label" for="checkAll" style="font-size: 0.8rem;">Pilih Semua</label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Modul</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Create</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $module => $perms)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $module)) }}</td>
                                            @php
                                                $modulePerms = $perms->keyBy(function($p) {
                                                    return explode('.', $p->name)[1];
                                                });
                                            @endphp
                                            @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                                <td class="text-center">
                                                    @if(isset($modulePerms[$action]))
                                                        <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="{{ $modulePerms[$action]->id }}">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary px-5">Simpan Role</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('checkAll').addEventListener('change', function() {
        const checks = document.querySelectorAll('.perm-check');
        checks.forEach(c => c.checked = this.checked);
    });
</script>
@endpush
