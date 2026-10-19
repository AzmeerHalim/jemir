<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accompanying_lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('lecturer_name');
            $table->string('lecturer_email')->unique();
            $table->string('lecturer_phone')->nullable();
            $table->string('department')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accompanying_lecturers');
    }
};
