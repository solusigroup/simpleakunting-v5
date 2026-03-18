@extends('central.admin.layout')

@section('title', 'Tambah User Central')

@section('content')
    <h1>➕ Tambah User Administrator</h1>
    <p class="subtitle">Buat akun administrator baru untuk sistem pusat</p>

    <div class="actions">
        <a href="{{ route('central.users.index') }}" class="secondary">← Kembali</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('central.users.store') }}">
            @csrf

            <div class="form-group">
                <label for="nama_user">Username</label>
                <input type="text" id="nama_user" name="nama_user" value="{{ old('nama_user') }}" required autocomplete="off" placeholder="Masukkan username">
                @error('nama_user') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="jabatan">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', 'Central Administrator') }}" placeholder="Jabatan user">
                @error('jabatan') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
            </div>

            <button type="submit" class="btn-submit">Simpan User</button>
        </form>
    </div>
@endsection
