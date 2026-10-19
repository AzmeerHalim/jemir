<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->date('date_achieved')->nullable()->after('category_id');
            $table->string('event_name')->nullable()->after('date_achieved');
            $table->string('organizer')->nullable()->after('event_name');
            $table->string('location')->nullable()->after('organizer');
            $table->text('description')->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn(['date_achieved', 'event_name', 'organizer', 'location', 'description']);
        });
    }
};
