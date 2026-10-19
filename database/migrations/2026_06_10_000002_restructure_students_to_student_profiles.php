<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: rename table
        Schema::rename('students', 'student_profiles');

        // Step 2: rename course -> programme_old and add new programme column
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->renameColumn('course', 'programme_old');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('programme')->nullable()->after('year');
        });

        \DB::statement('UPDATE student_profiles SET programme = programme_old');

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('programme_old');
            $table->string('student_name')->nullable()->after('user_id');
            $table->foreignId('cluster_id')->nullable()->after('talent')->constrained('clusters')->nullOnDelete();
            $table->string('student_status')->default('active')->after('cluster_id');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropForeign(['cluster_id']);
            $table->dropColumn(['cluster_id', 'student_status', 'student_name']);
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->renameColumn('programme', 'programme_old');
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('course')->nullable()->after('year');
        });

        \DB::statement('UPDATE student_profiles SET course = programme_old');

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('programme_old');
        });

        Schema::rename('student_profiles', 'students');
    }
};
