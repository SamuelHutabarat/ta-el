@extends('layouts.crm')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="container-fluid p-0" style="max-width: 860px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Pengaturan Akun</h2>
            <p class="text-muted-coffee">Perbarui informasi profil pribadi dan kata sandi login Anda.</p>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-coffee alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> Profil berhasil diperbarui.
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-coffee alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> Kata sandi berhasil diperbarui.
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Information Card -->
    <div class="card-coffee mb-4">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-user-pen me-2 text-warning"></i>Informasi Akun</h5>
        </div>
        <div class="card-coffee-body">
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label form-label-coffee">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-coffee @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label form-label-coffee">Alamat Email (opsional)</label>
                        <input type="email" class="form-control form-control-coffee @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="no_telp" class="form-label form-label-coffee">No. Telepon (untuk login member)</label>
                        <input type="text" class="form-control form-control-coffee @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 081234567890">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label form-label-coffee">Peran / Role Pengguna</label>
                        <div>
                            <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-30 px-3 py-2 text-uppercase fw-bold font-monospace" style="font-size: 0.85rem;">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Update Card -->
    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-key me-2 text-warning"></i>Ubah Kata Sandi (Password)</h5>
        </div>
        <div class="card-coffee-body">
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="update_password_current_password" class="form-label form-label-coffee">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control form-control-coffee @error('current_password', 'updatePassword') is-invalid @enderror" id="update_password_current_password" name="current_password" placeholder="••••••••" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="update_password_password" class="form-label form-label-coffee">Kata Sandi Baru</label>
                        <input type="password" class="form-control form-control-coffee @error('password', 'updatePassword') is-invalid @enderror" id="update_password_password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="update_password_password_confirmation" class="form-label form-label-coffee">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control form-control-coffee @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="update_password_password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="fa-solid fa-lock me-1"></i> Perbarui Kata Sandi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
