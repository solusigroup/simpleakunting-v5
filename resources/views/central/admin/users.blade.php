@extends('central.admin.layout')

@section('title', 'Manajemen User Central')

@section('content')
    <h1>👥 Manajemen User Central</h1>
    <p class="subtitle">Kelola administrator sistem pusat</p>

    <div class="actions">
        <a href="{{ route('central.users.create') }}">+ Tambah User</a>
        <a href="{{ route('central.password.edit') }}" class="secondary">🔑 Ganti Password Saya</a>
    </div>

    @if($users->isEmpty())
        <div class="empty">
            <p>Belum ada user terdaftar.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Jabatan</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id_user }}</td>
                    <td><strong>{{ $user->nama_user }}</strong></td>
                    <td><span class="badge badge-superuser">{{ $user->role }}</span></td>
                    <td>{{ $user->jabatan ?? '-' }}</td>
                    <td>{{ $user->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        @if($user->id_user !== Auth::guard('central')->user()->id_user)
                        <form method="POST" action="{{ route('central.users.destroy', $user->id_user) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama_user }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Hapus</button>
                        </form>
                        @else
                            <span style="color: #5a7090; font-size: 0.8rem;">Anda</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
