<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->enum('lecturer_status', ['pending', 'verified', 'rejected'])->default('pending')->after('status');
            $table->text('lecturer_notes')->nullable()->after('lecturer_status');
            $table->foreignId('lecturer_reviewed_by')->nullable()->after('lecturer_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('lecturer_reviewed_at')->nullable()->after('lecturer_reviewed_by');

            $table->enum('coordinator_status', ['pending', 'approved', 'rejected'])->default('pending')->after('lecturer_reviewed_at');
            $table->text('coordinator_notes')->nullable()->after('coordinator_status');
            $table->foreignId('coordinator_reviewed_by')->nullable()->after('coordinator_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('coordinator_reviewed_at')->nullable()->after('coordinator_reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['lecturer_reviewed_by']);
            $table->dropForeign(['coordinator_reviewed_by']);
            $table->dropColumn([
                'lecturer_status',
                'lecturer_notes',
                'lecturer_reviewed_by',
                'lecturer_reviewed_at',
                'coordinator_status',
                'coordinator_notes',
                'coordinator_reviewed_by',
                'coordinator_reviewed_at',
            ]);
        });
    }
};
