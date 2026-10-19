<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE achievements MODIFY category_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('achievements', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->change();
            });
        }

        Schema::table('achievements', function (Blueprint $table) {
            $table->foreignId('participation_id')->nullable()->after('category_id')->constrained('participations')->nullOnDelete();
            $table->unique('participation_id');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropUnique(['participation_id']);
            $table->dropForeign(['participation_id']);
            $table->dropColumn('participation_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE achievements MODIFY category_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
