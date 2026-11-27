@extends('layouts.crm')

@section('title', 'Voucher & Promosi Member')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Voucher & Penukaran Poin Reward</h2>
            <p class="text-muted-coffee">Gunakan poin reward belanja Anda untuk mengklaim voucher potongan atau kopi gratis.</p>
        </div>
    </div>

    <!-- Points Summary Alert -->
    <div class="card-coffee border-info mb-4" style="background: radial-gradient(circle at top right, rgba(200, 150, 62, 0.08) 0%, rgba(31, 20, 18, 0.9) 100%);">
        <div class="card-coffee-body d-flex flex-wrap justify-content-between align-items-center p-4">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="rounded-circle bg-dark text-warning p-3 d-flex align-items-center justify-content-center border border-warning" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-coins fs-2"></i>
                </div>
                <div class="ms-3">
                    <h5 class="fw-bold text-light m-0">Poin Reward Anda</h5>
                    <p class="text-muted-coffee m-0" style="font-size: 0.85rem;">Tingkatkan terus belanjamu untuk mengumpulkan lebih banyak poin.</p>
                </div>
            </div>
            <div>
                <span class="fw-bold text-warning font-monospace fs-1">{{ $pelanggan->poin }}</span>
                <span class="text-muted-coffee fw-bold ms-1" style="font-size: 1.1rem;">Poin</span>
            </div>
        </div>
    </div>

    <!-- Promos List -->
    <div class="row g-4">
        @forelse($promosi as $p)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-coffee h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header / discount size badge -->
                        <div class="p-3 bg-dark border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                            @if($p->diskon_persen > 0)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                    Diskon {{ $p->diskon_persen }}%
                                </span>
                            @elseif($p->diskon_nominal > 0)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                    Potongan Rp {{ number_format($p->diskon_nominal, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                    Special Reward
                                </span>
                            @endif
                            
                            <span class="badge bg-secondary bg-opacity-25 text-light px-2.5 py-1.5 font-monospace" style="font-size: 0.8rem;">
                                {{ $p->kode_promo }}
                            </span>
                        </div>
                        
                        <div class="p-3">
                            <h5 class="fw-bold text-light mb-2">{{ $p->nama_promo }}</h5>
                            <p class="text-muted-coffee" style="font-size: 0.85rem; line-height: 1.45;">{{ $p->deskripsi ?? 'Syarat & ketentuan berlaku.' }}</p>
                            
                            <div class="border-top border-secondary border-opacity-10 pt-2 mt-3" style="font-size: 0.8rem;">
                                <div class="d-flex justify-content-between text-muted-coffee mb-1">
                                    <span>Minimal Belanja:</span>
                                    <span class="text-light fw-bold">Rp {{ number_format($p->minimal_transaksi, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted-coffee">
                                    <span>Berlaku Hingga:</span>
                                    <span class="text-light fw-bold">{{ $p->tanggal_selesai->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-dark border-top border-secondary border-opacity-10">
                        @if($p->poin_dibutuhkan > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted-coffee" style="font-size: 0.85rem;">Biaya Poin:</span>
                                <span class="text-info fw-bold"><i class="fa-solid fa-coins me-1"></i> {{ $p->poin_dibutuhkan }} Poin</span>
                            </div>
                            
                            @if($pelanggan->poin >= $p->poin_dibutuhkan)
                                <form action="{{ route('pelanggan.promosi.claim') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menukarkan {{ $p->poin_dibutuhkan }} poin untuk voucher ini?')">
                                    @csrf
                                    <input type="hidden" name="promosi_id" value="{{ $p->id }}">
                                    <button type="submit" class="btn btn-gold w-100 py-2 btn-sm fw-bold">
                                        <i class="fa-solid fa-gift me-1"></i> Tukar Poin Sekarang
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 py-2 btn-sm fw-bold" disabled>
                                    <i class="fa-solid fa-ban me-1"></i> Poin Tidak Cukup
                                </button>
                            @endif
                        @else
                            <div class="text-center py-1">
                                <span class="text-success fw-bold" style="font-size: 0.85rem;"><i class="fa-solid fa-circle-check me-1"></i> Otomatis Tersedia di Kasir</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-coffee text-center py-5">
                    <i class="fa-solid fa-tags fs-1 d-block text-warning mb-2 animate-pulse"></i> Belum ada program promosi aktif saat ini.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
