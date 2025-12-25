@extends('layouts.app')

@section('title', 'Dashboard - Simple Akunting')

@section('content')
    <!-- Page Header with Quick Add -->
    <div class="page-header-actions">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Selamat datang kembali, {{ Auth::user()->nama_user }}</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openSlidePanel('quickAddPanel')">
                <span data-feather="plus" style="width: 16px; height: 16px; margin-right: 4px;"></span>
                Tambah Transaksi
            </button>
        </div>
    </div>

    <!-- Summary Cards Row 1 - Hero Card -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="summary-card primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="summary-card-label mb-2">Total Saldo Kas & Bank</p>
                        <h2 class="mb-0" style="font-size: 2rem;">Rp {{ number_format($totalPiutang - $totalUtang, 0, ',', '.') }}</h2>
                    </div>
                    <div style="font-size: 48px; opacity: 0.3;">💰</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row 2 -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card success">
                <div class="d-flex align-items-start gap-3">
                    <div class="summary-card-icon success">
                        📈
                    </div>
                    <div>
                        <p class="summary-card-label">Total Piutang</p>
                        <h3 class="summary-card-value" style="color: var(--color-success);">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Tagihan ke pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card danger">
                <div class="d-flex align-items-start gap-3">
                    <div class="summary-card-icon danger">
                        📉
                    </div>
                    <div>
                        <p class="summary-card-label">Total Utang</p>
                        <h3 class="summary-card-value" style="color: var(--color-danger);">Rp {{ number_format($totalUtang, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Kewajiban ke pemasok</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card info">
                <div class="d-flex align-items-start gap-3">
                    <div class="summary-card-icon info">
                        📦
                    </div>
                    <div>
                        <p class="summary-card-label">Nilai Persediaan</p>
                        <h3 class="summary-card-value" style="color: var(--color-info);">Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Aset persediaan saat ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simpan Pinjam Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>🏦 Total Simpanan</span>
                    <span class="badge" style="background: var(--color-primary); color: white;">{{ $simpananByType->count() }} Jenis</span>
                </div>
                <div class="card-body">
                    <h3 style="color: var(--color-primary); margin-bottom: var(--space-md);">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                    
                    @if($simpananByType->count() > 0)
                    <div class="mt-3">
                        @foreach($simpananByType as $simpanan)
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--color-border-light);">
                            <div>
                                <span class="fw-bold">{{ $simpanan->nama_simpanan }}</span>
                                <small class="text-muted d-block">{{ ucfirst($simpanan->tipe) }}</small>
                            </div>
                            <span class="fw-bold {{ $simpanan->saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($simpanan->saldo, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">Belum ada data simpanan</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>💰 Pinjaman Aktif</span>
                    <span class="badge" style="background: var(--color-warning); color: white;">{{ $pinjamanByType->sum('jumlah_aktif') }} Pinjaman</span>
                </div>
                <div class="card-body">
                    <h3 style="color: var(--color-warning); margin-bottom: var(--space-md);">Rp {{ number_format($totalPinjamanAktif, 0, ',', '.') }}</h3>
                    
                    @if($pinjamanByType->count() > 0)
                    <div class="mt-3">
                        @foreach($pinjamanByType as $pinjaman)
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--color-border-light);">
                            <div>
                                <span class="fw-bold">{{ $pinjaman->nama_pinjaman }}</span>
                                <small class="text-muted d-block">{{ $pinjaman->jumlah_aktif }} aktif</small>
                            </div>
                            <span class="fw-bold" style="color: var(--color-warning);">
                                Rp {{ number_format($pinjaman->sisa_pokok, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">Belum ada pinjaman aktif</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    📈 Tren Penjualan vs Pembelian
                </div>
                <div class="card-body">
                    <canvas id="trendChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    💵 Pendapatan vs Biaya
                </div>
                <div class="card-body">
                    <canvas id="pendapatanBiayaChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Transaction Slide Panel -->
    <div class="slide-panel-overlay" id="quickAddPanelOverlay" onclick="closeSlidePanel('quickAddPanel')"></div>
    <div class="slide-panel" id="quickAddPanel">
        <div class="slide-panel-header">
            <h3 class="slide-panel-title">Tambah Transaksi</h3>
            <button type="button" class="slide-panel-close" onclick="closeSlidePanel('quickAddPanel')" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="slide-panel-body">
            <p class="text-muted mb-4">Pilih jenis transaksi yang ingin ditambahkan:</p>
            
            <div class="d-grid gap-3">
                <a href="{{ route('penjualan.create') }}" class="quick-add-item">
                    <div class="quick-add-icon income">🛒</div>
                    <div class="quick-add-info">
                        <div class="quick-add-title">Penjualan</div>
                        <div class="quick-add-desc">Buat faktur penjualan baru</div>
                    </div>
                    <span class="quick-add-arrow">→</span>
                </a>
                
                <a href="{{ route('pembelian.create') }}" class="quick-add-item">
                    <div class="quick-add-icon expense">🛍️</div>
                    <div class="quick-add-info">
                        <div class="quick-add-title">Pembelian</div>
                        <div class="quick-add-desc">Catat pembelian dari pemasok</div>
                    </div>
                    <span class="quick-add-arrow">→</span>
                </a>
                
                <a href="{{ route('jurnal.create') }}" class="quick-add-item">
                    <div class="quick-add-icon neutral">📋</div>
                    <div class="quick-add-info">
                        <div class="quick-add-title">Jurnal Umum</div>
                        <div class="quick-add-desc">Buat jurnal manual</div>
                    </div>
                    <span class="quick-add-arrow">→</span>
                </a>

                @if(config('app.tipe_usaha') == 'Simpan Pinjam')
                <a href="{{ route('simpanan.create') }}" class="quick-add-item">
                    <div class="quick-add-icon income">💰</div>
                    <div class="quick-add-info">
                        <div class="quick-add-title">Simpanan</div>
                        <div class="quick-add-desc">Transaksi simpanan anggota</div>
                    </div>
                    <span class="quick-add-arrow">→</span>
                </a>
                
                <a href="{{ route('pinjaman.create') }}" class="quick-add-item">
                    <div class="quick-add-icon expense">💳</div>
                    <div class="quick-add-info">
                        <div class="quick-add-title">Pinjaman</div>
                        <div class="quick-add-desc">Pengajuan pinjaman baru</div>
                    </div>
                    <span class="quick-add-arrow">→</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    <style>
        .quick-add-item {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-md);
            background: var(--color-bg);
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        .quick-add-item:hover {
            background: var(--color-border-light);
            transform: translateX(4px);
        }
        .quick-add-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .quick-add-icon.income {
            background: rgba(16, 185, 129, 0.1);
        }
        .quick-add-icon.expense {
            background: rgba(239, 68, 68, 0.1);
        }
        .quick-add-icon.neutral {
            background: rgba(59, 130, 246, 0.1);
        }
        .quick-add-info {
            flex: 1;
        }
        .quick-add-title {
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: 2px;
        }
        .quick-add-desc {
            font-size: var(--font-size-xs);
            color: var(--color-text-muted);
        }
        .quick-add-arrow {
            color: var(--color-text-muted);
            font-size: 18px;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart 1: Penjualan vs Pembelian
        const ctx1 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Penjualan',
                        data: {!! $chartSales !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pembelian',
                        data: {!! $chartPurchases !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Chart 2: Pendapatan vs Biaya
        const ctx2 = document.getElementById('pendapatanBiayaChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: {!! $chartPendapatan !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Biaya',
                        data: {!! $chartBiaya !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
