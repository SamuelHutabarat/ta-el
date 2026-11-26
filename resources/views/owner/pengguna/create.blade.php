@extends('layouts.crm')

@title('Tambah Pengguna - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.pengguna.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Pengguna
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Tambah Pengguna Baru</h2>
            <p class="text-muted-coffee">Buat akun login sistem baru untuk Owner, Barista, atau Member Pelanggan.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-user-plus me-2 text-warning"></i>Formulir Data Akun Pengguna</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.pengguna.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label form-label-coffee">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Rian Barista" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label form-label-coffee">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-coffee @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Contoh: rian@kopitkp.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="col-12">
                        <label for="role" class="form-label form-label-coffee">Hak Akses / Role Sistem <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled selected>Pilih Role...</option>
                            <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner (Pemilik Toko)</option>
                            <option value="barista" {{ old('role') === 'barista' ? 'selected' : '' }}>Kasir / Barista</option>
                            <option value="pelanggan" {{ old('role') === 'pelanggan' ? 'selected' : '' }}>Pelanggan (Member CRM)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted-coffee mt-1" style="font-size: 0.8rem;">
                            * Catatan: Jika memilih role Pelanggan, sistem akan otomatis membuat profil member kosong dan loyalitas baru.
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label form-label-coffee">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-coffee @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label form-label-coffee">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-coffee" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <button type="reset" class="btn btn-outline-gold me-2 px-4">Reset</button>
                        <button type="submit" class="btn btn-gold px-4">Simpan Akun</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
