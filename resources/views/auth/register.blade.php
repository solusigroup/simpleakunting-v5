<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Simple Akunting</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        📊
                    </div>
                    <h1 class="auth-title">Simple Akunting</h1>
                    <p class="auth-subtitle">Buat akun baru</p>
                </div>
                
                <div class="auth-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group">
                            <label for="nama_user" class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nama_user" 
                                   name="nama_user" 
                                   value="{{ old('nama_user') }}" 
                                   placeholder="Masukkan username"
                                   required 
                                   autofocus>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Ulangi password"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="manajer">Manajer</option>
                                <option value="staff" selected>Staff</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="jabatan" 
                                   name="jabatan" 
                                   value="{{ old('jabatan') }}" 
                                   placeholder="Masukkan jabatan"
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            Daftar
                        </button>
                    </form>
                </div>
                
                <div class="auth-footer">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                </div>
            </div>
            
            <div class="auth-credit">
                <p>Developed by <a href="https://simpleakunting.my.id" target="_blank">Kurniawan</a></p>
            </div>
        </div>
    </div>
</body>
</html>
