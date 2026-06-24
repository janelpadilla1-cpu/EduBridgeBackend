<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioRol extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'usuarios_roles';

    protected $keyType = 'string';

    public $incrementing = false;
    public const UPDATED_AT = null;


    protected $fillable = [
        'usuario_id',
        'rol_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(RolUsuario::class, 'rol_id');
    }

}
