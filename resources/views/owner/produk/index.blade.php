@extends('layouts.crm')

@section('title', 'Menu Kopi - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Daftar Menu Kopi & Produk</h2>
            <p class="text-muted-coffee">Kelola seluruh produk, kategori menu, harga, dan ketersediaan stok café.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('owner.produk.create') }}" class="btn btn-gold">
                <i class="fa-solid fa-plus me-2"></i> Tambah Menu Baru
            </a>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-mug-hot me-2 text-warning"></i>Inventori Menu Kopi</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="produkTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Satuan</th>
                            <th>Stok</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $p)
                            <tr>
                                <td>
                                    @if($p->gambar)
                                        <img src="{{ asset($p->gambar) }}" alt="{{ $p->nama_produk }}" class="rounded object-fit-cover" style="width: 60px; height: 60px; border: 1.5px solid var(--accent-gold);">
                                    @else
                                        <div class="rounded bg-opacity-15 bg-warning text-warning d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border: 1.5px dashed var(--accent-gold);">
                                            <i class="fa-solid fa-mug-hot fs-4"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold text-light">{{ $p->nama_produk }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-25 text-info border border-info border-opacity-10 px-2.5 py-1.5" style="font-size: 0.8rem;">
                                        {{ $p->kategori }}
                                    </span>
                                </td>
                                <td class="text-warning fw-bold">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                                <td>
                                    @if($p->stok <= 10)
                                        <span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Hampir Habis ({{ $p->stok }})</span>
                                    @else
                                        <span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Tersedia ({{ $p->stok }})</span>
                                    @endif
                                </td>
                                <td class="text-muted-coffee text-truncate" style="max-width: 200px;">{{ $p->deskripsi ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('owner.produk.edit', $p->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-2.5" title="Edit Produk">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('owner.produk.destroy', $p->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" title="Hapus Produk">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
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


