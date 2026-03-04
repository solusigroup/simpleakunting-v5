<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            // Agriculture (PSAK 69)
            $table->string('akun_aset_biologis', 20)->nullable()->after('akun_persediaan');
            $table->string('akun_keuntungan_revaluasi', 20)->nullable()->after('akun_aset_biologis');
            $table->string('akun_kerugian_revaluasi', 20)->nullable()->after('akun_keuntungan_revaluasi');
            // Closing (Tutup Buku)
            $table->string('akun_ikhtisar_laba_rugi', 20)->nullable()->after('akun_kerugian_revaluasi');
            $table->string('akun_laba_ditahan', 20)->nullable()->after('akun_ikhtisar_laba_rugi');
            // Pinjaman (Koperasi)
            $table->string('akun_pendapatan_provisi', 20)->nullable()->after('akun_laba_ditahan');
            $table->string('akun_pendapatan_admin', 20)->nullable()->after('akun_pendapatan_provisi');
        });
    }

    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropColumn([
                'akun_aset_biologis',
                'akun_keuntungan_revaluasi',
                'akun_kerugian_revaluasi',
                'akun_ikhtisar_laba_rugi',
                'akun_laba_ditahan',
                'akun_pendapatan_provisi',
                'akun_pendapatan_admin',
            ]);
        });
    }
};
