<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('external_user_ref', 100)->unique();
            $table->string('codigo_universitario', 50)->nullable()->unique();
            $table->string('correo_institucional', 150)->unique();
            $table->string('nombre_completo', 200);
            $table->string('estado', 30)->default('ACTIVO');
            $table->timestampTz('fecha_registro')->useCurrent();
            $table->timestampsTz();
            $table->index('estado');
        });

        Schema::create('cuentas_usuario', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->unique()->constrained('usuarios')->cascadeOnDelete();
            $table->text('password_hash');
            $table->string('estado', 30)->default('ACTIVA');
            $table->timestampTz('ultimo_acceso')->nullable();
            $table->timestampsTz();
        });

        Schema::create('roles_usuario', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('nombre', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        Schema::create('usuarios_roles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('rol_id')->constrained('roles_usuario')->cascadeOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->unique(['usuario_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
        Schema::dropIfExists('roles_usuario');
        Schema::dropIfExists('cuentas_usuario');
        Schema::dropIfExists('usuarios');
    }
};
