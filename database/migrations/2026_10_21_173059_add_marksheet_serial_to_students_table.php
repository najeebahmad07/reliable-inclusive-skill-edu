<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'marksheet_serial')) {
                $table->string('marksheet_serial', 50)->nullable()->after('marksheet_approved');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'marksheet_serial')) {
                $table->dropColumn('marksheet_serial');
            }
        });
    }
};
