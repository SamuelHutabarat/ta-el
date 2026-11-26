@extends('layouts.crm')

@title('Dashboard Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Ringkasan CRM & Analytics</h2>
            <p class="text-muted-coffee">Selamat datang kembali, Owner! Pantau performa bisnis dan loyalitas pelanggan di bawah ini.</p>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center">
                    <div class="rounded-3 bg-opacity-10 bg-warning text-warning p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-rupiah-sign fs-3"></i>
                    </div>
                    <div class="ms-3 overflow-hidden">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Total Omset</span>
                        <h4 class="fw-bold m-0 text-truncate text-warning">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center">
                    <div class="rounded-3 bg-opacity-10 bg-info text-info p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-cart-shopping fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Total Transaksi</span>
                        <h4 class="fw-bold m-0 text-info">{{ number_format($totalOrders, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center">
                    <div class="rounded-3 bg-opacity-10 bg-success text-success p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-users fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Member Terdaftar</span>
                        <h4 class="fw-bold m-0 text-success">{{ number_format($totalMembers, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Promos Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center">
                    <div class="rounded-3 bg-opacity-10 bg-danger text-danger p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-bullhorn fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Promo Aktif</span>
                        <h4 class="fw-bold m-0 text-danger">{{ $activePromos }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-12 col-lg-8">
            <div class="card-coffee h-100">
                <div class="card-coffee-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-simple me-2 text-warning"></i>Grafik Omset 7 Hari Terakhir</h5>
                    <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-30 px-2.5 py-1 font-monospace" style="font-size: 0.8rem;">
                        <i class="fa-regular fa-calendar me-1"></i> {{ $chartPeriod ?? '7 Hari Terakhir' }}
                    </span>
                </div>
                <div class="card-coffee-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Level Donut -->
        <div class="col-12 col-lg-4">
            <div class="card-coffee h-100">
                <div class="card-coffee-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-pie me-2 text-warning"></i>Distribusi Level Member</h5>
                    <span class="badge bg-secondary bg-opacity-25 text-light px-2 py-1 font-monospace" style="font-size: 0.75rem;">
                        {{ $totalMembers }} Member
                    </span>
                </div>
                <div class="card-coffee-body d-flex flex-column justify-content-center align-items-center">
                    <div style="height: 240px; width: 100%; max-width: 250px; position: relative;">
                        <canvas id="memberChart"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted-coffee">Proporsi Pelanggan CRM (Bronze, Silver, Gold)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-12 col-xl-7">
            <div class="card-coffee h-100">
                <div class="card-coffee-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Transaksi Terakhir</h5>
                    <a href="{{ route('owner.transaksi.index') }}" class="btn btn-sm btn-outline-gold py-1 px-3">Lihat Semua</a>
                </div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0">
                            <thead>
                                <tr>
                                    <th>Kode TRX</th>
                                    <th>Pelanggan</th>
                                    <th>Total Bayar</th>
                                    <th>Metode</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $rt)
                                    <tr>
                                        <td>
                                            <a href="{{ route('owner.transaksi.show', $rt->id) }}" class="text-warning text-decoration-none fw-bold">
                                                {{ $rt->kode_transaksi }}
                                            </a>
                                        </td>
                                        <td>{{ $rt->pelanggan ? $rt->pelanggan->nama : 'Guest Walk-in' }}</td>
                                        <td>Rp {{ number_format($rt->total_akhir, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-opacity-25 {{ $rt->metode_pembayaran === 'QRIS' ? 'bg-info text-info' : 'bg-success text-success' }} px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $rt->metode_pembayaran }}
                                            </span>
                                        </td>
                                        <td>{{ $rt->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted-coffee py-4">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="col-12 col-xl-5">
            <div class="card-coffee h-100">
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-trophy me-2 text-warning"></i>Menu Terlaris (Favorit)</h5>
                </div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th class="text-end">Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSellers as $key => $bs)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="fw-bold">{{ $bs->nama_produk }}</td>
                                        <td class="text-end text-warning fw-bold">{{ $bs->total_qty }} cup/porsi</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted-coffee py-4">Belum ada penjualan menu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js gagal dimuat.');
            return;
        }

        // 1. Sales Chart Configuration
        const salesCanvas = document.getElementById('salesChart');
        if (salesCanvas) {
            const ctxSales = salesCanvas.getContext('2d');
            
            // Elegant vertical gold-to-coffee gradient
            const salesGradient = ctxSales.createLinearGradient(0, 0, 0, 300);
            salesGradient.addColorStop(0, 'rgba(212, 160, 71, 0.92)');
            salesGradient.addColorStop(1, 'rgba(86, 55, 50, 0.4)');

            new Chart(ctxSales, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Omset Penjualan (Rp)',
                        data: {!! json_encode($chartValues) !!},
                        backgroundColor: salesGradient,
                        borderColor: '#EAB965',
                        borderWidth: 1.5,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(234, 185, 101, 1)',
                        hoverBorderColor: '#FFFFFF',
                        barPercentage: 0.65,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(34, 22, 20, 0.95)',
                            titleColor: '#EAB965',
                            bodyColor: '#FAF5F2',
                            borderColor: 'rgba(212, 160, 71, 0.4)',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let val = context.raw || 0;
                                    return ' Omset: Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(212, 160, 71, 0.08)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#C7B3AE',
                                font: {
                                    family: "'Outfit', sans-serif",
                                    weight: 500
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(212, 160, 71, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#C7B3AE',
                                font: {
                                    family: "'Outfit', sans-serif"
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000) + 'jt';
                                    } else if (value >= 1000) {
                                        return (value / 1000) + 'k';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Member Distribution Chart Configuration
        const memberCanvas = document.getElementById('memberChart');
        if (memberCanvas) {
            const ctxMember = memberCanvas.getContext('2d');
            const memberCounts = {!! json_encode($levelCounts) !!};
            const totalMembers = memberCounts.reduce((a, b) => a + b, 0);

            new Chart(ctxMember, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($levelLabels) !!},
                    datasets: [{
                        data: memberCounts,
                        backgroundColor: [
                            '#B87333', // Bronze
                            '#B0BEC5', // Silver
                            '#D4A047'  // Gold
                        ],
                        borderWidth: 2,
                        borderColor: '#2D1E1B',
                        hoverBorderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#FAF5F2',
                                font: {
                                    family: "'Outfit', sans-serif",
                                    size: 12
                                },
                                padding: 14,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(34, 22, 20, 0.95)',
                            titleColor: '#EAB965',
                            bodyColor: '#FAF5F2',
                            borderColor: 'rgba(212, 160, 71, 0.4)',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    let val = context.raw || 0;
                                    let pct = totalMembers > 0 ? Math.round((val / totalMembers) * 100) : 0;
                                    return ' ' + context.label + ': ' + val + ' Member (' + pct + '%)';
                                }
                            }
                        }
                    },
                    cutout: '62%'
                }
            });
        }
    });
</script>
@endsection
