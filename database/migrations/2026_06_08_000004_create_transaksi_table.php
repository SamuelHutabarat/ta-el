<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The barista or admin who made the transaction
            $table->decimal('total_harga', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_akhir', 12, 2);
            $table->decimal('total_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->string('metode_pembayaran'); // Cash, QRIS
            $table->string('status_pembayaran')->default('Lunas'); // Lunas, Pending
            $table->integer('poin_dapat')->default(0);
            $table->integer('poin_digunakan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
