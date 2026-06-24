<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulaciones_auxiliar', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('materia_id')->constrained('materias')->restrictOnDelete();
            $table->text('motivo')->nullable();
            $table->text('experiencia')->nullable();
            $table->string('estado', 30)->default('PENDIENTE');
            $table->timestampTz('fecha_postulacion')->useCurrent();
            $table->timestampsTz();
            $table->index('usuario_id');
            $table->index('materia_id');
        });

        Schema::create('auxiliares_materia', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('materia_id')->constrained('materias')->restrictOnDelete();
            $table->string('estado', 30)->default('ACTIVO');
            $table->timestampTz('fecha_asignacion')->useCurrent();
            $table->timestampsTz();
            $table->unique(['usuario_id', 'materia_id']);
            $table->index('usuario_id');
            $table->index('materia_id');
        });

        Schema::create('disponibilidad_auxiliar', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('dia_semana', 20);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estado', 30)->default('ACTIVA');
            $table->timestampsTz();
            $table->unique(['usuario_id', 'dia_semana', 'hora_inicio', 'hora_fin'], 'uq_disponibilidad_auxiliar');
        });

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'], true)) {
            DB::statement('ALTER TABLE disponibilidad_auxiliar ADD CONSTRAINT chk_disponibilidad_auxiliar_horas CHECK (hora_fin > hora_inicio)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidad_auxiliar');
        Schema::dropIfExists('auxiliares_materia');
        Schema::dropIfExists('postulaciones_auxiliar');
    }
};
