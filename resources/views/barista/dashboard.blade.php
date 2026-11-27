@extends('layouts.crm')

@section('title', 'Dashboard Barista')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Dashboard Barista & Kasir</h2>
            <p class="text-muted-coffee">Selamat bekerja! Kelola pesanan pelanggan dan catat penjualan hari ini.</p>
        </div>
    </div>

    <!-- Quick POS Actions -->
    <div class="row g-4 mb-4">
        <!-- New POS Checkout -->
        <div class="col-12 col-md-4">
            <div class="card-coffee h-100 border-warning" style="background: linear-gradient(135deg, rgba(200, 150, 62, 0.1) 0%, rgba(31, 20, 18, 0.9) 100%); overflow: hidden;">
                <img src="{{ asset('images/barista_pos_banner.png') }}" class="card-img-top object-fit-cover" style="height: 140px; border-bottom: 1px solid rgba(200, 150, 62, 0.2);" alt="POS Kasir Banner">
                <div class="card-coffee-body d-flex flex-column justify-content-between p-4">
                    <div>
                        <h4 class="fw-bold text-warning mb-2"><i class="fa-solid fa-cash-register me-2"></i> POS Kasir</h4>
                        <p class="text-muted-coffee" style="font-size: 0.9rem;">Buka layar mesin kasir baru untuk melayani order, input member, hitung poin, dan proses diskon.</p>
                    </div>
                    <a href="{{ route('barista.transaksi.create') }}" class="btn btn-gold w-100 mt-3">
                        <i class="fa-solid fa-cart-plus me-1"></i> Buka Kasir Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Today Stats 1 -->
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center p-4">
                    <div class="rounded-3 bg-opacity-10 bg-success text-success p-3" style="width: 56px; height: 56px; min-width: 56px;">
                        <i class="fa-solid fa-money-bill-trend-up fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Omset Anda Hari Ini</span>
                        <h3 class="fw-bold m-0 text-success">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Stats 2 -->
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card-coffee h-100">
                <div class="card-coffee-body d-flex align-items-center p-4">
                    <div class="rounded-3 bg-opacity-10 bg-info text-info p-3" style="width: 56px; height: 56px; min-width: 56px;">
                        <i class="fa-solid fa-receipt fs-3"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted-coffee d-block mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Jumlah Order Hari Ini</span>
                        <h3 class="fw-bold m-0 text-info">{{ $todayTransactions }} Transaksi</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders by this Barista -->
    <div class="card-coffee">
        <div class="card-coffee-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Transaksi yang Anda Proses Baru-baru Ini</h5>
            <a href="{{ route('barista.transaksi.index') }}" class="btn btn-sm btn-outline-gold py-1 px-3">Lihat Riwayat</a>
        </div>
        <div class="card-coffee-body p-0">
            <div class="table-responsive">
                <table class="table table-coffee m-0">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal / Waktu</th>
                            <th>Pelanggan</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Total Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $rt)
                            <tr>
                                <td class="fw-bold text-warning">{{ $rt->kode_transaksi }}</td>
                                <td>{{ $rt->created_at->format('d/m/Y H:i') }} ({{ $rt->created_at->diffForHumans() }})</td>
                                <td>{{ $rt->pelanggan ? $rt->pelanggan->nama : 'Guest Walk-in' }}</td>
                                <td>Rp {{ number_format($rt->total_harga, 0, ',', '.') }}</td>
                                <td class="text-danger fw-bold">-Rp {{ number_format($rt->diskon, 0, ',', '.') }}</td>
                                <td class="text-success fw-bold">Rp {{ number_format($rt->total_akhir, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('barista.transaksi.show', $rt->id) }}" class="btn btn-sm btn-outline-gold py-1 px-3">
                                        <i class="fa-solid fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted-coffee py-4">Belum ada transaksi yang Anda proses hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
