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
        Schema::create('fixed_asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aset_id');
            $table->date('tanggal_pelepasan');
            $table->string('jenis_pelepasan'); // Dijual, Dibuang, Rusak
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->string('akun_kas', 20)->nullable();
            $table->string('akun_laba_rugi_pelepasan', 20)->nullable();
            $table->unsignedBigInteger('jurnal_id')->nullable();
            $table->timestamps();

            $table->foreign('aset_id')->references('id')->on('fixed_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_disposals');
    }
};
