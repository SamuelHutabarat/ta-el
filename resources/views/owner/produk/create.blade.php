@extends('layouts.crm')

@title('Tambah Menu - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.produk.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Inventori
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Tambah Menu Baru</h2>
            <p class="text-muted-coffee">Masukkan informasi produk dan menu baru untuk café Kopi TKP.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-plus me-2 text-warning"></i>Formulir Data Produk</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    <!-- Product Name -->
                    <div class="col-12">
                        <label for="nama_produk" class="form-label form-label-coffee">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Contoh: Kopi Susu TKP Gula Aren" required>
                        @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-6">
                        <label for="kategori" class="form-label form-label-coffee">Kategori Menu <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="" disabled selected>Pilih Kategori...</option>
                            <option value="Kopi" {{ old('kategori') === 'Kopi' ? 'selected' : '' }}>Kopi (Coffee)</option>
                            <option value="Non-Kopi" {{ old('kategori') === 'Non-Kopi' ? 'selected' : '' }}>Non-Kopi (Milk/Tea/Soda)</option>
                            <option value="Makanan" {{ old('kategori') === 'Makanan' ? 'selected' : '' }}>Makanan (Heavy Meal)</option>
                            <option value="Cemilan" {{ old('kategori') === 'Cemilan' ? 'selected' : '' }}>Cemilan / Dessert (Snacks)</option>
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
                            <input type="number" step="0.01" class="form-control form-control-coffee @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}" placeholder="22000" required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="col-12 col-md-6">
                        <label for="stok" class="form-label form-label-coffee">Ketersediaan Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-coffee @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', 50) }}" placeholder="100" required>
                        @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="col-12 col-md-6">
                        <label for="gambar" class="form-label form-label-coffee">Foto Produk (Maks 2MB)</label>
                        <input type="file" class="form-control form-control-coffee @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept="image/*">
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label form-label-coffee">Deskripsi Produk</label>
                        <textarea class="form-control form-control-coffee @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Tulis deskripsi menu kopi secara menarik...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <button type="reset" class="btn btn-outline-gold me-2 px-4">Reset</button>
                        <button type="submit" class="btn btn-gold px-4">Simpan Menu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
