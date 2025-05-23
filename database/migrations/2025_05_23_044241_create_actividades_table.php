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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title', 64);
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price_per_person', 8, 2);
            $table->integer('popularity')->default(0);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['start_date', 'end_date']);
            $table->index('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
