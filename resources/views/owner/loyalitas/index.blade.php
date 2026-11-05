@extends('layouts.crm')

@title('Loyalitas Pelanggan - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Monitoring Loyalitas Pelanggan</h2>
            <p class="text-muted-coffee">Pantau level member, total akumulasi belanja pelanggan, diskon khusus, dan keaktifan poin.</p>
        </div>
    </div>

    <!-- Tier Rule Guidelines Widget -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card-coffee h-100 border-bronze" style="border-left: 5px solid #8D6E63 !important;">
                <div class="card-coffee-body">
                    <h5 class="fw-bold text-bronze mb-2"><i class="fa-solid fa-user-tag me-1"></i> BRONZE LEVEL</h5>
                    <p class="text-muted-coffee mb-2" style="font-size: 0.85rem;">Syarat: Akumulasi Belanja &lt; Rp 500.000</p>
                    <div class="text-light fw-bold">Keuntungan: Poin standar (1 poin/Rp 10k), Diskon Member 0%</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card-coffee h-100 border-silver" style="border-left: 5px solid #B0BEC5 !important;">
                <div class="card-coffee-body">
                    <h5 class="fw-bold text-silver mb-2"><i class="fa-solid fa-medal me-1"></i> SILVER LEVEL</h5>
                    <p class="text-muted-coffee mb-2" style="font-size: 0.85rem;">Syarat: Akumulasi Belanja &gt;= Rp 500.000</p>
                    <div class="text-warning fw-bold">Keuntungan: Poin standar, Diskon Member Otomatis 5%</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card-coffee h-100 border-gold" style="border-left: 5px solid var(--accent-gold) !important;">
                <div class="card-coffee-body">
                    <h5 class="fw-bold text-gold mb-2"><i class="fa-solid fa-award me-1"></i> GOLD LEVEL</h5>
                    <p class="text-muted-coffee mb-2" style="font-size: 0.85rem;">Syarat: Akumulasi Belanja &gt;= Rp 1.000.000</p>
                    <div class="text-warning fw-bold">Keuntungan: Poin standar, Diskon Member Otomatis 10%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loyalty Database Table -->
    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-line me-2 text-warning"></i>Metrik Loyalitas CRM Member</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="loyalitasTable">
                    <thead>
                        <tr>
                            <th>Kode Pelanggan</th>
                            <th>Nama Member</th>
                            <th>Level Member</th>
                            <th>Total Akumulasi Belanja</th>
                            <th>Poin Saat Ini</th>
                            <th>Diskon Khusus</th>
                            <th>Tanggal Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loyalitas as $l)
                            <tr>
                                <td class="fw-bold text-warning">{{ $l->pelanggan->kode_pelanggan }}</td>
                                <td class="fw-bold text-light">{{ $l->pelanggan->nama }}</td>
                                <td>
                                    @if($l->level_member === 'Gold')
                                        <span class="badge-member badge-gold"><i class="fa-solid fa-award me-1"></i> Gold</span>
                                    @elseif($l->level_member === 'Silver')
                                        <span class="badge-member badge-silver"><i class="fa-solid fa-medal me-1"></i> Silver</span>
                                    @else
                                        <span class="badge-member badge-bronze"><i class="fa-solid fa-user-tag me-1"></i> Bronze</span>
                                    @endif
                                </td>
                                <td class="text-warning fw-bold">Rp {{ number_format($l->total_akumulasi_transaksi, 0, ',', '.') }}</td>
                                <td class="text-info fw-bold"><i class="fa-solid fa-coins me-1"></i> {{ $l->pelanggan->poin }} Poin</td>
                                <td class="text-success fw-bold">{{ $l->diskon_khusus }}%</td>
                                <td>
                                    {{ $l->tanggal_bergabung->format('d F Y') }}
                                    <small class="d-block text-muted-coffee" style="font-size: 0.8rem;">({{ $l->tanggal_bergabung->diffForHumans() }})</small>
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
        $('#loyalitasTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "order": [[3, "desc"]], // Sort by spending descending
            "responsive": true
        });
    });
</script>
@endsection
