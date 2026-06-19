<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesionAyudantia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sesiones_ayudantia';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'oferta_ayudantia_id',
        'auxiliar_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'aula_ref_id',
        'aula_nombre_cache',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function ofertaAyudantia(): BelongsTo
    {
        return $this->belongsTo(OfertaAyudantia::class, 'oferta_ayudantia_id');
    }

    public function auxiliar(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'auxiliar_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionAyudantia::class, 'sesion_ayudantia_id');
    }

}
