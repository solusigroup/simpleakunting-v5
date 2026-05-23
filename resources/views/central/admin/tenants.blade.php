@extends('central.admin.layout')

@section('title', 'Manajemen Tenant')

@section('content')
    <h1>🏢 Manajemen Tenant</h1>
    <p class="subtitle">Kelola semua perusahaan yang terdaftar</p>

    <div class="actions">
        <a href="{{ route('central.register-tenant') }}">+ Tenant Baru</a>
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
                        @if($tenant->heartbeat_domain)
                            <a href="https://{{ $tenant->heartbeat_domain }}" class="domain-link" target="_blank">{{ $tenant->heartbeat_domain }}</a>
                            <div style="font-size: 0.8em; color: #4caf50; margin-top: 2px;">(Active/Reported)</div>
                        @else
                            @foreach($tenant->domains as $domain)
                                @php $fullDomain = $domain->domain . '.' . request()->getHost(); @endphp
                                <a href="https://{{ $fullDomain }}" class="domain-link" target="_blank">{{ $fullDomain }}</a>
                            @endforeach
                            <div style="font-size: 0.8em; color: #999; margin-top: 2px;">(Default)</div>
                        @endif
                    </td>
                    <td>
                        @php
                            $lastSeen = $tenant->last_seen_at ? \Carbon\Carbon::parse($tenant->last_seen_at) : null;
                            $hoursDiff = $lastSeen ? $lastSeen->diffInHours(\Carbon\Carbon::now()) : null;
                        @endphp
                        
                        <div style="margin-bottom: 4px;">
                            @if($lastSeen === null || $hoursDiff > 72)
                                <span class="badge" style="background-color: #f44336; color: white;">Offline</span>
                            @elseif($hoursDiff >= 24 && $hoursDiff <= 72)
                                <span class="badge" style="background-color: #ff9800; color: white;">Idle</span>
                            @else
                                <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                            @endif
                        </div>
                        <div style="font-size: 0.85em; color: #666; white-space: nowrap;">
                            Last Seen:<br>
                            {{ $lastSeen ? $lastSeen->format('d M Y, H:i') : 'Never' }}
                        </div>
                    </td>
                    <td>{{ $tenant->created_at?->format('d M Y') }}</td>
                    <td class="table-actions">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('central.tenants.show', $tenant->id) }}" class="btn-view">Lihat</a>
                            <a href="{{ route('central.tenants.edit', $tenant->id) }}" class="btn-edit">Edit</a>
                            <form method="POST" action="{{ route('central.tenants.destroy', $tenant->id) }}" onsubmit="return confirmDelete(this, '{{ $tenant->id }}')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="confirm_name" value="">
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

@section('scripts')
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
@endsection
