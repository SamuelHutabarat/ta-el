@extends('layouts.crm')

@section('title', 'Daftar Menu Kopi')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-8">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Menu Kopi & Produk</h2>
            <p class="text-muted-coffee mb-0">Katalog menu untuk kasir. Tambah atau perbarui foto, harga, deskripsi, dan stok menu.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('barista.produk.create') }}" class="btn btn-gold">
                <i class="fa-solid fa-plus me-2"></i> Tambah Menu
            </a>
        </div>
    </div>

    <!-- Category Tabs -->
    @php
        $kategoriList = ['Kopi', 'Non-Kopi', 'Makanan', 'Cemilan'];
    @endphp

    @foreach($kategoriList as $kat)
        @php
            $filteredProduk = $produk->where('kategori', $kat);
        @endphp
        
        <h4 class="fw-bold text-warning border-bottom pb-2 mb-3 mt-4" style="font-family: 'Playfair Display', serif;">
            <i class="fa-solid fa-mug-hot me-2"></i> Kategori: {{ $kat }}
        </h4>

        <div class="row g-4">
            @forelse($filteredProduk as $p)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-coffee h-100">
                        <div class="position-relative">
                            @if($p->gambar)
                                <img src="{{ asset($p->gambar) }}" alt="{{ $p->nama_produk }}" class="card-img-top object-fit-cover" style="height: 180px; border-bottom: 1px solid rgba(200, 150, 62, 0.15);">
                            @else
                                <div class="card-img-top bg-opacity-10 bg-warning text-warning d-flex align-items-center justify-content-center" style="height: 180px; border-bottom: 1px solid rgba(200, 150, 62, 0.15);">
                                    <i class="fa-solid fa-mug-hot fs-1"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 end-0 m-3 badge bg-dark text-warning border border-warning px-2.5 py-1.5 fw-bold" style="font-size: 0.85rem;">
                                Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="card-coffee-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <h5 class="fw-bold text-light mb-1">{{ $p->nama_produk }}</h5>
                                <p class="text-muted-coffee" style="font-size: 0.85rem; line-height: 1.4;">{{ $p->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top" style="border-color: rgba(200, 150, 62, 0.08) !important;">
                                <small class="text-muted-coffee">Ketersediaan Stok:</small>
                                @if($p->stok <= 10)
                                    <span class="badge bg-danger text-light py-1.5 px-2.5 fw-bold">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Terbatas ({{ $p->stok }})
                                    </span>
                                @else
                                    <span class="badge bg-success text-light py-1.5 px-2.5 fw-bold">
                                        <i class="fa-solid fa-circle-check me-1"></i> Tersedia ({{ $p->stok }})
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('barista.produk.edit', $p->id) }}" class="btn btn-sm btn-outline-gold w-100 mt-3">
                                <i class="fa-regular fa-pen-to-square me-1"></i> Edit Menu / Foto
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-coffee text-center py-4">Belum ada produk di kategori ini.</div>
                </div>
            @endforelse
        </div>
    @endforeach
</div>
@endsection
