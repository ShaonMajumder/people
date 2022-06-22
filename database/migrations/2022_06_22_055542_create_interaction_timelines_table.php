<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteractionTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interaction_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interaction_status_id')->references('id')->on('interaction_statuses')->cascadeOnDelete();
            $table->foreignId('target_id')->nullable()->references('id')->on('people')->cascadeOnDelete();
            $table->foreignId('causer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('occurance_type')->nullable(); // sudden, planned
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interaction_timelines');
    }
}
