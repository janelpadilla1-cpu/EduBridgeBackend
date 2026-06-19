<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionAyudantia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inscripciones_ayudantia';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'sesion_ayudantia_id',
        'estado',
        'fecha_inscripcion',
        'asistencia',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'asistencia' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function sesionAyudantia(): BelongsTo
    {
        return $this->belongsTo(SesionAyudantia::class, 'sesion_ayudantia_id');
    }

}
