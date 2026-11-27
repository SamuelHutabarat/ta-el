@extends('layouts.crm')

@section('title', 'Edit Member - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.pelanggan.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Member
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Edit Data Member</h2>
            <p class="text-muted-coffee">Kode member <span class="text-warning fw-bold">{{ $pelanggan->kode_pelanggan }}</span> · Bergabung {{ $pelanggan->tanggal_bergabung?->locale('id')->isoFormat('D MMMM Y') ?? '-' }}</p>
        </div>
    </div>

    <div class="card-coffee mb-4">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-user-pen me-2 text-warning"></i>Formulir Data Member</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.pelanggan.update', $pelanggan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="nama" class="form-label form-label-coffee">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $pelanggan->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="no_telp" class="form-label form-label-coffee">No. Telepon / Login <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp', $pelanggan->no_telp) }}" required>
                        @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tanggal_lahir" class="form-label form-label-coffee">Tanggal Lahir</label>
                        <input type="date" class="form-control form-control-coffee @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pelanggan->tanggal_lahir?->format('Y-m-d')) }}">
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="status_member" class="form-label form-label-coffee">Level Member <span class="text-danger">*</span></label>
                        <select id="status_member" name="status_member" class="form-select form-control-coffee @error('status_member') is-invalid @enderror" required>
                            @foreach(['Bronze' => 'Bronze (Diskon 0%)', 'Silver' => 'Silver (Diskon 5%)', 'Gold' => 'Gold (Diskon 10%)'] as $nilai => $label)
                                <option value="{{ $nilai }}" {{ old('status_member', $pelanggan->status_member) === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status_member')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="poin" class="form-label form-label-coffee">Poin Reward <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control form-control-coffee @error('poin') is-invalid @enderror" id="poin" name="poin" value="{{ old('poin', $pelanggan->poin) }}" required>
                        @error('poin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label form-label-coffee">Akun Login</label>
                        <input type="text" class="form-control form-control-coffee" value="{{ $pelanggan->user ? 'Aktif (login dengan no. telepon: '.($pelanggan->user->no_telp ?? '-').')' : 'Belum ada akun login (member offline / POS)' }}" disabled>
                    </div>

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
