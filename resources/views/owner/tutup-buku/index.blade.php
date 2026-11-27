@extends('layouts.crm')

@section('title', 'Tutup Buku - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Buka / Tutup Buku</h2>
            <p class="text-muted-coffee">Tutup buku menyimpan ringkasan final periode (omset, diskon, modal, dan laba). Setelah ditutup, belanja bahan pada periode tersebut terkunci. Periode yang masih berjalan belum bisa ditutup.</p>
        </div>
    </div>

    <div class="card-coffee mb-4">
        <div class="card-coffee-header"><h5 class="m-0 fw-bold"><i class="fa-solid fa-lock me-2 text-warning"></i>Periode yang Bisa Ditutup</h5></div>
        <div class="card-coffee-body p-0">
            <div class="table-responsive">
                <table class="table table-coffee m-0 align-middle">
                    <thead><tr><th>Jenis</th><th>Periode</th><th>Status</th><th class="text-center" style="width: 180px;">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($kandidat as $k)
                            <tr>
                                <td>{{ \App\Support\Periode::TIPE[$k['tipe']] }}</td>
                                <td>{{ $k['label'] }}</td>
                                <td>
                                    @if($k['tutup'])
                                        <span class="badge bg-success">Sudah ditutup</span>
                                        <small class="text-muted-coffee ms-1">{{ $k['tutup']->ditutup_at?->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="badge bg-secondary">Belum ditutup</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(! $k['tutup'])
                                        <form action="{{ route('owner.tutup-buku.store') }}" method="POST" onsubmit="return confirm('Tutup buku {{ $k['label'] }}? Belanja bahan pada periode ini akan terkunci.')">
                                            @csrf
                                            <input type="hidden" name="tipe" value="{{ $k['tipe'] }}">
                                            <input type="hidden" name="tanggal" value="{{ $k['mulai'] }}">
                                            <button type="submit" class="btn btn-sm btn-gold px-3"><i class="fa-solid fa-book me-1"></i> Tutup Buku</button>
                                        </form>
                                    @else
                                        <span class="text-muted-coffee small">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header"><h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Tutup Buku</h5></div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="tutupBukuTable">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th class="text-end">Omset</th>
                            <th class="text-end">Transaksi</th>
                            <th class="text-end">Modal</th>
                            <th class="text-end">Laba Kotor</th>
                            <th>Ditutup</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $t)
                            <tr>
                                <td>{{ $t->tipe }}</td>
                                <td style="font-size: 0.85rem;">{{ $t->periode_mulai->format('d/m/Y') }} – {{ $t->periode_selesai->format('d/m/Y') }}</td>
                                <td class="text-end text-warning">Rp {{ number_format($t->total_penjualan, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $t->total_transaksi }}</td>
                                <td class="text-end">Rp {{ number_format($t->total_modal, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold {{ $t->laba_kotor >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($t->laba_kotor, 0, ',', '.') }}</td>
                                <td style="font-size: 0.85rem;">
                                    {{ $t->ditutup_at?->format('d/m/Y H:i') }}<br>
                                    <small class="text-muted-coffee">oleh {{ $t->penutup?->name ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($t->dibuka_at)
                                        <span class="badge bg-secondary">Dibuka kembali</span>
                                        <div class="small text-muted-coffee">{{ $t->dibuka_at->format('d/m/Y H:i') }}</div>
                                    @else
                                        <span class="badge bg-success">Tertutup</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @unless($t->dibuka_at)
                                        <form action="{{ route('owner.tutup-buku.buka', $t->id) }}" method="POST" onsubmit="return confirm('Buka kembali buku periode ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-gold px-2"><i class="fa-solid fa-lock-open me-1"></i> Buka Kembali</button>
                                        </form>
                                    @endunless
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
