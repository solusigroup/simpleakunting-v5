@extends('layouts.app')

@section('title', 'Audit Trail - Simple Akunting')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">📋 Audit Trail</h1>
        <span class="badge bg-primary" style="font-size: 0.9rem;">{{ $trails->total() }} records</span>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('audit-trail.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Aksi</label>
                    <select class="form-select form-select-sm" name="action">
                        <option value="">Semua</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <input type="text" class="form-control form-control-sm" name="user" value="{{ request('user') }}" placeholder="Nama user...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Model</label>
                    <input type="text" class="form-control form-control-sm" name="model" value="{{ request('model') }}" placeholder="Jurnal, Akun...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('audit-trail.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Waktu</th>
                            <th style="width: 120px;">User</th>
                            <th style="width: 80px;">Aksi</th>
                            <th style="width: 100px;">Model</th>
                            <th>Deskripsi</th>
                            <th style="width: 100px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trails as $trail)
                        <tr>
                            <td>
                                <small>{{ $trail->created_at->format('d/m/Y H:i:s') }}</small>
                            </td>
                            <td>
                                <small class="fw-bold">{{ $trail->user_name }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $trail->action_badge }}">
                                    {{ strtoupper($trail->action) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $trail->model_name }}</small>
                                @if($trail->model_id)
                                    <br><small class="text-muted">#{{ $trail->model_id }}</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ $trail->description }}</small>
                            </td>
                            <td>
                                @if($trail->old_values || $trail->new_values)
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $trail->id }}">
                                        <span data-feather="eye" style="width:14px;height:14px;"></span>
                                    </button>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @if($trail->old_values || $trail->new_values)
                        <tr class="collapse" id="detail-{{ $trail->id }}">
                            <td colspan="6" style="padding: 16px 20px;">
                                <div class="row g-3">
                                    @if($trail->old_values)
                                    <div class="col-md-6">
                                        <div style="background: #fff; border: 2px solid #dc3545; border-radius: 8px; overflow: hidden;">
                                            <div style="background: #dc3545; color: #fff; padding: 6px 12px; font-size: 0.8rem; font-weight: 600;">
                                                🔴 Data Lama
                                            </div>
                                            <pre style="font-size: 0.78rem; max-height: 220px; overflow-y: auto; margin: 0; padding: 10px 14px; color: #1e293b; background: #fff; white-space: pre-wrap; word-break: break-word;">{{ json_encode($trail->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                    @endif
                                    @if($trail->new_values)
                                    <div class="col-md-6">
                                        <div style="background: #fff; border: 2px solid #198754; border-radius: 8px; overflow: hidden;">
                                            <div style="background: #198754; color: #fff; padding: 6px 12px; font-size: 0.8rem; font-weight: 600;">
                                                🟢 Data Baru
                                            </div>
                                            <pre style="font-size: 0.78rem; max-height: 220px; overflow-y: auto; margin: 0; padding: 10px 14px; color: #1e293b; background: #fff; white-space: pre-wrap; word-break: break-word;">{{ json_encode($trail->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small style="color: #94a3b8;">🌐 IP: {{ $trail->ip_address }} | {{ Str::limit($trail->user_agent, 60) }}</small>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="text-muted">Belum ada data audit trail.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $trails->withQueryString()->links() }}
    </div>
@endsection
