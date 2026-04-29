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
        Schema::create('fixed_asset_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok');
            $table->integer('umur_ekonomis')->comment('Umur ekonomis dalam bulan');
            $table->string('metode_penyusutan')->default('Garis Lurus');
            $table->string('akun_aset', 20)->nullable();
            $table->string('akun_akumulasi_penyusutan', 20)->nullable();
            $table->string('akun_beban_penyusutan', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_groups');
    }
};
