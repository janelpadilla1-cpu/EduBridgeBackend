<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $table = 'usuarios';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'external_user_ref',
        'codigo_universitario',
        'correo_institucional',
        'nombre_completo',
        'estado',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];


    public function getAuthPassword(): string
    {
        return (string) optional($this->cuenta)->password_hash;
    }

    public function cuenta(): HasOne
    {
        return $this->hasOne(CuentaUsuario::class, 'usuario_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(RolUsuario::class, 'usuarios_roles', 'usuario_id', 'rol_id')
            ->withPivot('id', 'created_at');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionAyudantia::class, 'usuario_id');
    }

    public function postulacionesAuxiliar(): HasMany
    {
        return $this->hasMany(PostulacionAuxiliar::class, 'usuario_id');
    }

    public function auxiliaresMateria(): HasMany
    {
        return $this->hasMany(AuxiliarMateria::class, 'usuario_id');
    }

    public function disponibilidades(): HasMany
    {
        return $this->hasMany(DisponibilidadAuxiliar::class, 'usuario_id');
    }

    public function sesionesComoAuxiliar(): HasMany
    {
        return $this->hasMany(SesionAyudantia::class, 'auxiliar_id');
    }

}
