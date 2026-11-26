<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Promosi;
use App\Models\Transaksi;
use App\Models\LoyalitasPelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CrmSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_owner_can_access_dashboard_with_chart_data(): void
    {
        $owner = User::where('role', 'owner')->first();

        $response = $this->actingAs($owner)->get('/owner/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartValues');
        $response->assertViewHas('levelLabels');
        $response->assertViewHas('levelCounts');
        $response->assertSee('Grafik Omset 7 Hari Terakhir');
        $response->assertSee('Distribusi Level Member');
    }

    public function test_barista_can_access_pos_screen(): void
    {
        $barista = User::where('role', 'barista')->first();

        $response = $this->actingAs($barista)->get('/barista/transaksi/baru');
        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
        $response->assertSee('Pilih Member CRM');
    }

    public function test_pos_checkout_creates_transaction_and_deducts_stock(): void
    {
        $barista = User::where('role', 'barista')->first();
        $produk = Produk::where('stok', '>=', 5)->first();
        $initialStock = $produk->stok;

        $payload = [
            'cart' => [
                [
                    'produk_id' => $produk->id,
                    'qty' => 2,
                ]
            ],
            'pelanggan_id' => null,
            'kode_promo' => null,
            'poin_digunakan' => 0,
            'metode_pembayaran' => 'Cash',
            'total_bayar' => $produk->harga * 2,
        ];

        $response = $this->actingAs($barista)
            ->postJson('/barista/transaksi/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify stock deducted
        $this->assertEquals($initialStock - 2, $produk->fresh()->stok);
    }

    public function test_member_can_access_customer_dashboard(): void
    {
        $memberUser = User::where('role', 'pelanggan')->first();
        $response = $this->actingAs($memberUser)->get('/pelanggan/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal Loyalty Member');
        $response->assertSee('Saldo Poin Anda');
    }

    public function test_user_can_access_profile_edit_with_crm_layout(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun');
        $response->assertSee('Informasi Akun');
        $response->assertSee('Ubah Kata Sandi');
    }

    public function test_product_with_sales_history_cannot_be_deleted(): void
    {
        $owner = User::where('role', 'owner')->first();
        $produkWithSales = Produk::has('detail_transaksi')->first();

        $response = $this->actingAs($owner)
            ->delete('/owner/produk/' . $produkWithSales->id);

        $response->assertRedirect('/owner/produk');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('produk', ['id' => $produkWithSales->id]);
    }

    public function test_barista_can_open_product_create_and_edit_screens(): void
    {
        $barista = User::where('role', 'barista')->first();
        $produk = Produk::firstOrFail();

        $this->actingAs($barista)
            ->get('/barista/produk/create')
            ->assertOk()
            ->assertSee('Foto Produk');

        $this->get('/barista/produk/' . $produk->id . '/edit')
            ->assertOk()
            ->assertSee('Ganti Foto Produk');
    }

    public function test_barista_can_add_a_menu_with_its_photo(): void
    {
        $barista = User::where('role', 'barista')->first();
        $imagePath = null;
        $temporaryImage = storage_path('framework/testing/menu-photo-test.jpg');
        File::ensureDirectoryExists(dirname($temporaryImage));
        copy(public_path('uploads/produk/1794564783_cafe_latte.jpg'), $temporaryImage);

        try {
            $response = $this->actingAs($barista)->post('/barista/produk', [
                'nama_produk' => 'Menu Foto Kasir Test',
                'kategori' => 'Kopi',
                'harga' => 19000,
                'stok' => 12,
                'deskripsi' => 'Produk untuk uji unggah gambar.',
                'gambar' => new \Illuminate\Http\UploadedFile(
                    $temporaryImage,
                    'menu.jpg',
                    'image/jpeg',
                    null,
                    true
                ),
            ]);

            $response->assertRedirect('/barista/produk');
            $produk = Produk::where('nama_produk', 'Menu Foto Kasir Test')->firstOrFail();
            $imagePath = public_path($produk->gambar);
            $this->assertFileExists($imagePath);
        } finally {
            if ($imagePath && is_file($imagePath)) {
                unlink($imagePath);
            }
            if (is_file($temporaryImage)) {
                unlink($temporaryImage);
            }
        }
    }

    public function test_menu_photo_mapping_and_cashier_roles_are_corrected_by_data_migration(): void
    {
        $produk = Produk::where('nama_produk', 'Americano')->firstOrFail();
        $produk->update(['gambar' => 'uploads/produk/espresso_latte.png']);

        foreach ([['Owit', 'owit@tkp.com'], ['Tantri', 'tantri@tkp.com']] as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => 'pelanggan',
            ]);
        }

        $migration = require base_path('database/migrations/2026_11_25_000001_map_menu_images_and_cashier_roles.php');
        $migration->up();

        $this->assertSame('uploads/produk/1794564621_americano.jpg', $produk->fresh()->gambar);
        $this->assertSame(2, User::whereIn('email', ['owit@tkp.com', 'tantri@tkp.com'])->where('role', 'barista')->count());
    }
}
