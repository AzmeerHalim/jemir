<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('student_profiles')->cascadeOnDelete();
            $table->integer('total_score')->default(0);
            $table->integer('ranking')->nullable();
            $table->timestamp('last_recalculated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merits');
    }
};
