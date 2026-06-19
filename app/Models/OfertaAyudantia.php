<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfertaAyudantia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ofertas_ayudantia';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'materia_id',
        'titulo',
        'descripcion',
        'cupo_maximo',
        'estado',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'cupo_maximo' => 'integer',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionAyudantia::class, 'oferta_ayudantia_id');
    }

}
