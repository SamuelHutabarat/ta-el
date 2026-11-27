<form action="{{ $action }}" method="POST">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <label for="tanggal" class="form-label form-label-coffee">Tanggal Belanja <span class="text-danger">*</span></label>
            <input type="date" id="tanggal" name="tanggal" class="form-control form-control-coffee @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $belanja->tanggal?->format('Y-m-d')) }}" required>
            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label for="kategori" class="form-label form-label-coffee">Kategori <span class="text-danger">*</span></label>
            <select id="kategori" name="kategori" class="form-select form-control-coffee @error('kategori') is-invalid @enderror" required>
                @foreach(\App\Models\BelanjaBahan::KATEGORI as $kat)
                    <option value="{{ $kat }}" {{ old('kategori', $belanja->kategori) === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label for="nama_bahan" class="form-label form-label-coffee">Nama Bahan <span class="text-danger">*</span></label>
            <input type="text" id="nama_bahan" name="nama_bahan" class="form-control form-control-coffee @error('nama_bahan') is-invalid @enderror" value="{{ old('nama_bahan', $belanja->nama_bahan) }}" placeholder="Contoh: Biji Kopi Arabika" required>
            @error('nama_bahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6 col-md-3">
            <label for="jumlah" class="form-label form-label-coffee">Jumlah <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" id="jumlah" name="jumlah" class="form-control form-control-coffee @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $belanja->jumlah) }}" required>
            @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6 col-md-3">
            <label for="satuan" class="form-label form-label-coffee">Satuan <span class="text-danger">*</span></label>
            <input type="text" id="satuan" name="satuan" class="form-control form-control-coffee @error('satuan') is-invalid @enderror" value="{{ old('satuan', $belanja->satuan) }}" placeholder="kg, liter, pcs" required>
            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="harga_total" class="form-label form-label-coffee">Total Harga (Rp) <span class="text-danger">*</span></label>
            <input type="number" step="1" min="0" id="harga_total" name="harga_total" class="form-control form-control-coffee @error('harga_total') is-invalid @enderror" value="{{ old('harga_total', $belanja->harga_total !== null ? (int) $belanja->harga_total : '') }}" placeholder="Contoh: 240000" required>
            @error('harga_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label for="keterangan" class="form-label form-label-coffee">Keterangan</label>
            <input type="text" id="keterangan" name="keterangan" class="form-control form-control-coffee @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', $belanja->keterangan) }}" placeholder="Opsional: nama supplier, catatan">
            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 text-end mt-3">
            <a href="{{ route('owner.belanja-bahan.index') }}" class="btn btn-outline-gold me-2 px-4">Batal</a>
            <button type="submit" class="btn btn-gold px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button>
        </div>
    </div>
</form>
