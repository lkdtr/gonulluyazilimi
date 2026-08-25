<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->date('seminar_start_date')->nullable()->after('seminar_date');
            $table->date('seminar_end_date')->nullable()->after('seminar_start_date');
            $table->index(['status', 'seminar_start_date']);
        });

        DB::table('seminar_requests')->update([
            'seminar_start_date' => DB::raw('seminar_date'),
            'seminar_end_date' => DB::raw('seminar_date'),
        ]);
    }

    public function down(): void
    {
        Schema::table('seminar_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'seminar_start_date']);
            $table->dropColumn(['seminar_start_date', 'seminar_end_date']);
        });
    }
};
