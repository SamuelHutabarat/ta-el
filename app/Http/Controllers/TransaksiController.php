<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LoyalitasPelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class TransaksiController extends Controller
{
    /**
     * Owner view: List all transactions.
     */
    public function index()
    {
        $transaksi = Transaksi::with(['pelanggan', 'user'])->orderBy('id', 'desc')->get();
        return view('owner.transaksi.index', compact('transaksi'));
    }

    /**
     * Shared view: Show invoice detail.
     */
    public function show($id)
    {
        $transaksi = Transaksi::with(['detail_transaksi.produk', 'pelanggan.loyalitas_pelanggan', 'user'])
            ->findOrFail($id);

        return view('shared.transaksi.show', compact('transaksi'));
    }

    /**
     * Barista Dashboard: Stats and recent orders.
     */
    public function baristaDashboard()
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

        return view('barista.dashboard', compact('todayRevenue', 'todayTransactions', 'totalProductsCount', 'recentTransactions'));
    }

    /**
     * Barista view: Menu cards with stock.
     */
    public function baristaProduk()
    {
        $produk = Produk::orderBy('kategori')->get();
        return view('barista.produk', compact('produk'));
    }

    /**
     * Barista history.
     */
    public function baristaHistory()
    {
        $transaksi = Transaksi::with(['pelanggan'])
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
        return view('barista.history', compact('transaksi'));
    }

    /**
     * POS Form: Show checkout POS view.
     */
    public function create()
    {
        $produk = Produk::where('stok', '>', 0)->orderBy('kategori')->get();
        $pelanggan = Pelanggan::orderBy('nama')->get();
        $promosi = Promosi::where('status', true)
            ->where('tanggal_mulai', '<=', Carbon::today())
            ->where('tanggal_selesai', '>=', Carbon::today())
            ->get();

        return view('barista.transaksi.create', compact('produk', 'pelanggan', 'promosi'));
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
            'total_bayar' => 'required|numeric|min:0',
        ]);

        // Wrap transaction in DB Transaction to ensure database integrity
        return DB::transaction(function () use ($request) {
            $cart = $request->cart;
            $pelangganId = $request->pelanggan_id;
            $kodePromo = $request->kode_promo;
            $usePoints = $request->poin_digunakan > 0;
            $paymentMethod = $request->metode_pembayaran;
            $totalBayar = $request->total_bayar;

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
            $memberDiscountPersen = 0;
            $pelanggan = null;

            // Apply Member Level Discount (from CRM Loyalitas)
            if ($pelangganId) {
                $pelanggan = Pelanggan::findOrFail($pelangganId);
                
                // Get customer membership tier discount percentage
                if ($pelanggan->status_member === 'Gold') {
                    $memberDiscountPersen = 10; // 10% discount
                } elseif ($pelanggan->status_member === 'Silver') {
                    $memberDiscountPersen = 5; // 5% discount
                }

                if ($memberDiscountPersen > 0) {
                    $diskon += ($totalHarga * ($memberDiscountPersen / 100));
                }
            }

            // Apply Promo Code Discount
            $promo = null;
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
            if ($pelanggan && $usePoints) {
                // Let's assume they redeem points in increments of 50 points
                // 50 points = Rp 22.000 discount (cost of signature coffee)
                $maxPossibleRedeems = floor($pelanggan->poin / 50);
                if ($maxPossibleRedeems > 0) {
                    $pointsRedeemed = $maxPossibleRedeems * 50;
                    $diskon += ($maxPossibleRedeems * 22000.00);
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

            // Calculate new points earned (1 point per 10k spent)
            $poinDapat = floor($totalAkhir / 10000);

            // Generate unique order number
            $todayStr = Carbon::now()->format('Ymd');
            $randomStr = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $kodeTransaksi = 'TRX-' . $todayStr . '-' . $randomStr;

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

            // 5. CRM Updates: Points & Loyalty Tiering
            if ($pelanggan) {
                // Update customer points
                $pelanggan->poin = max(0, $pelanggan->poin - $pointsRedeemed + $poinDapat);
                $pelanggan->save();

                // Update accumulated spending
                $loyalty = LoyalitasPelanggan::where('pelanggan_id', $pelanggan->id)->first();
                if ($loyalty) {
                    $loyalty->total_akumulasi_transaksi += $totalAkhir;
                    
                    // Determine new member status tier
                    $oldTier = $loyalty->level_member;
                    $newTier = 'Bronze';
                    $newDiscount = 0;

                    if ($loyalty->total_akumulasi_transaksi >= 1000000.00) {
                        $newTier = 'Gold';
                        $newDiscount = 10;
                    } elseif ($loyalty->total_akumulasi_transaksi >= 500000.00) {
                        $newTier = 'Silver';
                        $newDiscount = 5;
                    }

                    $loyalty->level_member = $newTier;
                    $loyalty->diskon_khusus = $newDiscount;
                    $loyalty->save();

                    // Keep pelanggan main status synced
                    $pelanggan->status_member = $newTier;
                    $pelanggan->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'redirect_url' => route('barista.transaksi.show', $transaksi->id)
            ]);
        });
    }
}
