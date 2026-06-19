<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RolUsuario extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'roles_usuario';

    protected $keyType = 'string';

    public $incrementing = false;
    public const UPDATED_AT = null;


    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $casts = [
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'rol_id', 'usuario_id');
    }

}
