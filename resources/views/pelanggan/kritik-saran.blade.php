@extends('layouts.crm')

@section('title', 'Kritik & Saran - Member Kopi TKP')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kritik &amp; Saran</h2>
            <p class="text-muted-coffee">Ceritakan pengalaman Anda, kami membaca setiap masukan untuk memperbaiki menu dan layanan.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="card-coffee h-100">
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Kirim Masukan</h5>
                </div>
                <div class="card-coffee-body">
                    <form method="POST" action="{{ route('pelanggan.kritik-saran.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="jenis" class="form-label form-label-coffee">Jenis Masukan</label>
                            <select id="jenis" name="jenis" class="form-select form-control-coffee @error('jenis') is-invalid @enderror" required>
                                <option value="Saran" {{ old('jenis', 'Saran') === 'Saran' ? 'selected' : '' }}>Saran</option>
                                <option value="Kritik" {{ old('jenis') === 'Kritik' ? 'selected' : '' }}>Kritik</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="isi" class="form-label form-label-coffee">Isi Kritik / Saran</label>
                            <textarea id="isi" name="isi" rows="5" minlength="10" maxlength="1000" class="form-control form-control-coffee @error('isi') is-invalid @enderror" placeholder="Minimal 10 karakter" required>{{ old('isi') }}</textarea>
                            @error('isi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-gold w-100"><i class="fa-solid fa-paper-plane me-1"></i> Kirim</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card-coffee h-100">
                <div class="card-coffee-header">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Kiriman Anda</h5>
                </div>
                <div class="card-coffee-body p-0">
                    <div class="table-responsive">
                        <table class="table table-coffee m-0">
                            <thead>
                                <tr><th>Tanggal</th><th>Jenis</th><th>Isi</th></tr>
                            </thead>
                            <tbody>
                                @forelse($daftar as $k)
                                    <tr>
                                        <td style="white-space: nowrap;">{{ $k->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $k->jenis }}</td>
                                        <td>{{ $k->isi }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted-coffee py-4">Belum ada kiriman. Masukan Anda sangat berarti bagi kami.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
