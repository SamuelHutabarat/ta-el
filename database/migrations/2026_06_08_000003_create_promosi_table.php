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
        Schema::create('promosi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_promo');
            $table->string('kode_promo')->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('diskon_persen')->default(0);
            $table->decimal('diskon_nominal', 10, 2)->default(0);
            $table->decimal('minimal_transaksi', 10, 2)->default(0);
            $table->integer('poin_dibutuhkan')->default(0); // If they want to swap points for coupon
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('status')->default(true); // active or inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promosi');
    }
};
