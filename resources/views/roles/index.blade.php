@extends('layouts.app')

@section('title', 'Manajemen Role - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Role</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary">
                Tambah Role
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Nama Role</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">User</th>
                    <th scope="col">System?</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr>
                        <td><strong>{{ $role->display_name }}</strong><br><small class="text-muted">{{ $role->name }}</small></td>
                        <td>{{ $role->description }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            @if($role->is_system)
                                <span class="badge bg-info">System</span>
                            @else
                                <span class="badge bg-secondary">Custom</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if(!$role->is_system && $role->users_count == 0)
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada role.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
