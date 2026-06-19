<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'materias';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
    ];

    public function ofertasAyudantia(): HasMany
    {
        return $this->hasMany(OfertaAyudantia::class, 'materia_id');
    }

    public function postulacionesAuxiliar(): HasMany
    {
        return $this->hasMany(PostulacionAuxiliar::class, 'materia_id');
    }

    public function auxiliaresMateria(): HasMany
    {
        return $this->hasMany(AuxiliarMateria::class, 'materia_id');
    }

}
