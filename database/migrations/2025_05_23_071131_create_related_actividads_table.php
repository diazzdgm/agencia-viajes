<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('related_activity_id')->constrained('activities')->onDelete('cascade');
            
            // Índices para mejorar el rendimiento
            $table->index(['activity_id', 'related_activity_id']);
            $table->index(['related_activity_id', 'activity_id']);
            
            // Evitar duplicados
            $table->unique(['activity_id', 'related_activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_activities');
    }
};
