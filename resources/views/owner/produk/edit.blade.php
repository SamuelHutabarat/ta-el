@extends('layouts.crm')

@title('Edit Menu - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.produk.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Inventori
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Edit Menu Kopi</h2>
            <p class="text-muted-coffee">Perbarui informasi produk dan stok menu café Kopi TKP.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-regular fa-pen-to-square me-2 text-warning"></i>Formulir Data Produk</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <!-- Product Name -->
                    <div class="col-12">
                        <label for="nama_produk" class="form-label form-label-coffee">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                        @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <label for="kategori" class="form-label form-label-coffee">Kategori Menu <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="Kopi" {{ old('kategori', $produk->kategori) === 'Kopi' ? 'selected' : '' }}>Kopi (Coffee)</option>
                            <option value="Non-Kopi" {{ old('kategori', $produk->kategori) === 'Non-Kopi' ? 'selected' : '' }}>Non-Kopi (Milk/Tea/Soda)</option>
                            <option value="Makanan" {{ old('kategori', $produk->kategori) === 'Makanan' ? 'selected' : '' }}>Makanan (Heavy Meal)</option>
                            <option value="Cemilan" {{ old('kategori', $produk->kategori) === 'Cemilan' ? 'selected' : '' }}>Cemilan / Dessert (Snacks)</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="col-12 col-md-6">
                        <label for="harga" class="form-label form-label-coffee">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-warning text-warning fw-bold">Rp</span>
                            <input type="number" step="0.01" class="form-control form-control-coffee @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga', $produk->harga) }}" required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="col-12 col-md-6">
                        <label for="stok" class="form-label form-label-coffee">Ketersediaan Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-coffee @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $produk->stok) }}" required>
                        @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="col-12 col-md-6">
                        <label for="gambar" class="form-label form-label-coffee">Ganti Foto Produk (Maks 2MB)</label>
                        <input type="file" class="form-control form-control-coffee @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    @if($produk->gambar)
                        <div class="col-12">
                            <label class="form-label form-label-coffee d-block">Foto Saat Ini</label>
                            <img src="{{ asset($produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="rounded border border-warning" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label form-label-coffee">Deskripsi Produk</label>
                        <textarea class="form-control form-control-coffee @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('owner.produk.index') }}" class="btn btn-outline-gold me-2 px-4">Batal</a>
                        <button type="submit" class="btn btn-gold px-4">Perbarui Menu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
