<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();

        // 🔗 Link to user
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // 📚 Student info
        $table->string('matric_no');
        $table->string('course');
        $table->integer('year');
        $table->string('talent')->nullable();

        // 📊 Status (important for your system later)
        $table->string('status')->default('active');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
