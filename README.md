# CRM Coffee Shop “Kopi Tempat Kamu Pulang (TKP)”
Aplikasi Customer Relationship Management (CRM) dan Loyalitas Pelanggan Berbasis Web untuk Coffee Shop Kopi TKP. Dibuat menggunakan **PHP, Laravel 11, MySQL, dan Bootstrap 5**. Project ini dirancang dengan standar profesional dan clean code untuk kebutuhan tugas akhir mahasiswa Sistem Informasi.

---

## 📌 Fitur Utama Sistem Berdasarkan Role

### 1. Owner (Pemilik Toko)
*   **Dashboard Statistik**: Grafik omset penjualan harian (7 hari terakhir), statistik total pendapatan, jumlah transaksi, total member, chart distribusi level member, menu terlaris, dan transaksi terbaru.
*   **CRUD Produk**: Manajemen menu kopi & produk (nama, kategori, harga, stok, deskripsi, upload gambar).
*   **CRUD Pelanggan (Member)**: Daftarkan member baru secara manual atau online, edit data, dan atur poin loyalitas.
*   **CRUD Promosi**: Pembuatan program diskon (persen/nominal), syarat minimal belanja, dan biaya penukaran poin reward.
*   **CRUD Pengguna**: Manajemen akun login karyawan (Owner, Barista, Pelanggan).
*   **Kelola Transaksi**: Pantau detail seluruh struk transaksi masuk.
*   **Laporan Penjualan**: Filter laporan penjualan berdasarkan tanggal mulai dan tanggal selesai.
*   **Export Laporan**: Export laporan ke format **Excel (.xls)** dan **PDF (Print thermal-friendly)** lengkap dengan tanda tangan formal.
*   **Monitoring Loyalitas Pelanggan**: Pantau total akumulasi belanja pelanggan, level member (Bronze, Silver, Gold), dan diskon khusus member.

### 2. Barista (Kasir)
*   **Dashboard Kasir**: Menampilkan omset penjualan kasir hari ini, jumlah transaksi kasir hari ini, dan orderan terbaru.
*   **POS Kasir (Checkout Transaksi)**: 
    *   Layar POS interaktif (keranjang belanja input kuantitas & hapus item).
    *   Pencarian menu kopi & filter kategori dinamis.
    *   Integrasi CRM: Otomatis menghitung diskon berdasarkan level member (Gold 10%, Silver 5%, Bronze 0%).
    *   Sistem redeem poin: Centang opsi potong 50 poin untuk diskon langsung Rp 22.000 (harga 1 cup kopi gratis).
    *   Validasi kode voucher promosi aktif.
    *   Pembayaran fleksibel: **Tunai (Cash)** dengan kalkulator kembalian otomatis, atau **QRIS** dengan QR Code dinamis modal scanner.
*   **Daftar Menu**: Melihat daftar menu kopi aktif beserta sisa stoknya.
*   **Riwayat Transaksi**: Melihat riwayat transaksi kasir yang diproses oleh barista bersangkutan.

### 3. Pelanggan (Member Portal)
*   **Dashboard Member**: Menampilkan **Loyalty Card Digital** (Gold, Silver, Bronze theme), jumlah poin reward terkini, diskon khusus member aktif, total belanja terakumulasi, total transaksi, dan ringkasan promo spesial.
*   **Menu Kopi**: Katalog online menu kopi Kopi TKP.
*   **Voucher Promo**: Lihat voucher aktif dan tombol penukaran poin langsung (klaim voucher gratis menggunakan poin).
*   **Riwayat Belanja**: Detail transaksi pembelian yang dilakukan secara member, status poin yang didapat, dan poin yang ditukar.

---

## 🔑 Akun Uji Coba Sidang (Default Credentials)
Semua akun di bawah ini telah disediakan secara otomatis oleh database seeder dengan password default: `password`.

