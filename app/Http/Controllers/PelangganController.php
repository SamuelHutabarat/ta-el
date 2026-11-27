<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\Telepon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $pelanggan = Pelanggan::member()->with('user')->orderBy('id', 'desc')->get();
        return view('owner.pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show form to create new customer profile (and optional login account).
     */
    public function create()
    {
        return view('owner.pelanggan.create');
    }

    /**
     * Store a new customer. Member cukup nama + nomor telepon; kata sandi opsional untuk akun login.
     */
    public function store(Request $request)
    {
        $request->merge(['no_telp' => Telepon::normalisasi($request->input('no_telp'))]);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => ['required', 'string', 'max:20', 'unique:pelanggan,no_telp', 'unique:users,no_telp'],
            'tanggal_lahir' => 'nullable|date|before:today',
            'poin' => 'required|integer|min:0',
            'status_member' => ['required', 'string', Rule::in(['Bronze', 'Silver', 'Gold'])],
            // optional login account (login dengan nomor telepon)
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $akun = null;
        if ($request->filled('password')) {
            $akun = User::create([
                'name' => $data['nama'],
                'email' => null,
                'no_telp' => $data['no_telp'],
                'password' => Hash::make($data['password']),
                'role' => 'pelanggan',
            ]);
        }

        $pelanggan = Pelanggan::create([
            'user_id' => $akun?->id,
            'kode_pelanggan' => Pelanggan::kodeBerikutnya(),
            'nama' => $data['nama'],
            'no_telp' => $data['no_telp'],
            'poin' => $data['poin'],
            'status_member' => $data['status_member'],
            'total_belanja' => match ($data['status_member']) {
                'Gold' => Pelanggan::AMBANG_TIER['Gold'],
                'Silver' => Pelanggan::AMBANG_TIER['Silver'],
                default => 0,
            },
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'tanggal_bergabung' => Carbon::today()->toDateString(),
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
        $request->merge(['no_telp' => Telepon::normalisasi($request->input('no_telp'))]);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => ['required', 'string', 'max:20', Rule::unique('pelanggan', 'no_telp')->ignore($pelanggan->id), Rule::unique('users', 'no_telp')->ignore($pelanggan->user_id)],
            'tanggal_lahir' => 'nullable|date|before:today',
            'poin' => 'required|integer|min:0',
            'status_member' => ['required', 'string', Rule::in(['Bronze', 'Silver', 'Gold'])],
        ]);

        $pelanggan->update([
            'nama' => $data['nama'],
            'no_telp' => $data['no_telp'],
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'poin' => $data['poin'],
            'status_member' => $data['status_member'],
        ]);

        // Sync name & phone to the login account if linked
        if ($pelanggan->user) {
            $pelanggan->user->update([
                'name' => $data['nama'],
                'no_telp' => $data['no_telp'],
            ]);
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

    /**
     * Kartu member yang bisa dicetak (dilihat owner untuk pelanggan tertentu).
     */
    public function kartuOwner(Pelanggan $pelanggan)
    {
        return view('kartu.show', [
            'pelanggan' => $pelanggan,
            'kembali' => route('owner.pelanggan.index'),
        ]);
    }

    // ========================================================
    // CUSTOMER PORTAL FUNCTIONS (Role: pelanggan)
    // ========================================================

    /**
     * Display Customer Dashboard.
     */
    public function dashboard()
    {
        $pelanggan = $this->pelangganAkun();

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

        return view('pelanggan.dashboard', compact('pelanggan', 'recentTransactions', 'totalTransactionCount', 'totalSpent', 'promosi'));
    }

    /**
     * Kartu member pelanggan yang login (dapat dicetak untuk melihat poin reward).
     */
    public function kartu()
    {
        return view('kartu.show', [
            'pelanggan' => $this->pelangganAkun(),
            'kembali' => route('pelanggan.dashboard'),
        ]);
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

        $pelanggan = $this->pelangganAkun();

        return view('pelanggan.promosi', compact('promosi', 'pelanggan'));
    }

    /**
     * Customer transaction history.
     */
    public function transaksi()
    {
        $pelanggan = $this->pelangganAkun();
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
        $pelanggan = $this->pelangganAkun();

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
        $pelanggan = $this->pelangganAkun();

        if ($pelanggan->poin < $promo->poin_dibutuhkan) {
            return redirect()->back()->with('error', 'Poin Anda tidak mencukupi untuk mengklaim promo ini.');
        }

        // Deduct points
        $pelanggan->poin -= $promo->poin_dibutuhkan;
        $pelanggan->save();

        return redirect()->back()->with('success', 'Promo "' . $promo->nama_promo . '" berhasil diklaim! Tunjukkan kode kupon "' . $promo->kode_promo . '" ke Kasir saat pemesanan.');
    }

    /**
     * Profil member milik akun yang login; dibuat otomatis bila belum ada.
     */
    private function pelangganAkun(): Pelanggan
    {
        $user = auth()->user();

        return $user->pelanggan ?? Pelanggan::buatUntukAkun($user);
    }
}
