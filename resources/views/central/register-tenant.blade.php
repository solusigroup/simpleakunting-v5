<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perusahaan Baru - SimpleAkunting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1b2a 100%); color: #e0e0e0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 40px; width: 100%; max-width: 480px; margin: 24px; }
        .card h1 { color: #ff8c00; font-size: 1.5rem; margin-bottom: 8px; }
        .card p.subtitle { color: #8fa8c8; font-size: 0.9rem; margin-bottom: 28px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #a0b4d0; font-size: 0.85rem; margin-bottom: 6px; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 0.95rem; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #ff8c00; }
        .form-group .hint { color: #5a7090; font-size: 0.78rem; margin-top: 4px; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-1px); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #8fa8c8; text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: #ff8c00; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-danger { background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff6b6b; }
        .alert-danger ul { list-style: none; padding: 0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🏢 Daftar Perusahaan Baru</h1>
        <p class="subtitle">Buat tenant baru dengan database terpisah</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('central.register-tenant.store') }}">
            @csrf
            <div class="form-group">
                <label for="tenant_id">Tenant ID (Subdomain)</label>
                <input type="text" id="tenant_id" name="tenant_id" value="{{ old('tenant_id') }}" placeholder="perusahaan-abc" required>
                <div class="hint">Akan menjadi subdomain: <strong>perusahaan-abc.{{ request()->getHost() }}</strong></div>
            </div>

            <div class="form-group">
                <label for="nama_perusahaan">Nama Perusahaan</label>
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" placeholder="PT Contoh Sejahtera" required>
            </div>

            <div class="form-group">
                <label for="email">Email (opsional)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@perusahaan.com">
            </div>

            <button type="submit" class="btn">Buat Tenant →</button>
        </form>

        <a href="{{ route('central.landing') }}" class="back-link">← Kembali ke Beranda</a>
    </div>
</body>
</html>
