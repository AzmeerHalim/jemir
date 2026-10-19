<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('ic_number')->nullable()->after('matric_no');
            $table->string('phone')->nullable()->after('ic_number');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('gender');
            $table->text('address')->nullable()->after('nationality');
            $table->string('emergency_contact_name')->nullable()->after('status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->decimal('cgpa', 3, 2)->nullable()->after('emergency_contact_phone');
            $table->string('expected_graduation')->nullable()->after('cgpa');
            $table->string('faculty')->default('FSKTM')->after('expected_graduation');
            $table->string('department')->nullable()->after('faculty');
            $table->json('skills')->nullable()->after('department');
            $table->json('interests')->nullable()->after('skills');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'ic_number', 'phone', 'date_of_birth', 'gender', 'nationality',
                'address', 'emergency_contact_name', 'emergency_contact_phone',
                'cgpa', 'expected_graduation', 'faculty', 'department',
                'skills', 'interests',
            ]);
        });
    }
};
