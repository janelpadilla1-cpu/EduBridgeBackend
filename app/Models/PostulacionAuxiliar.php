<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostulacionAuxiliar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'postulaciones_auxiliar';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'materia_id',
        'motivo',
        'experiencia',
        'estado',
        'fecha_postulacion',
    ];

    protected $casts = [
        'fecha_postulacion' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

}
