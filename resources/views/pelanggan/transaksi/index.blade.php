@extends('layouts.crm')

@section('title', 'Riwayat Belanja - Pelanggan')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Riwayat Belanja Anda</h2>
            <p class="text-muted-coffee">Pantau rincian transaksi belanja, potongan harga, dan perolehan poin reward Anda.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Daftar Transaksi Member</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="customerHistoryTable">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal / Waktu</th>
                            <th>Kasir</th>
                            <th>Subtotal</th>
                            <th>Diskon Member/Kupon</th>
                            <th>Total Akhir</th>
                            <th>Poin Diperoleh</th>
                            <th>Pembayaran</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi as $t)
                            <tr>
                                <td class="fw-bold text-warning">{{ $t->kode_transaksi }}</td>
                                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $t->user ? $t->user->name : 'Kasir' }}</td>
                                <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                <td class="text-danger fw-bold">-Rp {{ number_format($t->diskon, 0, ',', '.') }}</td>
                                <td class="text-success fw-bold">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                                <td class="text-info fw-bold">
                                    <i class="fa-solid fa-coins me-1"></i> +{{ $t->poin_dapat }} Poin
                                    @if($t->poin_digunakan > 0)
                                        <div class="text-danger" style="font-size: 0.75rem;">(Redeem {{ $t->poin_digunakan }} Poin)</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $t->metode_pembayaran }}</div>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-10 px-2 py-0.5" style="font-size: 0.75rem;">
                                        {{ $t->status_pembayaran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pelanggan.transaksi.show', $t->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-3">
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


