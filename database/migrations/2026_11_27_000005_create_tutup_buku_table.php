<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutup_buku', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 20); // Harian, Mingguan, Bulanan
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->decimal('total_penjualan', 15, 2)->default(0);
            $table->unsignedInteger('total_transaksi')->default(0);
            $table->decimal('total_diskon', 15, 2)->default(0);
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->decimal('laba_kotor', 15, 2)->default(0);
            $table->foreignId('ditutup_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditutup_at')->nullable();
            $table->foreignId('dibuka_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibuka_at')->nullable(); // terisi = periode sudah dibuka kembali
            $table->timestamps();
            $table->index(['tipe', 'periode_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutup_buku');
    }
};
