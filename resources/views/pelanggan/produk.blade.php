@extends('layouts.crm')

@section('title', 'Menu Kopi TKP')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12 text-center text-md-start">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Menu Kopi &amp; Produk Pilihan</h2>
            <p class="text-muted-coffee">Klik foto menu untuk melihat deskripsi, harga, dan ketersediaannya.</p>
        </div>
    </div>

    @php
        $kategoriList = ['Kopi', 'Non-Kopi', 'Makanan', 'Cemilan'];
    @endphp

    @foreach($kategoriList as $kat)
        @php
            $filteredProduk = $produk->where('kategori', $kat);
        @endphp

        <h4 class="fw-bold text-warning border-bottom pb-2 mb-3 mt-4" style="font-family: 'Playfair Display', serif;">
            <i class="fa-solid fa-mug-saucer me-2"></i> {{ $kat }}
        </h4>

        <div class="row g-4 mb-4">
            @forelse($filteredProduk as $p)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-coffee h-100">
                        <div class="position-relative">
                            <button type="button" class="btn p-0 border-0 w-100 d-block btn-menu-detail"
                                    data-bs-toggle="modal" data-bs-target="#menuDetailModal"
                                    data-nama="{{ $p->nama_produk }}"
                                    data-kategori="{{ $p->kategori }}"
                                    data-harga="Rp {{ number_format($p->harga, 0, ',', '.') }}"
                                    data-deskripsi="{{ $p->deskripsi ?? 'Nikmati kesegaran racikan menu kopi signature spesial dari tim kami.' }}"
                                    data-stok="{{ $p->stok > 0 ? 'Tersedia' : 'Habis' }}"
                                    data-stok-ok="{{ $p->stok > 0 ? '1' : '0' }}"
                                    data-gambar="{{ $p->gambar ? asset($p->gambar) : '' }}"
                                    aria-label="Lihat detail {{ $p->nama_produk }}">
                                @if($p->gambar)
                                    <img src="{{ asset($p->gambar) }}" alt="{{ $p->nama_produk }}" class="card-img-top object-fit-cover" style="height: 180px; border-bottom: 1px solid rgba(200, 150, 62, 0.15);">
                                @else
                                    <div class="card-img-top bg-opacity-10 bg-warning text-warning d-flex align-items-center justify-content-center" style="height: 180px; border-bottom: 1px solid rgba(200, 150, 62, 0.15);">
                                        <i class="fa-solid fa-mug-hot fs-1"></i>
                                    </div>
                                @endif
                            </button>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-dark text-warning border border-warning px-2.5 py-1.5 fw-bold font-monospace" style="font-size: 0.85rem; pointer-events: none;">
                                Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="card-coffee-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <h5 class="fw-bold text-light mb-1">{{ $p->nama_produk }}</h5>
                                <p class="text-muted-coffee" style="font-size: 0.85rem; line-height: 1.4;">{{ $p->deskripsi ?? 'Nikmati kesegaran racikan menu kopi signature spesial dari tim kami.' }}</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top" style="border-color: rgba(200, 150, 62, 0.08) !important;">
                                <small class="text-muted-coffee">Status Menu:</small>
                                @if($p->stok <= 0)
                                    <span class="badge bg-danger text-light py-1.5 px-2.5 fw-bold">Habis</span>
                                @else
                                    <span class="badge bg-success text-light py-1.5 px-2.5 fw-bold">Tersedia</span>
                                @endif
                            </div>
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

<!-- Detail menu -->
<div class="modal fade" id="menuDetailModal" tabindex="-1" aria-labelledby="menuDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-coffee">
            <div class="modal-header card-coffee-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning" id="menuDetailLabel">Detail Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <img id="detailGambar" src="" alt="" class="w-100 rounded-3 object-fit-cover mb-3 d-none" style="max-height: 280px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h4 class="fw-bold text-light mb-0" id="detailNama"></h4>
                        <small class="text-muted-coffee" id="detailKategori"></small>
                    </div>
                    <span class="badge bg-dark text-warning border border-warning px-2.5 py-1.5 fw-bold font-monospace" id="detailHarga"></span>
                </div>
                <p class="text-light" style="font-size: 0.95rem; line-height: 1.5;" id="detailDeskripsi"></p>
                <div class="d-flex justify-content-between align-items-center border-top pt-2" style="border-color: rgba(200, 150, 62, 0.15) !important;">
                    <small class="text-muted-coffee">Status:</small>
                    <span class="badge fw-bold" id="detailStatus"></span>
                </div>
                <p class="text-muted-coffee mt-3 mb-0" style="font-size: 0.8rem;">
                    Untuk memesan, tunjukkan menu ini ke kasir saat datang langsung, atau pesan lewat kanal online kami.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('menuDetailModal');
        if (!modal) { return; }
        modal.addEventListener('show.bs.modal', function (event) {
            var tombol = event.relatedTarget;
            var gambar = document.getElementById('detailGambar');
            document.getElementById('detailNama').textContent = tombol.getAttribute('data-nama');
            document.getElementById('detailKategori').textContent = tombol.getAttribute('data-kategori');
            document.getElementById('detailHarga').textContent = tombol.getAttribute('data-harga');
            document.getElementById('detailDeskripsi').textContent = tombol.getAttribute('data-deskripsi');

            var status = document.getElementById('detailStatus');
            var tersedia = tombol.getAttribute('data-stok-ok') === '1';
            status.textContent = tersedia ? 'Tersedia' : 'Habis';
            status.className = 'badge fw-bold ' + (tersedia ? 'bg-success' : 'bg-danger');

            if (tombol.getAttribute('data-gambar')) {
                gambar.src = tombol.getAttribute('data-gambar');
                gambar.alt = tombol.getAttribute('data-nama');
                gambar.classList.remove('d-none');
            } else {
                gambar.classList.add('d-none');
            }
        });
    });
</script>
@endsection
