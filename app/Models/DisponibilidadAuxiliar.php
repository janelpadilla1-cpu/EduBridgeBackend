<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibilidadAuxiliar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'disponibilidad_auxiliar';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    protected $casts = [
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

}
