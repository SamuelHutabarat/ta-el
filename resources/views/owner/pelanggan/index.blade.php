@extends('layouts.crm')

@section('title', 'Kelola Member - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kelola Member & Pelanggan</h2>
            <p class="text-muted-coffee">Pantau loyalitas member, akumulasi belanja, poin rewards, dan level keanggotaan.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('owner.pelanggan.create') }}" class="btn btn-gold">
                <i class="fa-solid fa-user-plus me-2"></i> Daftarkan Member Baru
            </a>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-id-card me-2 text-warning"></i>Database CRM Member Kopi TKP</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="pelangganTable">
                    <thead>
                        <tr>
                            <th>Kode Member</th>
                            <th>Nama Lengkap</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Poin Reward</th>
                            <th>Status Member</th>
                            <th>Akun Login</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggan as $p)
                            <tr>
                                <td class="fw-bold text-warning">{{ $p->kode_pelanggan }}</td>
                                <td class="fw-bold text-light">{{ $p->nama }}</td>
                                <td>{{ $p->no_telp ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 180px;">{{ $p->alamat ?? '-' }}</td>
                                <td class="fw-bold text-info">
                                    <i class="fa-solid fa-coins me-1"></i> {{ $p->poin }} Poin
                                </td>
                                <td>
                                    @if($p->status_member === 'Gold')
                                        <span class="badge-member badge-gold"><i class="fa-solid fa-award me-1"></i> Gold (Diskon 10%)</span>
                                    @elseif($p->status_member === 'Silver')
                                        <span class="badge-member badge-silver"><i class="fa-solid fa-medal me-1"></i> Silver (Diskon 5%)</span>
                                    @else
                                        <span class="badge-member badge-bronze"><i class="fa-solid fa-user-tag me-1"></i> Bronze (Diskon 0%)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->user)
                                        <span class="text-success" style="font-size: 0.9rem;"><i class="fa-solid fa-circle-check me-1"></i> Aktif ({{ $p->user->email }})</span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.9rem;"><i class="fa-solid fa-circle-minus me-1"></i> Offline / POS Only</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('owner.pelanggan.edit', $p->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-2.5" title="Edit Member">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('owner.pelanggan.destroy', $p->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini? Seluruh riwayat loyalitas dan akun login (jika ada) akan dihapus secara permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" title="Hapus Member">
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


