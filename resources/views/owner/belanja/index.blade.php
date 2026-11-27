@extends('layouts.crm')

@section('title', 'Modal Belanja Bahan - CRM Owner')

@section('content')
@php
    $parameterBelanja = ['periode' => $periode['tipe'], 'tanggal' => $periode['acuan']->toDateString()];
@endphp
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Modal Belanja Bahan</h2>
            <p class="text-muted-coffee">Catat belanja bahan baku dan lihat modal per minggu serta per bulan.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('owner.belanja-bahan.create') }}" class="btn btn-gold"><i class="fa-solid fa-plus me-1"></i> Catat Belanja</a>
        </div>
    </div>

    <x-periode-tabs :periode="$periode" rute="owner.belanja-bahan.index" />

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Modal {{ \App\Support\Periode::TIPE[$periode['tipe']] }}</small>
                <h4 class="fw-bold text-warning m-0">Rp {{ number_format($totalPeriode, 0, ',', '.') }}</h4>
                <small class="text-muted-coffee">{{ $periode['label'] }}</small>
            </div></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Jumlah Catatan</small>
                <h4 class="fw-bold text-info m-0">{{ $entri->count() }}</h4>
                <small class="text-muted-coffee">pada periode ini</small>
            </div></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card-coffee h-100"><div class="card-coffee-body text-center py-3">
                <small class="text-muted-coffee text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Modal Minggu Ini</small>
                <h4 class="fw-bold text-light m-0">Rp {{ number_format($rekapMinggu->first()['modal'], 0, ',', '.') }}</h4>
                <small class="text-muted-coffee">{{ $rekapMinggu->first()['label'] }}</small>
            </div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card-coffee h-100">
                <div class="card-coffee-header"><h5 class="m-0 fw-bold"><i class="fa-solid fa-calendar-week me-2 text-warning"></i>Rekap per Minggu (12 minggu)</h5></div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0 align-middle">
                            <thead><tr><th>Minggu</th><th class="text-end">Omset</th><th class="text-end">Modal</th><th class="text-end">Omset − Modal</th></tr></thead>
                            <tbody>
                                @foreach($rekapMinggu as $r)
                                    <tr>
                                        <td style="font-size: 0.85rem;">{{ $r['label'] }}</td>
                                        <td class="text-end text-warning">Rp {{ number_format($r['omset'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($r['modal'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold {{ $r['laba'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($r['laba'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card-coffee h-100">
                <div class="card-coffee-header"><h5 class="m-0 fw-bold"><i class="fa-solid fa-calendar me-2 text-warning"></i>Rekap per Bulan (12 bulan)</h5></div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0 align-middle">
                            <thead><tr><th>Bulan</th><th class="text-end">Omset</th><th class="text-end">Modal</th><th class="text-end">Omset − Modal</th></tr></thead>
                            <tbody>
                                @foreach($rekapBulan as $r)
                                    <tr>
                                        <td>{{ $r['label'] }}</td>
                                        <td class="text-end text-warning">Rp {{ number_format($r['omset'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($r['modal'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold {{ $r['laba'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($r['laba'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header"><h5 class="m-0 fw-bold"><i class="fa-solid fa-basket-shopping me-2 text-warning"></i>Catatan Belanja pada Periode Ini</h5></div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="belanjaTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th class="text-end">Total Harga</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entri as $b)
                            <tr>
                                <td style="white-space: nowrap;">{{ $b->tanggal->format('d/m/Y') }}</td>
                                <td class="fw-bold text-light">{{ $b->nama_bahan }}</td>
                                <td>{{ $b->kategori }}</td>
                                <td>{{ rtrim(rtrim(number_format($b->jumlah, 2, ',', '.'), '0'), ',') }} {{ $b->satuan }}</td>
                                <td class="text-end fw-bold text-warning">Rp {{ number_format($b->harga_total, 0, ',', '.') }}</td>
                                <td>{{ $b->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('owner.belanja-bahan.edit', $b->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-2.5" title="Ubah"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <form action="{{ route('owner.belanja-bahan.destroy', $b->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Hapus catatan belanja ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" title="Hapus"><i class="fa-regular fa-trash-can"></i></button>
                                    </form>
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
