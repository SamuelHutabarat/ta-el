@extends('layouts.crm')

@section('title', 'Riwayat Transaksi - Barista')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Riwayat Penjualan Anda</h2>
            <p class="text-muted-coffee">Daftar lengkap transaksi penjualan yang Anda proses di mesin kasir.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Kasir</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="historyTable">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal / Waktu</th>
                            <th>Nama Member</th>
                            <th>Subtotal</th>
                            <th>Potongan Diskon</th>
                            <th>Total Akhir</th>
                            <th>Pembayaran</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi as $t)
                            <tr>
                                <td class="fw-bold text-warning">{{ $t->kode_transaksi }}</td>
                                <td>{{ $t->created_at->format('d/m/Y H:i') }} ({{ $t->created_at->diffForHumans() }})</td>
                                <td>
                                    @if($t->pelanggan)
                                        <div class="fw-bold text-light">{{ $t->pelanggan->nama }}</div>
                                        <small class="text-warning" style="font-size: 0.8rem;">
                                            <i class="fa-solid fa-id-card"></i> {{ $t->pelanggan->kode_pelanggan }}
                                        </small>
                                    @else
                                        <span class="text-muted">Guest Walk-in</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                <td class="text-danger fw-bold">-Rp {{ number_format($t->diskon, 0, ',', '.') }}</td>
                                <td class="text-success fw-bold">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                                <td>
                                    <div>{{ $t->metode_pembayaran }}</div>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-10 px-2 py-0.5" style="font-size: 0.75rem;">
                                        {{ $t->status_pembayaran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('barista.transaksi.show', $t->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-3">
                                        <i class="fa-solid fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


