@extends('central.admin.layout')

@section('title', 'Ganti Password')

@section('content')
    <h1>🔑 Ganti Password</h1>
    <p class="subtitle">Ubah password akun Anda: <strong>{{ Auth::guard('central')->user()->nama_user }}</strong></p>

    <div class="actions">
        <a href="{{ route('central.users.index') }}" class="secondary">← Kembali</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('central.password.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Password Lama</label>
                <input type="password" id="current_password" name="current_password" required placeholder="Masukkan password saat ini">
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn-submit">Ubah Password</button>
        </form>
    </div>
@endsection
