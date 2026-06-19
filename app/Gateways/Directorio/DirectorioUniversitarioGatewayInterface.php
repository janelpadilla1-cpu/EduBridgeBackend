<?php

namespace App\Gateways\Directorio;

interface DirectorioUniversitarioGatewayInterface
{
    /**
     * Retorna datos básicos del usuario universitario validado.
     *
     * @return array{external_user_ref:string,codigo_universitario:?string,correo_institucional:string,nombre_completo:string}
     */
    public function validarUsuarioUniversitario(string $externalUserRef, ?string $correoInstitucional = null): array;
}
