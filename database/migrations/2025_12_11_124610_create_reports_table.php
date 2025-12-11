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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del reporte
            $table->date('start_date')->nullable(); // Fecha desde (opcional)
            $table->date('end_date')->nullable(); // Fecha hasta (opcional)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que generó el reporte
            $table->string('file_path')->nullable(); // Ruta del PDF generado (opcional)
            $table->enum('status', ['pending', 'generated', 'failed'])->default('pending'); // Estado del reporte
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
