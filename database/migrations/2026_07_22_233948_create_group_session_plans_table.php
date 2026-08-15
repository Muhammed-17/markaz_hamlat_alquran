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
        Schema::create('group_session_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('center_id')
                ->nullable()
                ->constrained('centers')
                ->nullOnDelete();

            $table->foreignId('circle_id')
                ->constrained('circles')
                ->cascadeOnDelete();

            $table->string('session_name', 255)
                ->comment('Session name/title');

            $table->time('start_time')
                ->comment('Session start time');

            $table->time('end_time')
                ->comment('Session end time');

            $table->text('planned_content')
                ->nullable()
                ->comment('Planned content for the session');

            $table->text('completed_content')
                ->nullable()
                ->comment('Content actually completed during the session');

            $table->text('notes')
                ->nullable()
                ->comment('Additional notes');

            $table->timestamps();

            $table->index('circle_id', 'idx_group_session_plans_circle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_session_plans');
    }
};
