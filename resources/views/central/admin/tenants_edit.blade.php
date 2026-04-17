@extends('central.admin.layout')

@section('title', 'Edit Tenant')

@section('content')
    <h1>✏️ Edit Tenant</h1>
    <p class="subtitle">Perbarui informasi dan kredensial untuk tenant <strong>{{ $tenant->id }}</strong></p>

    <div class="actions">
        <a href="{{ route('central.tenants.index') }}" class="secondary"> kembali ke Daftar</a>
    </div>

    <div class="form-card">
        <form action="{{ route('central.tenants.update', $tenant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>ID Tenant (Subdomain)</label>
                <input type="text" value="{{ $tenant->id }}" disabled style="opacity: 0.6; cursor: not-allowed;">
                <small style="color: #8fa8c8; font-size: 0.75rem;">ID Tenant tidak dapat diubah untuk menjaga integritas database dan domain.</small>
            </div>

            <div class="form-group">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan', $tenant->nama_perusahaan) }}" required>
                @error('nama_perusahaan') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Perusahaan / Kontak</label>
                <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 24px 0;">

            <div class="form-group">
                <label for="is_active">Status Akses</label>
                <select name="is_active" id="is_active" style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; color: #e0e0e0; font-size: 0.95rem; font-family: inherit; outline: none;">
                    <option value="1" {{ old('is_active', $tenant->is_active) ? 'selected' : '' }}>🟢 Aktif</option>
                    <option value="0" {{ !old('is_active', $tenant->is_active) ? 'selected' : '' }}>🔴 Nonaktif (Blokir Akses)</option>
                </select>
                @error('is_active') <div class="error">{{ $message }}</div> @enderror
            </div>

            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 24px 0;">
            <p style="color: #ff8c00; font-size: 0.85rem; font-weight: 600; margin-bottom: 12px;">🔐 Sinkronisasi Kredensial Admin</p>

            <div class="form-group">
                <label for="admin_username">Username Admin Tenant</label>
                <input type="text" name="admin_username" id="admin_username" value="{{ old('admin_username', $tenant->admin_username) }}" required>
                @error('admin_username') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="admin_password">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                <input type="password" name="admin_password" id="admin_password" placeholder="Minimal 8 karakter">
                @error('admin_password') <div class="error">{{ $message }}</div> @enderror
                <small style="color: #8fa8c8; font-size: 0.75rem;">Password akan disinkronkan langsung ke user admin di database tenant terkait.</small>
            </div>

            <div style="margin-top: 32px;">
                <button type="submit" class="btn-submit">Simpan Perubahan & Sinkronkan</button>
            </div>
        </form>
    </div>
@endsection
