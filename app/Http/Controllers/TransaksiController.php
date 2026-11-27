<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Support\Periode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    /**
     * Owner view: List all transactions per harian, mingguan, atau bulanan.
     */
    public function index(Request $request)
    {
        $periode = Periode::dariRequest($request, 'bulanan');

        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->dalamPeriode($periode['mulai'], $periode['selesai'])
            ->orderBy('id', 'desc')
            ->get();

        return view('owner.transaksi.index', compact('transaksi', 'periode'));
    }

    /**
     * Shared view: Show invoice detail.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['detail_transaksi.produk', 'pelanggan', 'user'])
            ->findOrFail($id);

        return view('shared.transaksi.show', compact('transaksi'));
    }

    /**
     * Kasir Dashboard: Stats and recent orders.
     */
    public function kasirDashboard()
    {
        $today = Carbon::today();

        $todayRevenue = Transaksi::whereDate('created_at', $today)
            ->where('status_pembayaran', 'Lunas')
            ->sum('total_akhir');

        $todayTransactions = Transaksi::whereDate('created_at', $today)->count();
        $totalProductsCount = Produk::count();

        $recentTransactions = Transaksi::with(['pelanggan'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact('todayRevenue', 'todayTransactions', 'totalProductsCount', 'recentTransactions'));
    }

    /**
     * Kasir view: Menu cards with stock.
     */
    public function kasirProduk()
    {
        $produk = Produk::orderBy('kategori')->get();
        return view('kasir.produk', compact('produk'));
    }

    /**
     * Kasir history.
     */
    public function kasirHistory()
    {
        $transaksi = Transaksi::with(['pelanggan'])
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
        return view('kasir.history', compact('transaksi'));
    }

    /**
     * POS Form: Show checkout POS view.
     */
    public function create()
    {
        $produk = Produk::where('stok', '>', 0)->orderBy('kategori')->get();
        $pelanggan = Pelanggan::member()->orderBy('nama')->get();
        $promosi = Promosi::where('status', true)
            ->where('tanggal_mulai', '<=', Carbon::today())
            ->where('tanggal_selesai', '>=', Carbon::today())
            ->get();

        return view('kasir.transaksi.create', compact('produk', 'pelanggan', 'promosi'));
    }

    /**
     * POS Action: Process checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.produk_id' => 'required|exists:produk,id',
            'cart.*.qty' => 'required|integer|min:1',
            'pelanggan_id' => 'nullable|exists:pelanggan,id',
            'kode_promo' => 'nullable|string',
            'poin_digunakan' => 'nullable|integer|min:0',
            'metode_pembayaran' => 'required|string|in:Cash,QRIS',
            'sumber_transaksi' => ['nullable', Rule::in(Transaksi::SUMBER)],
            'total_bayar' => 'required|numeric|min:0',
        ]);

        // Wrap transaction in DB Transaction to ensure database integrity
        return DB::transaction(function () use ($request) {
            $cart = $request->cart;
            $pelangganId = $request->pelanggan_id;
            $kodePromo = $request->kode_promo;
            $paymentMethod = $request->metode_pembayaran;
            $totalBayar = $request->total_bayar;
            $sumber = $request->input('sumber_transaksi') ?: 'Offline';

            $totalHarga = 0;
            $detailItems = [];

            // 1. Calculate base items pricing and check stock
            foreach ($cart as $item) {
                $produk = Produk::findOrFail($item['produk_id']);

                if ($produk->stok < $item['qty']) {
                    throw new \Exception('Stok produk "' . $produk->nama_produk . '" tidak mencukupi.');
                }

                $subtotal = $produk->harga * $item['qty'];
                $totalHarga += $subtotal;

                $detailItems[] = [
                    'produk_id' => $produk->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $produk->harga,
                    'subtotal' => $subtotal,
                    'produk_model' => $produk, // keep model reference to deduct stock later
                ];
            }

            // 2. Initialize discounts
            $diskon = 0;
            $pelanggan = null;

            // Apply Member Level Discount (only real members, never kasir/owner accounts)
            if ($pelangganId) {
                $pelanggan = Pelanggan::member()->findOrFail($pelangganId);

                $memberDiscountPersen = $pelanggan->diskonPersen();
                if ($memberDiscountPersen > 0) {
                    $diskon += ($totalHarga * ($memberDiscountPersen / 100));
                }
            }

            // Apply Promo Code Discount
            if ($kodePromo) {
                $promo = Promosi::where('kode_promo', $kodePromo)
                    ->where('status', true)
                    ->where('tanggal_mulai', '<=', Carbon::today())
                    ->where('tanggal_selesai', '>=', Carbon::today())
                    ->first();

                if ($promo) {
                    // Validate minimum purchase
                    if ($totalHarga >= $promo->minimal_transaksi) {
                        if ($promo->diskon_persen > 0) {
                            $diskon += ($totalHarga * ($promo->diskon_persen / 100));
                        }
                        if ($promo->diskon_nominal > 0) {
                            $diskon += $promo->diskon_nominal;
                        }
                    }
                }
            }

            // Apply Point Redemption Discount
            $pointsRedeemed = 0;
            if ($pelanggan && $request->poin_digunakan > 0) {
                // Redeem in units of 50 points (50 pts = Rp 22.000 discount)
                $requestedPoints = (int) $request->poin_digunakan;
                $availableRedeemUnits = floor(min($requestedPoints, $pelanggan->poin) / 50);
                if ($availableRedeemUnits > 0) {
                    $pointsRedeemed = $availableRedeemUnits * 50;
                    $diskon += ($availableRedeemUnits * 22000.00);
                }
            }

            // Final totals
            $totalAkhir = max(0, $totalHarga - $diskon);

            // Validate QRIS vs Cash payments
            if ($paymentMethod === 'QRIS') {
                $totalBayar = $totalAkhir; // QRIS is exact payment
            }

            if ($totalBayar < $totalAkhir) {
                throw new \Exception('Jumlah pembayaran kurang dari total tagihan.');
            }

            $kembalian = $totalBayar - $totalAkhir;

            // Poin reward: setiap kelipatan nilai belanja sesuai kanal (datang langsung / online)
            $poinDapat = (int) floor($totalAkhir / config('crm.rupiah_per_poin.'.$sumber, 10000));

            // Generate unique sequential order number
            $todayStr = Carbon::now()->format('Ymd');
            $todayCount = Transaksi::whereDate('created_at', Carbon::today())->count() + 1;
            $kodeTransaksi = 'TRX-' . $todayStr . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);
            while (Transaksi::where('kode_transaksi', $kodeTransaksi)->exists()) {
                $todayCount++;
                $kodeTransaksi = 'TRX-' . $todayStr . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);
            }

            // 3. Create Transaction Record
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'pelanggan_id' => $pelanggan ? $pelanggan->id : null,
                'user_id' => auth()->id(),
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'total_akhir' => $totalAkhir,
                'total_bayar' => $totalBayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $paymentMethod,
                'sumber_transaksi' => $sumber,
                'status_pembayaran' => 'Lunas',
                'poin_dapat' => $poinDapat,
                'poin_digunakan' => $pointsRedeemed,
            ]);

            // 4. Create Details & Deduct Stock
            foreach ($detailItems as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Deduct inventory stock
                $prod = $item['produk_model'];
                $prod->stok -= $item['qty'];
                $prod->save();
            }

            // 5. CRM Updates: poin, total belanja, dan level member
            if ($pelanggan) {
                $pelanggan->poin = max(0, $pelanggan->poin - $pointsRedeemed + $poinDapat);
                $pelanggan->total_belanja = (float) $pelanggan->total_belanja + $totalAkhir;
                $pelanggan->status_member = Pelanggan::tierUntuk((float) $pelanggan->total_belanja);
                $pelanggan->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'redirect_url' => route('kasir.transaksi.show', $transaksi->id)
            ]);
        });
    }
}
