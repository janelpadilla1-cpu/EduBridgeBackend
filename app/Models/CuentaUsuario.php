<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaUsuario extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cuentas_usuario';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'password_hash',
        'estado',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'ultimo_acceso' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

}
