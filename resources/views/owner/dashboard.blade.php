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
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-simple me-2 text-warning"></i>Grafik Omset 7 Hari Terakhir</h5>
                </div>
                <div class="card-coffee-body">
                    <div style="height: 320px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Level Donut -->
        <div class="col-12 col-lg-4">
            <div class="card-coffee h-100">
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-pie me-2 text-warning"></i>Distribusi Level Member</h5>
                </div>
                <div class="card-coffee-body d-flex flex-column justify-content-center align-items-center">
                    <div style="height: 240px; width: 100%; max-width: 240px; position: relative;">
                        <canvas id="memberChart"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted-coffee">Fokus Loyalitas: Hubungan Pelanggan (CRM)</small>
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
        // 1. Sales Chart Configuration
        const ctxSales = document.getElementById('salesChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Omset Penjualan (Rupiah)',
                    data: {!! json_encode($chartValues) !!},
                    backgroundColor: 'rgba(200, 150, 62, 0.75)',
                    borderColor: 'rgba(200, 150, 62, 1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(229, 178, 93, 0.95)'
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
                        callbacks: {
                            label: function(context) {
                                let val = context.raw;
                                return 'Omset: Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(200, 150, 62, 0.05)'
                        },
                        ticks: {
                            color: '#A58E8A'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(200, 150, 62, 0.08)'
                        },
                        ticks: {
                            color: '#A58E8A',
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

        // 2. Member Distribution Chart Configuration
        const ctxMember = document.getElementById('memberChart').getContext('2d');
        new Chart(ctxMember, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($levelLabels) !!},
                datasets: [{
                    data: {!! json_encode($levelCounts) !!},
                    backgroundColor: [
                        '#8D6E63', // Bronze
                        '#B0BEC5', // Silver
                        '#C8963E'  // Gold
                    ],
                    borderWidth: 2,
                    borderColor: '#1F1412',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#F4EBE8',
                            font: {
                                family: 'Outfit'
                            },
                            padding: 15
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection
