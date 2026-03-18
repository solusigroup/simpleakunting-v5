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
