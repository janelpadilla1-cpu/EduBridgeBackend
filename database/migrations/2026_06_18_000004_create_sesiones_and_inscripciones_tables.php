<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_ayudantia', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('oferta_ayudantia_id')->constrained('ofertas_ayudantia')->cascadeOnDelete();
            $table->foreignUuid('auxiliar_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->date('fecha')->index();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('aula_ref_id', 100)->index();
            $table->string('aula_nombre_cache', 150)->nullable();
            $table->string('estado', 30)->default('PROGRAMADA');
            $table->timestampsTz();
            $table->unique(['aula_ref_id', 'fecha', 'hora_inicio', 'hora_fin'], 'uq_sesiones_ayudantia_aula_horario');
            $table->check('hora_fin > hora_inicio');
            $table->index('oferta_ayudantia_id');
        });

        Schema::create('inscripciones_ayudantia', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('sesion_ayudantia_id')->constrained('sesiones_ayudantia')->cascadeOnDelete();
            $table->string('estado', 30)->default('INSCRITO');
            $table->timestampTz('fecha_inscripcion')->useCurrent();
            $table->boolean('asistencia')->nullable();
            $table->timestampsTz();
            $table->unique(['usuario_id', 'sesion_ayudantia_id']);
            $table->index('usuario_id');
            $table->index('sesion_ayudantia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_ayudantia');
        Schema::dropIfExists('sesiones_ayudantia');
    }
};
