<?php

use App\Http\Controllers\BelanjaBahanController;
use App\Http\Controllers\KritikSaranController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromosiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TutupBukuController;
use Illuminate\Support\Facades\Route;

// Redirect from homepage to login or dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Post-login redirect router based on roles
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isOwner()) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->isKasir()) {
        return redirect()->route('kasir.dashboard');
    } elseif ($user->isPelanggan()) {
        return redirect()->route('pelanggan.dashboard');
    }
    abort(403, 'Role tidak terdefinisi.');
})->middleware(['auth'])->name('dashboard');

// Profile routes (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 1. OWNER ROUTES (Role: owner)
// ==========================================
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

    // CRUD Produk (Menu Kopi)
    Route::resource('produk', ProdukController::class);

    // CRUD Pelanggan (CRM Member)
    Route::get('/pelanggan/{pelanggan}/kartu', [PelangganController::class, 'kartuOwner'])->name('pelanggan.kartu');
    Route::resource('pelanggan', PelangganController::class);

    // CRUD Promosi
    Route::resource('promosi', PromosiController::class);

    // CRUD Pengguna (Users/Employees)
    Route::resource('pengguna', OwnerController::class)->except(['dashboard', 'show']);

    // Kelola Transaksi (harian / mingguan / bulanan)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');

    // Laporan Penjualan & Export
    Route::get('/laporan', [OwnerController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/export-excel', [OwnerController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/export-pdf', [OwnerController::class, 'exportPdf'])->name('laporan.pdf');

    // Modal belanja bahan (per minggu & per bulan)
    Route::resource('belanja-bahan', BelanjaBahanController::class)->except(['show']);

    // Buka / tutup buku
    Route::get('/tutup-buku', [TutupBukuController::class, 'index'])->name('tutup-buku.index');
    Route::post('/tutup-buku', [TutupBukuController::class, 'store'])->name('tutup-buku.store');
    Route::post('/tutup-buku/{tutupBuku}/buka', [TutupBukuController::class, 'buka'])->name('tutup-buku.buka');

    // Kritik & saran dari pelanggan
    Route::get('/kritik-saran', [KritikSaranController::class, 'ownerIndex'])->name('kritik-saran.index');
});

// ==========================================
// 2. KASIR ROUTES (Role: owner, kasir)
// ==========================================
Route::middleware(['auth', 'role:owner,kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [TransaksiController::class, 'kasirDashboard'])->name('dashboard');
    Route::get('/produk', [TransaksiController::class, 'kasirProduk'])->name('produk');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');

    // POS / Checkout Transaksi
    Route::get('/transaksi/baru', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/checkout', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi', [TransaksiController::class, 'kasirHistory'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
});

// ==========================================
// 3. PELANGGAN ROUTES (Role: pelanggan)
// ==========================================
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/dashboard', [PelangganController::class, 'dashboard'])->name('dashboard');
    Route::get('/kartu', [PelangganController::class, 'kartu'])->name('kartu');
    Route::get('/produk', [PelangganController::class, 'produk'])->name('produk');
    Route::get('/promosi', [PelangganController::class, 'promosi'])->name('promosi');
    Route::get('/transaksi', [PelangganController::class, 'transaksi'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [PelangganController::class, 'transaksiDetail'])->name('transaksi.show');
    Route::post('/promosi/claim', [PelangganController::class, 'claimPromo'])->name('promosi.claim');
    Route::get('/kritik-saran', [KritikSaranController::class, 'pelangganIndex'])->name('kritik-saran.index');
    Route::post('/kritik-saran', [KritikSaranController::class, 'store'])->name('kritik-saran.store');
});

require __DIR__.'/auth.php';
