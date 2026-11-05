@extends('layouts.crm')

@title('Kelola Promosi - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kelola Program Promosi & Voucher</h2>
            <p class="text-muted-coffee">Buat kupon diskon, promosi khusus gajian, dan penukaran poin reward loyalitas.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('owner.promosi.create') }}" class="btn btn-gold">
                <i class="fa-solid fa-plus me-2"></i> Buat Promosi Baru
            </a>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-tags me-2 text-warning"></i>Daftar Promosi Aktif</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="promosiTable">
                    <thead>
                        <tr>
                            <th>Nama Promosi</th>
                            <th>Kode Promo</th>
                            <th>Besar Diskon</th>
                            <th>Min. Belanja</th>
                            <th>Tukar Poin</th>
                            <th>Periode Promo</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promosi as $p)
                            <tr>
                                <td class="fw-bold text-light">{{ $p->nama_promo }}</td>
                                <td class="fw-bold text-warning"><code>{{ $p->kode_promo }}</code></td>
                                <td>
                                    @if($p->diskon_persen > 0)
                                        <span class="text-success fw-bold"><i class="fa-solid fa-percent"></i> {{ $p->diskon_persen }}%</span>
                                    @else
                                        <span class="text-success fw-bold">Rp {{ number_format($p->diskon_nominal, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($p->minimal_transaksi, 0, ',', '.') }}</td>
                                <td>
                                    @if($p->poin_dibutuhkan > 0)
                                        <span class="text-info fw-bold"><i class="fa-solid fa-coins me-1"></i> {{ $p->poin_dibutuhkan }} Poin</span>
                                    @else
                                        <span class="text-muted">Umum / Tanpa Poin</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block text-muted-coffee">Mulai: {{ $p->tanggal_mulai->format('d/m/Y') }}</small>
                                    <small class="d-block text-muted-coffee">Selesai: {{ $p->tanggal_selesai->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($p->status && $p->tanggal_selesai >= \Carbon\Carbon::today())
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-10 px-2 py-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-circle-play me-1"></i> Berjalan
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-10 px-2 py-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-circle-stop me-1"></i> Berakhir
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('owner.promosi.edit', $p->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-2.5" title="Edit Promo">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('owner.promosi.destroy', $p->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promosi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" title="Hapus Promo">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#promosiTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "order": [[0, "asc"]],
            "responsive": true
        });
    });
</script>
@endsection
