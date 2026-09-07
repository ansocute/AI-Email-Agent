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
        Schema::table('agent_actions', function (Blueprint $table) {
            $table->timestamp('event_start')->nullable()->after('content');
            $table->timestamp('event_end')->nullable()->after('event_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_actions', function (Blueprint $table) {
            $table->dropColumn(['event_start', 'event_end']);
        });
    }
};
