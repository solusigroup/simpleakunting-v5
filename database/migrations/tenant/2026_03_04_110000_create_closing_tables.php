<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('periode_tutup_buku')) {
            Schema::create('periode_tutup_buku', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('bulan');
                $table->unsignedSmallInteger('tahun');
                $table->date('tanggal_tutup');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('status', 20)->default('tutup');
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->unique(['bulan', 'tahun']);
            });
        }

        if (!Schema::hasTable('ikhtisar_laba_rugi')) {
            Schema::create('ikhtisar_laba_rugi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('periode_id');
                $table->decimal('total_pendapatan', 18, 2)->default(0);
                $table->decimal('total_beban', 18, 2)->default(0);
                $table->decimal('laba_rugi_bersih', 18, 2)->default(0);
                $table->timestamps();

                $table->foreign('periode_id')->references('id')->on('periode_tutup_buku')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ikhtisar_laba_rugi');
        Schema::dropIfExists('periode_tutup_buku');
    }
};
