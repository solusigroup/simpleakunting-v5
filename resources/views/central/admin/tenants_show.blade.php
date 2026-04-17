@extends('central.admin.layout')

@section('title', 'Detail Tenant')

@section('content')
    <h1>🏢 Detail Tenant: {{ $tenant->id }}</h1>
    <p class="subtitle">Informasi teknis dan status operasional tenant</p>

    <div class="actions">
        <a href="{{ route('central.tenants.index') }}" class="secondary"> kembali ke Daftar</a>
        <a href="{{ route('central.tenants.edit', $tenant->id) }}">Edit Tenant</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <!-- Info Utama -->
        <div class="form-card" style="max-width: none;">
            <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 10px;">📋 Informasi Umum</h3>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">ID / Subdomain</label>
                <div style="font-size: 1.2rem; font-weight: 700; color: #ff8c00;">{{ $tenant->id }}</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Nama Perusahaan</label>
                <div style="font-size: 1rem; color: #fff;">{{ $tenant->nama_perusahaan }}</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Email Kontak</label>
                <div style="font-size: 1rem; color: #fff;">{{ $tenant->email ?? '-' }}</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Status Operasional</label>
                @if($tenant->is_active)
                    <span class="badge badge-active" style="font-size: 0.9rem; padding: 6px 16px;">🟢 AKTIF</span>
                @else
                    <span class="badge badge-inactive" style="font-size: 0.9rem; padding: 6px 16px;">🔴 NONAKTIF (DIBLOKIR)</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Tanggal Registrasi</label>
                <div style="font-size: 1rem; color: #fff;">{{ $tenant->created_at?->format('d F Y, H:i') }}</div>
            </div>
        </div>

        <!-- Info Teknis -->
        <div class="form-card" style="max-width: none;">
            <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 10px;">⚙️ Detail Teknis</h3>
            
            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Domain Terdaftar</label>
                @foreach($tenant->domains as $domain)
                    @php $fullDomain = $domain->domain . '.' . request()->getHost(); @endphp
                    <div style="margin-bottom: 4px;">
                        <a href="http://{{ $fullDomain }}" class="domain-link" target="_blank" style="font-size: 1rem;">{{ $fullDomain }}</a>
                    </div>
                @endforeach
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Database Name</label>
                <code style="background: rgba(0,0,0,0.3); padding: 4px 8px; border-radius: 4px; color: #51cf66;">{{ $tenant->tenancy_db_name ?? 'tenant_' . $tenant->id }}</code>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="color: #a0b4d0; font-size: 0.8rem; display: block; margin-bottom: 4px;">Admin Username (Pusat)</label>
                <div style="font-size: 1rem; color: #fff;">{{ $tenant->admin_username }}</div>
            </div>

            <div style="margin-top: 24px; padding: 12px; background: rgba(255,140,0,0.05); border-left: 3px solid #ff8c00; border-radius: 4px;">
                <p style="font-size: 0.8rem; color: #a0b4d0; line-height: 1.5;">
                    💡 Password admin tidak ditampilkan di sini demi alasan keamanan. Gunakan fitur <strong>Edit</strong> untuk mereset atau menyinkronkan ulang kredensial akses tenant.
                </p>
            </div>
        </div>
    </div>
@endsection