| Role | Email Login | Password | Deskripsi / Keuntungan |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner@tkp.com` | `password` | Akses penuh seluruh statistik dan CRUD |
| **Barista** | `barista@tkp.com` | `password` | Akses kasir POS, menu, & riwayat kasir |
| **Pelanggan (Gold)** | `budi@tkp.com` | `password` | Member Gold, Akumulasi Rp 1.5M, Diskon 10%, 120 Poin |
| **Pelanggan (Silver)** | `siti@tkp.com` | `password` | Member Silver, Akumulasi Rp 600k, Diskon 5%, 45 Poin |
| **Pelanggan (Bronze)** | `rudi@tkp.com` | `password` | Member Bronze, Akumulasi Rp 150k, Diskon 0%, 10 Poin |

---

## ⚙️ Persyaratan Sistem (System Requirements)
*   PHP >= 8.2 (Direkomendasikan PHP 8.2 atau 8.3)
*   Composer
*   MySQL (melalui XAMPP)
*   Web Browser modern (Chrome/Firefox/Edge)

---

## 🚀 Cara Instalasi di Localhost XAMPP

1.  **Aktifkan XAMPP**:
    Buka XAMPP Control Panel, lalu aktifkan layanan **Apache** dan **MySQL**.
    
2.  **Siapkan Database**:
    Buka [http://localhost/phpmyadmin](http://localhost/phpmyadmin) di browser Anda, buat database baru dengan nama `kopi_tkp`.

3.  **Buka Terminal di Folder Project**:
    Buka terminal (Command Prompt / PowerShell / Git Bash) di folder project ini (`TA EL`).

4.  **Jalankan Migrasi & Seed Data**:
    Jalankan perintah berikut untuk membuat semua tabel MySQL beserta data tiruan penjualan 30 hari terakhir secara otomatis:
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Jalankan Local Dev Server**:
    Jalankan perintah PHP internal server:
    ```bash
    php artisan serve
    ```
    
6.  **Akses Web**:
    Buka browser Anda dan buka alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000). Anda akan langsung diarahkan ke halaman login.

---

## 📊 Skema & Relasi Database (Eloquent Laravel)
Project ini mengimplementasikan database relasional MySQL dengan relasi model sebagai berikut:

*   **User (`users`)**:
    *   Menampung login multi-user (`role` : `owner`, `barista`, `pelanggan`).
    *   `hasOne(Pelanggan)`: Menghubungkan akun user pelanggan dengan profil membernya.
*   **Pelanggan (`pelanggan`)**:
    *   Menyimpan info member (`kode_pelanggan`, `nama`, `no_telp`, `alamat`, `poin`, `status_member`).
    *   `belongsTo(User)`: Terhubung kembali ke akun user login.
    *   `hasMany(Transaksi)`: Satu pelanggan dapat memiliki banyak transaksi pembelian.
    *   `hasOne(LoyalitasPelanggan)`: Setiap pelanggan memiliki satu status loyalitas.
*   **Produk (`produk`)**:
    *   Menyimpan menu kopi (`nama_produk`, `kategori`, `harga`, `stok`, `gambar`, `deskripsi`).
    *   `hasMany(DetailTransaksi)`: Satu produk dapat dibeli di banyak detail baris transaksi.
*   **Transaksi (`transaksi`)**:
    *   Menyimpan penjualan (`kode_transaksi`, `total_harga`, `diskon`, `total_akhir`, `total_bayar`, `kembalian`, `metode_pembayaran`, `poin_dapat`, `poin_digunakan`).
    *   `belongsTo(Pelanggan)`: Transaksi terhubung ke pembeli member (atau `null` jika pembeli non-member/guest).
    *   `belongsTo(User)`: Terhubung ke barista/kasir yang memproses.
    *   `hasMany(DetailTransaksi)`: Satu transaksi memiliki banyak detail menu produk yang dibeli.
*   **Detail Transaksi (`detail_transaksi`)**:
    *   Menyimpan detail kuantitas item (`transaksi_id`, `produk_id`, `jumlah`, `harga_satuan`, `subtotal`).
    *   `belongsTo(Transaksi)`: Terhubung ke transaksi induk.
    *   `belongsTo(Produk)`: Terhubung ke produk menu.
*   **Promosi (`promosi`)**:
    *   Menyimpan program promo (`nama_promo`, `kode_promo`, `diskon_persen`, `diskon_nominal`, `minimal_transaksi`, `poin_dibutuhkan`, `tanggal_mulai`, `tanggal_selesai`, `status`).
*   **Loyalitas Pelanggan (`loyalitas_pelanggan`)**:
    *   Menyimpan statistik loyalitas (`pelanggan_id`, `total_akumulasi_transaksi`, `level_member`, `diskon_khusus`, `tanggal_bergabung`).
    *   `belongsTo(Pelanggan)`: Terhubung kembali ke pelanggan.

---

## 💡 Mekanisme CRM & Loyalitas yang Diuji pada Sidang

1.  **Perhitungan Poin Otomatis**:
    Ketika barista memproses transaksi, setiap kelipatan **Rp 10.000** dari total bayar bersih akan menghasilkan **1 Poin** yang ditambahkan langsung ke saldo member pelanggan.
2.  **Auto-Upgrade Level Member (Tiers)**:
    Sistem melacak total uang yang dibelanjakan pelanggan (`total_akumulasi_transaksi`). 
    *   Jika akumulasi mencapai **Rp 500.000**, level member otomatis naik ke **Silver** (Diskon otomatis 5% setiap transaksi).
    *   Jika akumulasi mencapai **Rp 1.000.000**, level member otomatis naik ke **Gold** (Diskon otomatis 10% setiap transaksi).
3.  **Redeem Poin**:
    Pelanggan dapat menukarkan poin reward miliknya dengan kupon gratis di web portal, atau langsung mengklaim pemotongan harga di kasir (Redeem kelipatan 50 poin = Potongan Rp 22.000).
4.  **Metode Bayar QRIS & Cash**:
    Jika memilih QRIS, kasir menampilkan QR Code dinamis dan pembayaran dianggap pas (tunai exact). Jika memilih Cash, kasir menghitung nominal kembalian secara presisi berdasarkan uang tunai yang diserahkan pelanggan.
