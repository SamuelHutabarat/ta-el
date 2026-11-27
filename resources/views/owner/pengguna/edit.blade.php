@extends('layouts.crm')

@section('title', 'Edit Pengguna - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.pengguna.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Pengguna
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Edit Akun Pengguna</h2>
            <p class="text-muted-coffee">Perbarui informasi akun, email, role, atau ganti password login.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-regular fa-pen-to-square me-2 text-warning"></i>Formulir Data Akun Pengguna</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.pengguna.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label form-label-coffee">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label form-label-coffee">Alamat Email (wajib untuk Owner &amp; Kasir)</label>
                        <input type="email" class="form-control form-control-coffee @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- No. Telepon -->
                    <div class="col-12 col-md-6">
                        <label for="no_telp" class="form-label form-label-coffee">No. Telepon (wajib untuk Pelanggan)</label>
                        <input type="text" class="form-control form-control-coffee @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 081234567890">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="col-12">
                        <label for="role" class="form-label form-label-coffee">Hak Akses / Role Sistem <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('role') is-invalid @enderror" id="role" name="role" required {{ Auth::id() === $user->id ? 'disabled' : '' }}>
                            <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Owner (Pemilik Toko)</option>
                            <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                            <option value="pelanggan" {{ old('role', $user->role) === 'pelanggan' ? 'selected' : '' }}>Pelanggan (Member CRM)</option>
                        </select>
                        @if(Auth::id() === $user->id)
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="form-text text-danger mt-1" style="font-size: 0.8rem;">
                                * Anda tidak dapat mengubah role Anda sendiri untuk mencegah kehilangan hak akses Owner.
                            </div>
                        @endif
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div class="col-12 col-md-6 mt-4">
                        <label for="password" class="form-label form-label-coffee">Kata Sandi Baru (Password)</label>
                        <input type="password" class="form-control form-control-coffee @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kosongkan jika tidak ingin diganti">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="col-12 col-md-6 mt-4">
                        <label for="password_confirmation" class="form-label form-label-coffee">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control form-control-coffee" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('owner.pengguna.index') }}" class="btn btn-outline-gold me-2 px-4">Batal</a>
                        <button type="submit" class="btn btn-gold px-4">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
