@extends('layouts.crm')

@section('title', 'Buat Promosi - CRM Owner')

@section('content')
<div class="container-fluid p-0" style="max-width: 850px; margin: 0 auto;">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('owner.promosi.index') }}" class="text-warning text-decoration-none fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Daftar Promo
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="font-family: 'Playfair Display', serif;">Buat Promosi Baru</h2>
            <p class="text-muted-coffee">Konfigurasikan diskon belanja dan penukaran poin member baru.</p>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-plus me-2 text-warning"></i>Formulir Aturan Promosi</h5>
        </div>
        <div class="card-coffee-body">
            <form action="{{ route('owner.promosi.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <!-- Promo Name -->
                    <div class="col-12 col-md-8">
                        <label for="nama_promo" class="form-label form-label-coffee">Nama Kampanye Promosi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('nama_promo') is-invalid @enderror" id="nama_promo" name="nama_promo" value="{{ old('nama_promo') }}" placeholder="Contoh: Promo Cashback Weekend" required>
                        @error('nama_promo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Promo Code -->
                    <div class="col-12 col-md-4">
                        <label for="kode_promo" class="form-label form-label-coffee">Kode Kupon / Voucher <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-coffee @error('kode_promo') is-invalid @enderror" id="kode_promo" name="kode_promo" value="{{ old('kode_promo') }}" placeholder="Contoh: TKPWEEKEND" required style="text-transform: uppercase;">
                        @error('kode_promo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Discount Type (Option 1: Percent) -->
                    <div class="col-12 col-md-6">
                        <label for="diskon_persen" class="form-label form-label-coffee">Besar Diskon Persen (%)</label>
                        <div class="input-group">
                            <input type="number" min="0" max="100" class="form-control form-control-coffee @error('diskon_persen') is-invalid @enderror" id="diskon_persen" name="diskon_persen" value="{{ old('diskon_persen', 0) }}" placeholder="10">
                            <span class="input-group-text bg-dark border-warning text-warning fw-bold">%</span>
                            @error('diskon_persen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted-coffee" style="font-size: 0.8rem;">Gunakan 0 jika ingin menggunakan nominal diskon tetap.</small>
                    </div>

                    <!-- Discount Type (Option 2: Nominal) -->
                    <div class="col-12 col-md-6">
                        <label for="diskon_nominal" class="form-label form-label-coffee">Besar Diskon Rupiah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-warning text-warning fw-bold">Rp</span>
                            <input type="number" step="0.01" class="form-control form-control-coffee @error('diskon_nominal') is-invalid @enderror" id="diskon_nominal" name="diskon_nominal" value="{{ old('diskon_nominal', 0) }}" placeholder="15000">
                            @error('diskon_nominal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted-coffee" style="font-size: 0.8rem;">Gunakan 0 jika sudah menggunakan diskon persen.</small>
                    </div>

                    <!-- Minimum Spending -->
                    <div class="col-12 col-md-6">
                        <label for="minimal_transaksi" class="form-label form-label-coffee">Minimal Belanja (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-warning text-warning fw-bold">Rp</span>
                            <input type="number" step="0.01" class="form-control form-control-coffee @error('minimal_transaksi') is-invalid @enderror" id="minimal_transaksi" name="minimal_transaksi" value="{{ old('minimal_transaksi', 0) }}" placeholder="50000" required>
                            @error('minimal_transaksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted-coffee" style="font-size: 0.8rem;">Batas minimum order sebelum promo bisa digunakan.</small>
                    </div>

                    <!-- Points Needed for Swapping -->
                    <div class="col-12 col-md-6">
                        <label for="poin_dibutuhkan" class="form-label form-label-coffee">Poin Reward yang Dibutuhkan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-warning text-info fw-bold"><i class="fa-solid fa-coins"></i></span>
                            <input type="number" class="form-control form-control-coffee @error('poin_dibutuhkan') is-invalid @enderror" id="poin_dibutuhkan" name="poin_dibutuhkan" value="{{ old('poin_dibutuhkan', 0) }}" placeholder="50">
                            @error('poin_dibutuhkan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted-coffee" style="font-size: 0.8rem;">Isi di atas 0 jika kupon ini hanya bisa diklaim menggunakan poin member.</small>
                    </div>

                    <!-- Start Date -->
                    <div class="col-12 col-md-4">
                        <label for="tanggal_mulai" class="form-label form-label-coffee">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-coffee @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', \Carbon\Carbon::today()->toDateString()) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-12 col-md-4">
                        <label for="tanggal_selesai" class="form-label form-label-coffee">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-coffee @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', \Carbon\Carbon::today()->addMonth()->toDateString()) }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-4">
                        <label for="status" class="form-label form-label-coffee">Status Aktif <span class="text-danger">*</span></label>
                        <select class="form-select form-control-coffee @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label form-label-coffee">Deskripsi Promosi / Syarat & Ketentuan</label>
                        <textarea class="form-control form-control-coffee @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Tulis rincian promo secara jelas...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action buttons -->
                    <div class="col-12 mt-4 text-end">
                        <button type="reset" class="btn btn-outline-gold me-2 px-4">Reset</button>
                        <button type="submit" class="btn btn-gold px-4">Simpan Promosi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
