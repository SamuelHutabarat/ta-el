# CRM Coffee Shop “Kopi Tempat Kamu Pulang (TKP)”
Aplikasi Customer Relationship Management (CRM) Berbasis Web untuk Coffee Shop Kopi TKP. Dibuat menggunakan **PHP, Laravel 11, MySQL, dan Bootstrap 5**. Project ini dirancang dengan standar profesional dan clean code untuk kebutuhan tugas akhir mahasiswa Sistem Informasi.

---

## 📌 Fitur Utama Sistem Berdasarkan Role

Sistem memiliki tiga role: **Owner**, **Kasir**, dan **Pelanggan**.

### 1. Owner (Pemilik Toko)
*   **Dashboard CRM**: omset 7 hari terakhir, total pendapatan, jumlah transaksi, total member, distribusi level member, menu terlaris, transaksi terbaru, dan **hari penting pelanggan setia** (ulang tahun dan ulang tahun keanggotaan Gold/Silver dalam 30 hari ke depan).
*   **CRUD Produk**: manajemen menu kopi & produk (nama, kategori, harga, stok, deskripsi, upload gambar).
*   **CRUD Pelanggan (Member)**: daftarkan member cukup dengan nama dan nomor telepon (kata sandi opsional untuk login), edit data, atur poin reward dan level, serta cetak **kartu member**.
*   **CRUD Promosi**: program diskon (persen/nominal), syarat minimal belanja, dan biaya penukaran poin reward.
*   **CRUD Pengguna**: akun login Owner, Kasir, dan Pelanggan.
*   **Kelola Transaksi**: riwayat transaksi per **harian, mingguan, atau bulanan**, lengkap dengan kanal (datang langsung / online).
*   **Laporan Penjualan**: omset, diskon, modal belanja bahan, dan selisihnya per harian, mingguan, atau bulanan; export CSV (Excel) dan PDF.
*   **Modal Belanja Bahan**: catat belanja bahan baku, dengan rekap modal **per minggu** dan **per bulan** berikut perbandingan omset.
*   **Buka / Tutup Buku**: menutup buku periode yang sudah berakhir (harian, mingguan, bulanan). Belanja pada periode yang sudah ditutup terkunci sampai buku dibuka kembali.
*   **Kritik & Saran**: masukan dari member.

### 2. Kasir
*   **Dashboard Kasir**: omset dan jumlah transaksi kasir hari ini, serta pesanan terbaru.
*   **POS Kasir (Checkout)**: keranjang belanja, pilih member CRM, kode promo, tukar poin, **sumber transaksi** (datang langsung atau online) yang menentukan perolehan poin, dan pembayaran Tunai atau QRIS.
*   **Daftar Menu** dan **Riwayat Transaksi** kasir.

### 3. Pelanggan (Member)
*   **Dashboard Member**: level, saldo poin reward, diskon otomatis, total belanja, dan riwayat belanja terakhir.
*   **Kartu Member**: lihat dan cetak kartu yang menampilkan level dan poin reward.
*   **Menu Kopi TKP**: klik foto menu untuk melihat deskripsi, harga, dan ketersediaan.
*   **Voucher Reward Poin** dan **Riwayat Transaksi**.
*   **Kritik & Saran**: kirim masukan dan lihat riwayat kiriman.

### Registrasi & Login
*   Pelanggan mendaftar cukup dengan **nama dan nomor telepon** (plus kata sandi), lalu login memakai **nomor telepon**.
*   Owner dan Kasir login memakai **email**.

---

## 🔑 Akun Uji Coba Sidang (Default Credentials)
Semua akun di bawah ini disediakan otomatis oleh database seeder dengan password default: `password`.

