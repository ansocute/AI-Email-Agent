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
    Schema::create('calendar_events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agent_action_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->timestamp('start_time');
        $table->timestamp('end_time');
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
