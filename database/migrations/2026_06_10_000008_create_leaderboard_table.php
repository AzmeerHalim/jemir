<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('student_profiles')->cascadeOnDelete();
            $table->integer('total_points')->default(0);
            $table->integer('position')->nullable();
            $table->string('programme_filter')->nullable();
            $table->string('category_filter')->nullable();
            $table->string('year_filter')->nullable();
            $table->timestamp('snapshot_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
