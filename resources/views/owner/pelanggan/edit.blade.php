@extends('layouts.crm')

@title('Edit Member - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.pelanggan.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Member
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Edit Profil Member</h2>
            <p class="text-muted-coffee">Perbarui informasi member pelanggan Kopi TKP, kode member: <strong>{{ $pelanggan->kode_pelanggan }}</strong>.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-regular fa-pen-to-square me-2 text-warning"></i>Formulir Data Profil Member</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.pelanggan.update', $pelanggan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <!-- Member Code (ReadOnly) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-coffee">Kode Member</label>
                        <input type="text" class="form-control form-control-coffee" value="{{ $pelanggan->kode_pelanggan }}" disabled>
                    </div>

                    <!-- Name -->
                    <div class="col-12 col-md-6">
                        <label for="nama" class="form-label form-label-coffee">Nama Lengkap Member <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $pelanggan->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-12 col-md-6">
                        <label for="no_telp" class="form-label form-label-coffee">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control form-control-coffee @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp', $pelanggan->no_telp) }}">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Member Status Tier -->
                    <div class="col-12 col-md-6">
                        <label for="status_member" class="form-label form-label-coffee">Level Member <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('status_member') is-invalid @enderror" id="status_member" name="status_member" required>
                            <option value="Bronze" {{ old('status_member', $pelanggan->status_member) === 'Bronze' ? 'selected' : '' }}>Bronze (Diskon 0%)</option>
                            <option value="Silver" {{ old('status_member', $pelanggan->status_member) === 'Silver' ? 'selected' : '' }}>Silver (Diskon 5%)</option>
                            <option value="Gold" {{ old('status_member', $pelanggan->status_member) === 'Gold' ? 'selected' : '' }}>Gold (Diskon 10%)</option>
                        </select>
                        @error('status_member')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Poin Reward -->
                    <div class="col-12 col-md-6">
                        <label for="poin" class="form-label form-label-coffee">Saldo Poin Reward <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-warning text-info fw-bold"><i class="fa-solid fa-coins"></i></span>
                            <input type="number" class="form-control form-control-coffee @error('poin') is-invalid @enderror" id="poin" name="poin" value="{{ old('poin', $pelanggan->poin) }}" required>
                            @error('poin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Linked User Account (ReadOnly indication) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-coffee">Akun Login Web</label>
                        <input type="text" class="form-control form-control-coffee" value="{{ $pelanggan->user ? $pelanggan->user->email : 'Tidak memiliki akses online' }}" disabled>
                    </div>

                    <!-- Address -->
                    <div class="col-12">
                        <label for="alamat" class="form-label form-label-coffee">Alamat Rumah</label>
                        <textarea class="form-control form-control-coffee @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('owner.pelanggan.index') }}" class="btn btn-outline-gold me-2 px-4">Batal</a>
                        <button type="submit" class="btn btn-gold px-4">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
