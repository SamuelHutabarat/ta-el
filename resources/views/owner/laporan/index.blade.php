@extends('layouts.crm')

@section('title', 'Laporan Penjualan - CRM Owner')

@section('content')
@php
    $parameterLaporan = ['periode' => $periode['tipe'], 'tanggal' => $periode['acuan']->toDateString()];
@endphp
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-lg-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Laporan Penjualan</h2>
            <p class="text-muted-coffee">Omset, diskon, dan modal belanja bahan per hari, minggu, atau bulan.</p>
        </div>
        <div class="col-12 col-lg-6 text-lg-end mt-3 mt-lg-0 no-print">
            <a href="{{ route('owner.laporan.excel', $parameterLaporan) }}" class="btn btn-outline-gold me-2">
                <i class="fa-solid fa-file-csv me-1"></i> Export Excel (CSV)
            </a>
            <a href="{{ route('owner.laporan.pdf', $parameterLaporan) }}" target="_blank" class="btn btn-gold">
                <i class="fa-solid fa-print me-1"></i> Cetak / PDF
            </a>
        </div>
    </div>

    <x-periode-tabs :periode="$periode" rute="owner.laporan.index" />

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Omset</small>
                <h4 class="fw-bold text-warning m-0">Rp {{ number_format($ringkasan['omset'], 0, ',', '.') }}</h4>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Transaksi</small>
                <h4 class="fw-bold text-info m-0">{{ number_format($ringkasan['transaksi'], 0, ',', '.') }}</h4>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Diskon Diberikan</small>
                <h4 class="fw-bold text-danger m-0">Rp {{ number_format($ringkasan['diskon'], 0, ',', '.') }}</h4>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Modal Belanja Bahan</small>
                <h4 class="fw-bold text-light m-0">Rp {{ number_format($ringkasan['modal'], 0, ',', '.') }}</h4>
            </div></div>
        </div>
        <div class="col-6 col-lg">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Omset − Modal</small>
                <h4 class="fw-bold m-0 {{ $ringkasan['laba'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($ringkasan['laba'], 0, ',', '.') }}</h4>
            </div></div>
        </div>
    </div>

    @if($rincian->isNotEmpty())
        <div class="card-coffee mb-4">
            <div class="card-coffee-header">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-table-list me-2 text-warning"></i>Rincian {{ $periode['tipe'] === 'mingguan' ? 'per Hari' : 'per Minggu' }}</h5>
            </div>
            <div class="card-coffee-body">
                <div class="table-responsive">
                    <table class="table table-coffee table-striped align-middle m-0">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th class="text-end">Transaksi</th>
                                <th class="text-end">Omset</th>
                                <th class="text-end">Modal Belanja</th>
                                <th class="text-end">Omset − Modal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rincian as $baris)
                                <tr>
                                    <td class="fw-bold text-light">{{ $baris['label'] }}</td>
                                    <td class="text-end">{{ $baris['transaksi'] }}</td>
                                    <td class="text-end text-warning">Rp {{ number_format($baris['omset'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($baris['modal'], 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold {{ $baris['laba'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($baris['laba'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-receipt me-2 text-warning"></i>Daftar Transaksi ({{ $transaksi->count() }})</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="laporanTable">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal / Waktu</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Sumber</th>
                            <th>Total Harga</th>
                            <th>Diskon</th>
                            <th>Total Akhir</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi as $t)
                            <tr>
                                <td class="fw-bold text-warning">{{ $t->kode_transaksi }}</td>
                                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $t->pelanggan ? $t->pelanggan->nama : 'Guest (Walk-in)' }}</td>
                                <td>{{ $t->user ? $t->user->name : 'System' }}</td>
                                <td>
                                    @if($t->sumber_transaksi === 'Online')
                                        <span class="badge bg-info text-dark">Online</span>
                                    @else
                                        <span class="badge bg-secondary">Datang Langsung</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                <td class="text-danger">-Rp {{ number_format($t->diskon, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($t->total_akhir, 0, ',', '.') }}</td>
                                <td>{{ $t->metode_pembayaran }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
