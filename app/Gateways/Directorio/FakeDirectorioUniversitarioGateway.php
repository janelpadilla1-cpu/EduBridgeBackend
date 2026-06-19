<?php

namespace App\Gateways\Directorio;

use App\Exceptions\BusinessRuleException;
use Illuminate\Support\Str;

class FakeDirectorioUniversitarioGateway implements DirectorioUniversitarioGatewayInterface
{
    public function validarUsuarioUniversitario(string $externalUserRef, ?string $correoInstitucional = null): array
    {
        $allowedDomain = config('services.directorio.allowed_email_domain', 'edu.bo');
        $correo = $correoInstitucional ?: $externalUserRef;

        if (! str_contains($correo, '@')) {
            $correo = Str::slug($externalUserRef)."@{$allowedDomain}";
        }

        if (! str_ends_with(Str::lower($correo), '@'.Str::lower($allowedDomain))) {
            throw new BusinessRuleException('El correo institucional no pertenece al dominio universitario permitido.');
        }

        return [
            'external_user_ref' => $externalUserRef,
            'codigo_universitario' => Str::upper(Str::before($externalUserRef, '@')),
            'correo_institucional' => $correo,
            'nombre_completo' => 'Usuario Universitario '.Str::upper(Str::substr(md5($externalUserRef), 0, 6)),
        ];
    }
}
