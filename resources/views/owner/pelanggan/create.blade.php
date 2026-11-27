@extends('layouts.crm')

@section('title', 'Daftarkan Member - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.pelanggan.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Member
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Daftarkan Member Baru</h2>
            <p class="text-muted-coffee">Cukup isi nama dan nomor telepon. Nomor telepon juga dipakai pelanggan untuk login.</p>
        </div>
    </div>

    <div class="card-coffee mb-4">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-address-card me-2 text-warning"></i>Formulir Data Member</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.pelanggan.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label for="nama" class="form-label form-label-coffee">Nama Lengkap Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Budi Sudrajat" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="no_telp" class="form-label form-label-coffee">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" placeholder="Contoh: 081234567890" required>
                        @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tanggal_lahir" class="form-label form-label-coffee">Tanggal Lahir</label>
                        <input type="date" class="form-control form-control-coffee @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text text-muted-coffee">Dipakai CRM untuk mengingat hari ulang tahun member setia.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="status_member" class="form-label form-label-coffee">Level Member Awal <span class="text-danger">*</span></label>
                        <select id="status_member" name="status_member" class="form-select form-control-coffee @error('status_member') is-invalid @enderror" required>
                            <option value="Bronze" {{ old('status_member') === 'Bronze' ? 'selected' : '' }}>Bronze (Diskon 0%)</option>
                            <option value="Silver" {{ old('status_member') === 'Silver' ? 'selected' : '' }}>Silver (Diskon 5%)</option>
                            <option value="Gold" {{ old('status_member') === 'Gold' ? 'selected' : '' }}>Gold (Diskon 10%)</option>
                        </select>
                        @error('status_member')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="poin" class="form-label form-label-coffee">Poin Reward Awal <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control form-control-coffee @error('poin') is-invalid @enderror" id="poin" name="poin" value="{{ old('poin', 0) }}" placeholder="0" required>
                        @error('poin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="text-warning border-bottom pb-2 mb-3" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-key me-2"></i> Akun Login Member (Opsional)
                </h5>
                <p class="text-muted-coffee" style="font-size: 0.85rem;">Isi kata sandi jika member ingin login sendiri ke portal dengan nomor telepon di atas untuk melihat poin dan kartunya.</p>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label form-label-coffee">Kata Sandi</label>
                        <input type="password" class="form-control form-control-coffee @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label form-label-coffee">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control form-control-coffee" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi">
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('owner.pelanggan.index') }}" class="btn btn-outline-gold me-2 px-4">Batal</a>
                        <button type="submit" class="btn btn-gold px-4">Daftarkan Member</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
