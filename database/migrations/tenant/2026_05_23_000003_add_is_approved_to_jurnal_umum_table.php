<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('jurnal_umum') && !Schema::hasColumn('jurnal_umum', 'is_approved')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->boolean('is_approved')->default(0)->after('is_locked');
            });

            // Mark existing historical journals as approved so we do not break existing reports
            DB::table('jurnal_umum')->update(['is_approved' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jurnal_umum') && Schema::hasColumn('jurnal_umum', 'is_approved')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->dropColumn('is_approved');
            });
        }
    }
};
