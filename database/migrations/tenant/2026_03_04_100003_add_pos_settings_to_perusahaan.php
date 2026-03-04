<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->string('pos_akun_kas_default', 20)->nullable()->after('akun_pendapatan_admin');
            $table->string('pos_akun_pendapatan_default', 20)->nullable()->after('pos_akun_kas_default');
            $table->string('pos_akun_hpp_default', 20)->nullable()->after('pos_akun_pendapatan_default');
            $table->string('pos_akun_persediaan_default', 20)->nullable()->after('pos_akun_hpp_default');
            $table->string('pos_akun_utang_default', 20)->nullable()->after('pos_akun_persediaan_default');
        });
    }

    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropColumn([
                'pos_akun_kas_default',
                'pos_akun_pendapatan_default',
                'pos_akun_hpp_default',
                'pos_akun_persediaan_default',
                'pos_akun_utang_default',
            ]);
        });
    }
};
