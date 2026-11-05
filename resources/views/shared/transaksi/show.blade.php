@extends('layouts.crm')

@title('Detail Nota Transaksi')

@section('styles')
<style>
    /* Receipt Box Design */
    .receipt-box {
        background-color: #fff;
        color: #333;
        border-radius: 12px;
        padding: 2.5rem;
        max-width: 480px;
        margin: 0 auto;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        position: relative;
        font-family: 'Courier New', Courier, monospace;
        border: 1px solid #ddd;
    }
    
    .receipt-box::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 10px;
        background: linear-gradient(-45deg, #fff 5px, transparent 0), linear-gradient(45deg, #fff 5px, transparent 0);
        background-position: left bottom;
        background-repeat: repeat-x;
        background-size: 10px 10px;
        transform: translateY(5px);
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px dashed #888;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .receipt-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.45rem;
        color: #2C1B18;
        margin: 0;
    }

    .receipt-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .receipt-divider {
        border-top: 1px dashed #888;
        margin: 1rem 0;
    }

    .receipt-items-table {
        width: 100%;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .receipt-items-table th {
        border-bottom: 1px dashed #888;
        padding-bottom: 0.5rem;
        text-align: left;
    }

    .receipt-items-table td {
        padding: 0.5rem 0;
    }

    @media print {
        #sidebar-wrapper, .top-navbar, .footer, .btn, .no-print {
            display: none !important;
        }
        #page-content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
            background: none !important;
        }
        .main-content {
            padding: 0 !important;
            background: none !important;
        }
        .receipt-box {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            color: #000 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Action buttons -->
    <div class="row mb-4 no-print">
        <div class="col-12 text-center text-md-start d-flex justify-content-between align-items-center">
            @if(Auth::user()->role === 'owner')
                <a href="{{ route('owner.transaksi.index') }}" class="text-warning text-decoration-none fw-bold">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Riwayat Transaksi
                </a>
            @else
                <a href="{{ route('barista.transaksi.index') }}" class="text-warning text-decoration-none fw-bold">
                    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Riwayat Kasir
                </a>
            @endif
            
            <div class="d-flex gap-2">
                <button onclick="window.print();" class="btn btn-gold">
                    <i class="fa-solid fa-print me-1"></i> Cetak Struk (Nota)
                </button>
            </div>
        </div>
    </div>

    <!-- Receipt Thermal View -->
    <div class="receipt-box">
        <div class="receipt-header">
            <h4 class="receipt-title">KOPI TEMPAT KAMU PULANG</h4>
            <div style="font-size: 0.8rem; color: #555; margin-top: 4px;">Tempat Kamu Pulang Jl. Meruyung</div>
            <div style="font-size: 0.8rem; color: #555;">Telp: (022) 1234567</div>
        </div>

        <!-- Meta Data -->
        <div class="receipt-line">
            <span>NO. NOTA:</span>
            <span class="fw-bold">{{ $transaksi->kode_transaksi }}</span>
        </div>
        <div class="receipt-line">
            <span>WAKTU:</span>
            <span>{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="receipt-line">
            <span>BARISTA:</span>
            <span>{{ strtoupper($transaksi->user->name) }}</span>
        </div>
        <div class="receipt-line">
            <span>PELANGGAN:</span>
            <span>{{ $transaksi->pelanggan ? strtoupper($transaksi->pelanggan->nama) : 'UMUM / GUEST' }}</span>
        </div>
        @if($transaksi->pelanggan)
            <div class="receipt-line">
                <span>KODE MEMBER:</span>
                <span>{{ $transaksi->pelanggan->kode_pelanggan }} ({{ strtoupper($transaksi->pelanggan->status_member) }})</span>
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Items Table -->
        <table class="receipt-items-table">
            <thead>
                <tr>
                    <th>MENU</th>
                    <th style="text-align: center; width: 60px;">QTY</th>
                    <th style="text-align: right; width: 100px;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->detail_transaksi as $det)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $det->produk->nama_produk }}</div>
                            <div style="font-size: 0.8rem; color: #666;">@ Rp {{ number_format($det->harga_satuan, 0, ',', '.') }}</div>
                        </td>
                        <td style="text-align: center;">{{ $det->jumlah }}</td>
                        <td style="text-align: right;">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-divider"></div>

        <!-- Financial Summary -->
        <div class="receipt-line">
            <span>SUBTOTAL:</span>
            <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->diskon > 0)
            <div class="receipt-line text-danger">
                <span>POTONGAN DISKON:</span>
                <span>-Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @endif
        
        <div class="receipt-line fw-bold" style="font-size: 1.05rem; border-top: 1px dashed #888; padding-top: 0.5rem; margin-top: 0.5rem;">
            <span>TOTAL AKHIR:</span>
            <span>Rp {{ number_format($transaksi->total_akhir, 0, ',', '.') }}</span>
        </div>
        
        <div class="receipt-divider"></div>

        <div class="receipt-line">
            <span>METODE BAYAR:</span>
            <span>{{ $transaksi->metode_pembayaran }}</span>
        </div>
        <div class="receipt-line">
            <span>DITERIMA:</span>
            <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="receipt-line">
            <span>KEMBALIAN:</span>
            <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
        </div>

        @if($transaksi->pelanggan)
            <div class="receipt-divider"></div>
            <!-- CRM Poin rewards update -->
            <div class="receipt-line text-info fw-bold" style="font-size: 0.85rem;">
                <span>POIN DIPEROLEH:</span>
                <span>+{{ $transaksi->poin_dapat }} POIN</span>
            </div>
            @if($transaksi->poin_digunakan > 0)
                <div class="receipt-line text-danger fw-bold" style="font-size: 0.85rem;">
                    <span>POIN DITUKAR:</span>
                    <span>-{{ $transaksi->poin_digunakan }} POIN</span>
                </div>
            @endif
            <div class="receipt-line" style="font-size: 0.85rem;">
                <span>SALDO POIN AKHIR:</span>
                <span>{{ $transaksi->pelanggan->poin }} POIN</span>
            </div>
        @endif

        <div class="receipt-divider"></div>
        
        <div class="text-center" style="font-size: 0.8rem; color: #555; font-style: italic; line-height: 1.4;">
            Terima kasih sudah pulang ke tempat kami.<br>
            Kami tunggu kepulanganmu berikutnya!
        </div>
    </div>
</div>
@endsection
