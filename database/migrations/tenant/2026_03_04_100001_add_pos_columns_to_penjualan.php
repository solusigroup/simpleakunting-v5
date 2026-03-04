<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('sumber', 20)->default('Manual')->after('status_pembayaran');
            $table->unsignedBigInteger('id_pos_session')->nullable()->after('sumber');
            $table->decimal('diskon_total', 15, 2)->default(0)->after('id_pos_session');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn(['sumber', 'id_pos_session', 'diskon_total']);
        });
    }
};