| Role | Login | Password | Deskripsi / Keuntungan |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner@tkp.com` | `password` | Akses penuh seluruh statistik, CRUD, laporan, dan tutup buku |
| **Kasir** | `owit@tkp.com` | `password` | Kasir POS, menu, & riwayat transaksi (Owit) |
| **Kasir** | `tantri@tkp.com` | `password` | Kasir POS, menu, & riwayat transaksi (Tantri) |
| **Pelanggan (Gold)** | `081234567890` | `password` | Budi Sudrajat, Gold, 120 poin, login dengan nomor telepon |
| **Pelanggan (Silver)** | `081987654321` | `password` | Siti Aminah, Silver, 45 poin, login dengan nomor telepon |
| **Pelanggan (Bronze)** | `08561234567` | `password` | Rudi Hermawan, Bronze, 15 poin, login dengan nomor telepon |

---

### Upgrade Database yang Sudah Ada
Jika database sudah berisi data dari versi sebelumnya, cukup jalankan `php artisan migrate`. Role `barista` otomatis berubah menjadi `kasir`, tabel loyalitas dipindahkan ke data pelanggan lalu dihapus, dan profil member milik staf dilepas. Akun seed `barista@tkp.com` ikut menjadi kasir, jadi hapus lewat menu **Kelola Pengguna** jika hanya ingin dua kasir (owit & tantri).

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
    *   Menampung login multi-user (`role`: `owner`, `kasir`, `pelanggan`).
    *   `email` wajib untuk staf (owner & kasir), sedangkan `no_telp` dipakai member untuk login.
    *   `hasOne(Pelanggan)`: Menghubungkan akun pelanggan dengan profil membernya. Kasir dan owner tidak pernah memiliki profil member.
*   **Pelanggan (`pelanggan`)**:
    *   Menyimpan info member (`kode_pelanggan`, `nama`, `no_telp`, `poin`, `status_member`, `total_belanja`, `tanggal_lahir`, `tanggal_bergabung`). Kolom `alamat` tidak lagi diisi dari form.
    *   `belongsTo(User)`: Terhubung ke akun login (opsional untuk member yang didaftarkan offline).
    *   `hasMany(Transaksi)` dan `hasMany(KritikSaran)`.
*   **Produk (`produk`)**:
    *   Menyimpan menu kopi (`nama_produk`, `kategori`, `harga`, `stok`, `gambar`, `deskripsi`).
    *   `hasMany(DetailTransaksi)`: Satu produk dapat dibeli di banyak detail baris transaksi.
*   **Transaksi (`transaksi`)**:
    *   Menyimpan penjualan (`kode_transaksi`, `total_harga`, `diskon`, `total_akhir`, `total_bayar`, `kembalian`, `metode_pembayaran`, `sumber_transaksi` (Offline/Online), `poin_dapat`, `poin_digunakan`).
    *   `belongsTo(Pelanggan)`: Transaksi terhubung ke pembeli member (atau `null` jika guest).
    *   `belongsTo(User)`: Terhubung ke kasir yang memproses.
    *   `hasMany(DetailTransaksi)`: Satu transaksi memiliki banyak detail menu produk.
*   **Detail Transaksi (`detail_transaksi`)**:
    *   Menyimpan detail kuantitas item (`transaksi_id`, `produk_id`, `jumlah`, `harga_satuan`, `subtotal`).
*   **Promosi (`promosi`)**:
    *   Menyimpan program promo (`nama_promo`, `kode_promo`, `diskon_persen`, `diskon_nominal`, `minimal_transaksi`, `poin_dibutuhkan`, `tanggal_mulai`, `tanggal_selesai`, `status`).
*   **Kritik & Saran (`kritik_saran`)**: masukan dari member (`pelanggan_id`, `jenis` Kritik/Saran, `isi`).
*   **Belanja Bahan (`belanja_bahan`)**: modal belanja bahan baku (`tanggal`, `nama_bahan`, `kategori`, `jumlah`, `satuan`, `harga_total`).
*   **Tutup Buku (`tutup_buku`)**: ringkasan final periode yang sudah ditutup (omset, transaksi, diskon, modal, laba kotor) beserta penutup dan waktu buka kembali.

---

## 💡 Mekanisme CRM yang Diuji pada Sidang

1.  **Perhitungan Poin Otomatis (per kanal)**:
    *   **Datang langsung (Offline)**: setiap kelipatan **Rp 10.000** dari total bayar bersih menghasilkan **1 Poin**.
    *   **Pesanan online (Online)**: setiap kelipatan **Rp 20.000** menghasilkan **1 Poin**.
    *   Nilai ini diatur di `config/crm.php`. Kasir memilih sumber transaksi di POS.
2.  **Auto-Upgrade Level Member (Tiers)**:
    Sistem melacak total belanja pelanggan (`total_belanja`).
    *   Jika total belanja mencapai **Rp 500.000**, level naik ke **Silver** (diskon otomatis 5% setiap transaksi).
    *   Jika total belanja mencapai **Rp 1.000.000**, level naik ke **Gold** (diskon otomatis 10% setiap transaksi).
3.  **Hari Penting Pelanggan Setia**:
    Dashboard owner menampilkan ulang tahun dan ulang tahun keanggotaan member Gold & Silver dalam 30 hari ke depan.
4.  **Redeem Poin**:
    Pelanggan dapat menukarkan poin reward dengan kupon di web portal, atau langsung mengklaim potongan di kasir (kelipatan 50 poin = potongan Rp 22.000).
5.  **Kartu Member**:
    Member dan owner dapat membuka kartu member yang menampilkan level dan poin reward, lalu mencetaknya.
6.  **Metode Bayar QRIS & Cash**:
    Jika memilih QRIS, kasir menampilkan QR Code dinamis dan pembayaran dianggap pas. Jika memilih Cash, kasir menghitung kembalian berdasarkan uang tunai yang diserahkan pelanggan.
7.  **Laporan & Tutup Buku**:
    Laporan dapat dilihat per harian, mingguan, atau bulanan. Setelah periode berakhir, owner menutup buku sehingga belanja bahan pada periode itu terkunci.
