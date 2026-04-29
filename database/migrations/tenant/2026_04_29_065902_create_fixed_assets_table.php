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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelompok_aset_id');
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->date('tanggal_perolehan');
            $table->decimal('harga_perolehan', 15, 2);
            $table->decimal('nilai_residu', 15, 2)->default(0);
            $table->integer('umur_ekonomis_bulan');
            $table->decimal('nilai_buku_saat_ini', 15, 2);
            $table->string('status')->default('Aktif'); // Aktif, Dijual, Rusak
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->timestamps();

            $table->foreign('kelompok_aset_id')->references('id')->on('fixed_asset_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
