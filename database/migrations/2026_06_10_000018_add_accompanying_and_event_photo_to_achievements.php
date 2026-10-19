<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->foreignId('accompanying_id')->nullable()->after('participation_id')->constrained('accompanying_lecturers')->nullOnDelete();
            $table->string('event_photo_path')->nullable()->after('certificate');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['accompanying_id']);
            $table->dropColumn(['accompanying_id', 'event_photo_path']);
        });
    }
};
