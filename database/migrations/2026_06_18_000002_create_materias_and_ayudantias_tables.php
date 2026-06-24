<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('estado', 30)->default('ACTIVA')->index();
            $table->timestampsTz();
        });

        Schema::create('ofertas_ayudantia', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('materia_id')->constrained('materias')->restrictOnDelete();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->integer('cupo_maximo')->default(0);
            $table->string('estado', 30)->default('BORRADOR')->index();
            $table->timestampTz('fecha_creacion')->useCurrent();
            $table->timestampsTz();
            $table->index('materia_id');
        });

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'], true)) {
            DB::statement('ALTER TABLE ofertas_ayudantia ADD CONSTRAINT chk_ofertas_ayudantia_cupo_maximo CHECK (cupo_maximo >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas_ayudantia');
        Schema::dropIfExists('materias');
    }
};
