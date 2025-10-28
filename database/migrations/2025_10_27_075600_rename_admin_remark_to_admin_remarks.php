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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->text('admin_remarks')->nullable()->after('admin_remark');
        });
            
        // Copy data from old column to new column
        DB::statement('UPDATE leave_requests SET admin_remarks = admin_remark');
            
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('admin_remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->text('admin_remark')->nullable()->after('admin_remarks');
        });
            
        // Copy data back from new column to old column
        DB::statement('UPDATE leave_requests SET admin_remark = admin_remarks');
            
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('admin_remarks');
        });
    }
};