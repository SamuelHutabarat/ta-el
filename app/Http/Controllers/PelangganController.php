<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\Promosi;
use App\Models\LoyalitasPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PelangganController extends Controller
{
    // ========================================================
    // OWNER FUNCTIONS (CRUD Pelanggan/Members)
    // ========================================================

    /**
     * List all customers/members for the Owner dashboard.
     */
    public function index()
    {
        $pelanggan = Pelanggan::orderBy('id', 'desc')->get();
        return view('owner.pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show form to create new customer profile (and optional user account).
     */
    public function create()
    {
        return view('owner.pelanggan.create');
    }

    /**
     * Store a new customer and create associated credentials.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'poin' => 'required|integer|min:0',
            'status_member' => 'required|string|in:Bronze,Silver,Gold',
            // optional account credentials
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $userId = null;

        // If email and password are provided, create login account
        if ($request->filled('email') && $request->filled('password')) {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pelanggan',
            ]);
            $userId = $user->id;
        }

        // Generate unique member code
        $latestPelanggan = Pelanggan::orderBy('id', 'desc')->first();
        $nextNum = $latestPelanggan ? ((int)substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;
        $kode = 'PLG-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $pelanggan = Pelanggan::create([
            'user_id' => $userId,
            'kode_pelanggan' => $kode,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp ?? '-',
            'alamat' => $request->alamat ?? '-',
            'poin' => $request->poin,
            'status_member' => $request->status_member,
        ]);

        // Create loyalty tracker record
        $diskonKhusus = 0;
        $accumulated = 0;
        if ($request->status_member === 'Gold') {
            $diskonKhusus = 10;
            $accumulated = 1000000.00;
        } elseif ($request->status_member === 'Silver') {
            $diskonKhusus = 5;
            $accumulated = 500000.00;
        }

        LoyalitasPelanggan::create([
            'pelanggan_id' => $pelanggan->id,
            'total_akumulasi_transaksi' => $accumulated,
            'level_member' => $request->status_member,
            'diskon_khusus' => $diskonKhusus,
            'tanggal_bergabung' => Carbon::now(),
        ]);

        return redirect()->route('owner.pelanggan.index')->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    /**
     * Show form to edit member details.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('owner.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update customer member information.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'poin' => 'required|integer|min:0',
            'status_member' => 'required|string|in:Bronze,Silver,Gold',
        ]);

        $pelanggan->update($request->only(['nama', 'no_telp', 'alamat', 'poin', 'status_member']));

        // Synchronize member level discount
        $diskonKhusus = 0;
        if ($request->status_member === 'Gold') {
            $diskonKhusus = 10;
        } elseif ($request->status_member === 'Silver') {
            $diskonKhusus = 5;
        }

        $loyalty = LoyalitasPelanggan::where('pelanggan_id', $pelanggan->id)->first();
        if ($loyalty) {
            $loyalty->level_member = $request->status_member;
            $loyalty->diskon_khusus = $diskonKhusus;
            $loyalty->save();
        }

        // Sync name to user account if linked
        if ($pelanggan->user) {
            $pelanggan->user->name = $request->nama;
            $pelanggan->user->save();
        }

        return redirect()->route('owner.pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Delete customer and their credentials.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Delete user credentials if linked
        if ($pelanggan->user) {
            $pelanggan->user->delete();
        } else {
            $pelanggan->delete();
        }

        return redirect()->route('owner.pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }

    // ========================================================
    // CUSTOMER PORTAL FUNCTIONS (Role: pelanggan)
    // ========================================================

    /**
     * Display Customer Dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Find associated customer record (or create one on the fly if missing)
        $pelanggan = $user->pelanggan;
        if (!$pelanggan) {
            $latestPelanggan = Pelanggan::orderBy('id', 'desc')->first();
            $nextNum = $latestPelanggan ? ((int)substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;
            $kode = 'PLG-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $pelanggan = Pelanggan::create([
                'user_id' => $user->id,
                'kode_pelanggan' => $kode,
                'nama' => $user->name,
                'no_telp' => '-',
                'alamat' => '-',
                'poin' => 0,
                'status_member' => 'Bronze',
            ]);

            LoyalitasPelanggan::create([
                'pelanggan_id' => $pelanggan->id,
                'total_akumulasi_transaksi' => 0,
                'level_member' => 'Bronze',
                'diskon_khusus' => 0,
                'tanggal_bergabung' => Carbon::now(),
            ]);
        }

        $loyalty = $pelanggan->loyalitas_pelanggan;
        
        // Get customer statistics
        $recentTransactions = Transaksi::where('pelanggan_id', $pelanggan->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalTransactionCount = Transaksi::where('pelanggan_id', $pelanggan->id)->count();
        $totalSpent = Transaksi::where('pelanggan_id', $pelanggan->id)->sum('total_akhir');

        // Active promotions available
        $promosi = Promosi::where('status', true)
            ->where('tanggal_selesai', '>=', Carbon::today())
            ->limit(3)
            ->get();

        return view('pelanggan.dashboard', compact('pelanggan', 'loyalty', 'recentTransactions', 'totalTransactionCount', 'totalSpent', 'promosi'));
    }

    /**
     * Display coffee shop products for customers.
     */
    public function produk()
    {
        $produk = \App\Models\Produk::orderBy('kategori')->get();
        return view('pelanggan.produk', compact('produk'));
    }

    /**
     * Display promotions page for customers, allowing points swap.
     */
    public function promosi()
    {
        $promosi = Promosi::where('status', true)
            ->where('tanggal_selesai', '>=', Carbon::today())
            ->orderBy('poin_dibutuhkan', 'asc')
            ->get();

        $pelanggan = auth()->user()->pelanggan;

        return view('pelanggan.promosi', compact('promosi', 'pelanggan'));
    }

    /**
     * Customer transaction history.
     */
    public function transaksi()
    {
        $pelanggan = auth()->user()->pelanggan;
        $transaksi = Transaksi::where('pelanggan_id', $pelanggan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.transaksi.index', compact('transaksi'));
    }

    /**
     * Customer transaction details.
     */
    public function transaksiDetail($id)
    {
        $pelanggan = auth()->user()->pelanggan;
        
        // Ensure the transaction belongs to the logged-in customer
        $transaksi = Transaksi::with(['detail_transaksi.produk', 'user'])
            ->where('pelanggan_id', $pelanggan->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('pelanggan.transaksi.show', compact('transaksi'));
    }

    /**
     * Claim a promo using reward points.
     */
    public function claimPromo(Request $request)
    {
        $request->validate([
            'promosi_id' => 'required|exists:promosi,id',
        ]);

        $promo = Promosi::findOrFail($request->promosi_id);
        $pelanggan = auth()->user()->pelanggan;

        if ($pelanggan->poin < $promo->poin_dibutuhkan) {
            return redirect()->back()->with('error', 'Poin Anda tidak mencukupi untuk mengklaim promo ini.');
        }

        // Deduct points
        $pelanggan->poin -= $promo->poin_dibutuhkan;
        $pelanggan->save();

        return redirect()->back()->with('success', 'Promo "' . $promo->nama_promo . '" berhasil diklaim! Tunjukkan kode kupon "' . $promo->kode_promo . '" ke Barista saat pemesanan.');
    }
}
