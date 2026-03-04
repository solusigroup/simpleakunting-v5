<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Tenant - SimpleAkunting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #0a1628, #1a2a4a, #0d1b2a); color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px 24px; }
        h1 { color: #ff8c00; font-size: 1.6rem; margin-bottom: 8px; }
        .subtitle { color: #8fa8c8; margin-bottom: 24px; }
        .actions { margin-bottom: 24px; display: flex; gap: 12px; }
        .actions a { padding: 10px 20px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; }
        .actions a.secondary { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); }
        
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: rgba(40,167,69,0.15); border: 1px solid rgba(40,167,69,0.3); color: #51cf66; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.04); border-radius: 12px; overflow: hidden; }
        thead th { background: rgba(255,255,255,0.06); padding: 14px 16px; text-align: left; color: #a0b4d0; font-size: 0.85rem; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.08); }
        tbody td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.9rem; }
        tbody tr:hover { background: rgba(255,255,255,0.03); }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-active { background: rgba(40,167,69,0.2); color: #51cf66; }
        .badge-inactive { background: rgba(220,53,69,0.2); color: #ff6b6b; }
        .domain-link { color: #5b9fff; text-decoration: none; }
        .domain-link:hover { text-decoration: underline; }
        .btn-delete { background: none; border: 1px solid rgba(220,53,69,0.4); color: #ff6b6b; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; }
        .btn-delete:hover { background: rgba(220,53,69,0.15); }
        .empty { text-align: center; padding: 48px; color: #5a7090; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 Manajemen Tenant</h1>
        <p class="subtitle">Kelola semua perusahaan yang terdaftar</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert" style="background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff6b6b;">{{ session('error') }}</div>
        @endif

        <div class="actions">
            <a href="{{ route('central.register-tenant') }}">+ Tenant Baru</a>
            <a href="{{ route('central.landing') }}" class="secondary">← Beranda</a>
        </div>

        @if($tenants->isEmpty())
            <div class="empty">
                <p>Belum ada tenant terdaftar.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Perusahaan</th>
                        <th>Domain</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    <tr>
                        <td><strong>{{ $tenant->id }}</strong></td>
                        <td>{{ $tenant->nama_perusahaan ?? '-' }}</td>
                        <td>
                            @foreach($tenant->domains as $domain)
                                @php $fullDomain = $domain->domain . '.' . request()->getHost(); @endphp
                                <a href="http://{{ $fullDomain }}" class="domain-link" target="_blank">{{ $fullDomain }}</a>
                            @endforeach
                        </td>
                        <td>
                            @if($tenant->is_active)
                                <span class="badge badge-active">Aktif</span>
                            @else
                                <span class="badge badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $tenant->created_at?->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('central.tenants.destroy', $tenant->id) }}" onsubmit="return confirmDelete(this, '{{ $tenant->id }}')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="confirm_name" value="">
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        function confirmDelete(form, tenantId) {
            var input = prompt(
                '⚠️ PERINGATAN: Menghapus tenant "' + tenantId + '" akan menghapus SELURUH database dan data!\n\n' +
                'Tindakan ini TIDAK BISA dibatalkan.\n\n' +
                'Ketik "' + tenantId + '" untuk konfirmasi:'
            );
            if (input === null) return false;
            if (input !== tenantId) {
                alert('Konfirmasi tidak cocok. Penghapusan dibatalkan.');
                return false;
            }
            form.querySelector('input[name="confirm_name"]').value = input;
            return true;
        }
    </script>
</body>
</html>
