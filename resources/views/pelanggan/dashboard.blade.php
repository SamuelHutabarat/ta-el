@extends('layouts.crm')

@title('Dashboard Member Kopi TKP')

@section('styles')
<style>
    /* Digital Loyalty Card Styling */
    .loyalty-card {
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        margin-bottom: 2rem;
    }
    
    .loyalty-card-gold {
        background: linear-gradient(135deg, #1E1202 0%, #3B2806 45%, #6A4B0E 100%);
        border: 2px solid var(--accent-gold);
    }
    
    .loyalty-card-silver {
        background: linear-gradient(135deg, #1A1D20 0%, #343A40 50%, #495057 100%);
        border: 2px solid #ADB5BD;
    }
    
    .loyalty-card-bronze {
        background: linear-gradient(135deg, #1C1510 0%, #3E2723 50%, #5D4037 100%);
        border: 2px solid #8D6E63;
    }

    .card-chip {
        width: 50px;
        height: 38px;
        background: linear-gradient(135deg, #FFE082, #FFB300);
        border-radius: 6px;
        position: relative;
        margin-bottom: 1.5rem;
    }

    .card-logo {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-style: italic;
        font-size: 1.6rem;
        color: #fff;
        line-height: 1;
        text-align: right;
    }

    .card-logo span {
        display: block;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-style: normal;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 3px;
    }

    .card-number {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.4rem;
        letter-spacing: 3px;
        font-weight: bold;
        margin-bottom: 1.5rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }

    .card-holder {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.65);
        letter-spacing: 1px;
    }

    .card-holder-name {
        font-weight: 600;
        font-size: 1.15rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card-meta {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .card-tier-label {
        position: absolute;
        bottom: 2rem;
        right: 2rem;
        font-weight: 800;
        font-size: 1.8rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.15;
    }

    /* Glow Poin display */
    .points-glow-box {
        background: radial-gradient(circle, rgba(200, 150, 62, 0.15) 0%, rgba(31, 20, 18, 0) 70%);
        border: 1px solid rgba(200, 150, 62, 0.2);
        border-radius: 16px;
        text-align: center;
        padding: 2rem 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Portal Loyalty Member</h2>
            <p class="text-muted-coffee">Selamat datang kembali, {{ $pelanggan->nama }}! Kumpulkan poin belanja dan klaim kopi gratis pilihanmu.</p>
        </div>
    </div>

    <div class="row">
        <!-- Left Panel: Loyalty Card & Poin -->
        <div class="col-12 col-lg-8">
            <!-- Digital Member Card -->
            <div class="loyalty-card @if($pelanggan->status_member === 'Gold') loyalty-card-gold @elseif($pelanggan->status_member === 'Silver') loyalty-card-silver @else loyalty-card-bronze @endif">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-chip"></div>
                    <div class="card-logo">
                        Kopi TKP
                        <span>Tempat Kamu Pulang</span>
                    </div>
                </div>

                <div class="card-number">{{ substr($pelanggan->kode_pelanggan, 0, 4) }} &nbsp; XXXX &nbsp; XXXX &nbsp; {{ substr($pelanggan->kode_pelanggan, 4) }}</div>

                <div class="row align-items-end">
                    <div class="col-8">
                        <div class="card-holder">Nama Member</div>
                        <div class="card-holder-name">{{ $pelanggan->nama }}</div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-meta">Bergabung Sejak</div>
                        <div class="fw-bold" style="font-size: 0.9rem;">{{ $loyalty->tanggal_bergabung->format('M Y') }}</div>
                    </div>
                </div>

                <div class="card-tier-label">
                    {{ $pelanggan->status_member }} Member
                </div>
            </div>

            <!-- Member stats & progress -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6">
                    <div class="card-coffee h-100">
                        <div class="card-coffee-body text-center py-4">
                            <span class="text-muted-coffee d-block mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Akumulasi Belanja</span>
                            <h3 class="fw-bold text-warning m-0">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h3>
                            <small class="text-muted-coffee mt-2 d-block" style="font-size: 0.8rem;">
                                @if($pelanggan->status_member === 'Bronze')
                                    Belanja Rp {{ number_format(500000 - $totalSpent, 0, ',', '.') }} lagi untuk naik level Silver!
                                @elseif($pelanggan->status_member === 'Silver')
                                    Belanja Rp {{ number_format(1000000 - $totalSpent, 0, ',', '.') }} lagi untuk naik level Gold!
                                @else
                                    <span class="text-success"><i class="fa-solid fa-crown"></i> Anda telah mencapai level member tertinggi!</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="card-coffee h-100">
                        <div class="card-coffee-body text-center py-4">
                            <span class="text-muted-coffee d-block mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Jumlah Kunjungan</span>
                            <h3 class="fw-bold text-info m-0">{{ $totalTransactionCount }} Kali Transaksi</h3>
                            <small class="text-muted-coffee mt-2 d-block" style="font-size: 0.8rem;">Dapatkan diskon otomatis setiap kali pesan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Glow Poin -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card-coffee h-100 points-glow-box d-flex flex-column justify-content-center align-items-center">
                <span class="text-muted-coffee text-uppercase" style="font-size: 0.8rem; letter-spacing: 1.5px;">Saldo Poin Anda</span>
                
                <div class="my-3 d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-coins text-warning fs-1 me-3 animate-bounce"></i>
                    <span class="fw-bold text-warning font-monospace" style="font-size: 4rem; line-height: 1; text-shadow: 0 0 20px rgba(200, 150, 62, 0.4);">
                        {{ $pelanggan->poin }}
                    </span>
                </div>
                
                <span class="text-light fw-bold" style="font-size: 0.95rem;">Tingkat Diskon Anda: {{ $loyalty->diskon_khusus }}% OFF</span>
                <p class="text-muted-coffee mt-2 mb-4" style="font-size: 0.85rem;">Gunakan poin Anda untuk menukarkan kupon diskon makanan dan kopi gratis!</p>
                
                <a href="{{ route('pelanggan.promosi') }}" class="btn btn-gold w-100">
                    <i class="fa-solid fa-gift me-2"></i> Klaim Kupon Penukaran Poin
                </a>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent purchases -->
        <div class="col-12 col-lg-7">
            <div class="card-coffee h-100">
                <div class="card-coffee-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Belanja Terakhir</h5>
                    <a href="{{ route('pelanggan.transaksi.index') }}" class="btn btn-sm btn-outline-gold py-1 px-3">Semua Riwayat</a>
                </div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0">
                            <thead>
                                <tr>
                                    <th>Kode TRX</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Total Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $rt)
                                    <tr>
                                        <td class="fw-bold text-warning">{{ $rt->kode_transaksi }}</td>
                                        <td>{{ $rt->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $rt->metode_pembayaran }}</td>
                                        <td class="fw-bold text-success">Rp {{ number_format($rt->total_akhir, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('pelanggan.transaksi.show', $rt->id) }}" class="btn btn-sm btn-outline-gold px-2 py-0.5" style="font-size: 0.8rem;">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted-coffee py-4">Anda belum melakukan pembelian member. Silakan sebutkan nama member Anda ke barista saat berkunjung!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo recommendation banner -->
        <div class="col-12 col-lg-5">
            <div class="card-coffee h-100">
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-tags me-2 text-warning"></i>Promo Spesial Hari Ini</h5>
                </div>
                <div class="card-coffee-body">
                    <div class="d-flex flex-column gap-3">
                        @forelse($promosi as $p)
                            <div class="p-3 rounded-3 border border-warning border-opacity-15 bg-white bg-opacity-2 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-bold text-light m-0">{{ $p->nama_promo }}</h6>
                                    <span class="badge bg-warning text-dark font-monospace fw-bold" style="font-size: 0.75rem;">
                                        {{ $p->kode_promo }}
                                    </span>
                                </div>
                                <p class="text-muted-coffee mb-2" style="font-size: 0.8rem; line-height: 1.4;">{{ $p->deskripsi ?? 'Syarat & ketentuan berlaku.' }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted-coffee" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i> Berakhir: {{ $p->tanggal_selesai->format('d M Y') }}</small>
                                    @if($p->poin_dibutuhkan > 0)
                                        <small class="text-info fw-bold"><i class="fa-solid fa-coins me-1"></i> {{ $p->poin_dibutuhkan }} Poin</small>
                                    @else
                                        <small class="text-success fw-bold">Diskon Umum</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-coffee text-center py-4">Belum ada promo hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
