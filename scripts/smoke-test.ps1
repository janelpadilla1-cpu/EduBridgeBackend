param(
    [string]$BaseUrl = "http://127.0.0.1:8000"
)

$ErrorActionPreference = "Stop"
$script:Token = ""

function Write-Step($Message) {
    Write-Host "[SMOKE] $Message" -ForegroundColor Cyan
}

function Invoke-Api {
    param(
        [string]$Method,
        [string]$Path,
        [object]$Body = $null,
        [int]$Expected = 200,
        [string]$Label = $Path,
        [bool]$Auth = $true
    )

    $headers = @{ Accept = "application/json" }
    if ($Auth -and $script:Token) { $headers.Authorization = "Bearer $script:Token" }

    $uri = "$BaseUrl$Path"
    $params = @{ Method = $Method; Uri = $uri; Headers = $headers }

    if ($null -ne $Body) {
        $headers["Content-Type"] = "application/json"
        $params.Body = ($Body | ConvertTo-Json -Depth 20)
    }

    try {
        $response = Invoke-WebRequest @params
    } catch {
        $statusCode = $null
        $content = ""
        if ($_.Exception.Response) {
            $statusCode = [int]$_.Exception.Response.StatusCode
            try {
                $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $content = $reader.ReadToEnd()
            } catch { $content = $_.Exception.Message }
        }
        throw "[$Label] falló. Esperado HTTP $Expected, recibido HTTP $statusCode. Body: $content"
    }

    if ([int]$response.StatusCode -ne $Expected) {
        throw "[$Label] falló. Esperado HTTP $Expected, recibido HTTP $($response.StatusCode). Body: $($response.Content)"
    }

    if ([string]::IsNullOrWhiteSpace($response.Content)) { return $null }
    return $response.Content | ConvertFrom-Json
}

function Get-DataId($Json, [string]$Label) {
    $id = $null
    if ($Json -and $Json.data -and $Json.data.id) { $id = $Json.data.id }
    elseif ($Json -and $Json.id) { $id = $Json.id }
    if (-not $id) { throw "[$Label] no devolvió data.id/id" }
    return $id
}

$runId = [DateTimeOffset]::UtcNow.ToUnixTimeMilliseconds().ToString()
$password = "Password123!"
$adminEmail = "admin.$runId@edu.bo"
$createdUserEmail = "estudiante.$runId@edu.bo"

Write-Step "API: $BaseUrl"
Write-Step "Run ID: $runId"

Write-Step "00 Auth y catálogos"
Invoke-Api GET "/api/v1/catalogos/estados" $null 200 "Catálogos" $false | Out-Null
$register = Invoke-Api POST "/api/v1/auth/register" @{
    correo_institucional = $adminEmail
    nombre_completo = "Administrador Smoke $runId"
    rol = "ADMINISTRADOR"
    password = $password
    password_confirmation = $password
} 201 "Auth Register" $false
$script:Token = $register.access_token
if (-not $script:Token) { throw "Auth Register no devolvió access_token" }
$userId = $register.usuario.id
Invoke-Api GET "/api/v1/auth/me" $null 200 "Auth Me" | Out-Null
$login = Invoke-Api POST "/api/v1/auth/login" @{ correo_institucional = $adminEmail; password = $password } 200 "Auth Login" $false
$script:Token = $login.access_token

Write-Step "01 Usuarios, cuentas, roles y pivote"
Invoke-Api GET "/api/v1/usuarios?per_page=5&sort_by=created_at&sort_order=desc" $null 200 "Usuarios Index" | Out-Null
$createdUser = Invoke-Api POST "/api/v1/usuarios" @{
    external_user_ref = "local-estudiante-$runId"
    codigo_universitario = "EST-$runId"
    correo_institucional = $createdUserEmail
    nombre_completo = "Estudiante CRUD $runId"
    estado = "ACTIVO"
} 201 "Usuarios Store"
$createdUserId = Get-DataId $createdUser "Usuarios Store"
Invoke-Api GET "/api/v1/usuarios/$createdUserId" $null 200 "Usuarios Show" | Out-Null
Invoke-Api PATCH "/api/v1/usuarios/$createdUserId" @{ nombre_completo = "Estudiante PATCH $runId"; estado = "ACTIVO" } 200 "Usuarios PATCH" | Out-Null
Invoke-Api PUT "/api/v1/usuarios/$createdUserId" @{
    external_user_ref = "local-estudiante-$runId"
    codigo_universitario = "EST-$runId"
    correo_institucional = $createdUserEmail
    nombre_completo = "Estudiante PUT $runId"
    estado = "ACTIVO"
} 200 "Usuarios PUT" | Out-Null

Invoke-Api GET "/api/v1/cuentas-usuario?per_page=5&sort_by=created_at&sort_order=desc" $null 200 "Cuentas Index" | Out-Null
$cuenta = Invoke-Api POST "/api/v1/cuentas-usuario" @{
    usuario_id = $createdUserId
    password_hash = '$2y$12$Hc6gP0zZQzQkgjZ6bgGgku6e1ztuACNQdA0nhx1V1i7iFNeJ3JiU2'
    estado = "ACTIVA"
    ultimo_acceso = "2030-01-01 08:00:00"
} 201 "Cuentas Store"
$cuentaId = Get-DataId $cuenta "Cuentas Store"
Invoke-Api GET "/api/v1/cuentas-usuario/$cuentaId" $null 200 "Cuentas Show" | Out-Null
Invoke-Api PATCH "/api/v1/cuentas-usuario/$cuentaId" @{ estado = "BLOQUEADA"; ultimo_acceso = "2030-01-02 09:00:00" } 200 "Cuentas PATCH" | Out-Null
Invoke-Api PUT "/api/v1/cuentas-usuario/$cuentaId" @{
    usuario_id = $createdUserId
    password_hash = '$2y$12$Hc6gP0zZQzQkgjZ6bgGgku6e1ztuACNQdA0nhx1V1i7iFNeJ3JiU2'
    estado = "ACTIVA"
    ultimo_acceso = "2030-01-03 10:00:00"
} 200 "Cuentas PUT" | Out-Null

Invoke-Api GET "/api/v1/roles-usuario?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Roles Index" | Out-Null
$rol = Invoke-Api POST "/api/v1/roles-usuario" @{ nombre = "MONITOR_SMOKE_$runId"; descripcion = "Rol temporal smoke" } 201 "Roles Store"
$rolId = Get-DataId $rol "Roles Store"
Invoke-Api GET "/api/v1/roles-usuario/$rolId" $null 200 "Roles Show" | Out-Null
Invoke-Api PATCH "/api/v1/roles-usuario/$rolId" @{ descripcion = "Rol PATCH" } 200 "Roles PATCH" | Out-Null
Invoke-Api PUT "/api/v1/roles-usuario/$rolId" @{ nombre = "MONITOR_SMOKE_$runId"; descripcion = "Rol PUT" } 200 "Roles PUT" | Out-Null

Invoke-Api GET "/api/v1/usuarios-roles?per_page=5&sort_by=created_at&sort_order=desc" $null 200 "UsuariosRoles Index" | Out-Null
$usuarioRol = Invoke-Api POST "/api/v1/usuarios-roles" @{ usuario_id = $createdUserId; rol_id = $rolId } 201 "UsuariosRoles Store"
$usuarioRolId = Get-DataId $usuarioRol "UsuariosRoles Store"
Invoke-Api GET "/api/v1/usuarios-roles/$usuarioRolId" $null 200 "UsuariosRoles Show" | Out-Null
Invoke-Api PATCH "/api/v1/usuarios-roles/$usuarioRolId" @{ usuario_id = $createdUserId; rol_id = $rolId } 200 "UsuariosRoles PATCH" | Out-Null
Invoke-Api PUT "/api/v1/usuarios-roles/$usuarioRolId" @{ usuario_id = $createdUserId; rol_id = $rolId } 200 "UsuariosRoles PUT" | Out-Null

function New-Materia([string]$Codigo, [string]$Nombre) {
    $json = Invoke-Api POST "/api/v1/materias" @{ codigo = $Codigo; nombre = $Nombre; descripcion = "Descripción de $Nombre"; estado = "ACTIVA" } 201 "Materias Store $Codigo"
    return Get-DataId $json "Materias Store $Codigo"
}
function New-Oferta([string]$MateriaId, [string]$Titulo, [int]$Cupo) {
    $json = Invoke-Api POST "/api/v1/ofertas-ayudantia" @{ materia_id = $MateriaId; titulo = $Titulo; descripcion = "Descripción de $Titulo"; cupo_maximo = $Cupo; estado = "BORRADOR" } 201 "Ofertas Store $Titulo"
    return Get-DataId $json "Ofertas Store $Titulo"
}
function New-Sesion([string]$OfertaId, [string]$Aula, [string]$Inicio, [string]$Fin) {
    $json = Invoke-Api POST "/api/v1/sesiones-ayudantia" @{ oferta_ayudantia_id = $OfertaId; auxiliar_id = $userId; fecha = "2030-08-15"; hora_inicio = $Inicio; hora_fin = $Fin; aula_ref_id = $Aula; aula_nombre_cache = "Aula $Aula"; estado = "PROGRAMADA" } 201 "Sesiones Store $Aula"
    return Get-DataId $json "Sesiones Store $Aula"
}
function New-Inscripcion([string]$UsuarioId, [string]$SesionId, [string]$Label) {
    $json = Invoke-Api POST "/api/v1/inscripciones-ayudantia" @{ usuario_id = $UsuarioId; sesion_ayudantia_id = $SesionId } 201 "Inscripciones Store $Label"
    return Get-DataId $json "Inscripciones Store $Label"
}
function New-Postulacion([string]$UsuarioId, [string]$MateriaId, [string]$Motivo) {
    $json = Invoke-Api POST "/api/v1/postulaciones-auxiliar" @{ usuario_id = $UsuarioId; materia_id = $MateriaId; motivo = $Motivo; experiencia = "Experiencia realista de prueba" } 201 "Postulaciones Store $Motivo"
    return Get-DataId $json "Postulaciones Store $Motivo"
}

Write-Step "02 Materias y ofertas"
Invoke-Api GET "/api/v1/materias?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Materias Index" | Out-Null
$materiaId = New-Materia "MAT-$runId" "Contabilidad I Smoke"
$materiaAprobarId = New-Materia "MAT-APR-$runId" "Costos Smoke"
$materiaRechazarId = New-Materia "MAT-REJ-$runId" "Auditoría Smoke"
$materiaCancelarId = New-Materia "MAT-CAN-$runId" "Tributaria Smoke"
$materiaAuxManualId = New-Materia "MAT-AUX-$runId" "Finanzas Smoke"
$materiaDeleteId = New-Materia "MAT-DEL-$runId" "Materia Delete Smoke"
Invoke-Api GET "/api/v1/materias/$materiaId" $null 200 "Materias Show" | Out-Null
Invoke-Api PATCH "/api/v1/materias/$materiaId" @{ nombre = "Contabilidad PATCH"; descripcion = "Patch real"; estado = "ACTIVA" } 200 "Materias PATCH" | Out-Null
Invoke-Api PUT "/api/v1/materias/$materiaId" @{ codigo = "MAT-$runId"; nombre = "Contabilidad PUT"; descripcion = "Put real"; estado = "ACTIVA" } 200 "Materias PUT" | Out-Null

Invoke-Api GET "/api/v1/ofertas-ayudantia?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Ofertas Index" | Out-Null
$ofertaId = New-Oferta $materiaId "Oferta principal Smoke" 6
$ofertaCancelId = New-Oferta $materiaId "Oferta cancelable Smoke" 3
$ofertaDeleteId = New-Oferta $materiaDeleteId "Oferta delete Smoke" 1
Invoke-Api GET "/api/v1/ofertas-ayudantia/$ofertaId" $null 200 "Ofertas Show" | Out-Null
Invoke-Api PATCH "/api/v1/ofertas-ayudantia/$ofertaId" @{ titulo = "Oferta PATCH"; descripcion = "Patch oferta"; cupo_maximo = 7; estado = "BORRADOR" } 200 "Ofertas PATCH" | Out-Null
Invoke-Api PUT "/api/v1/ofertas-ayudantia/$ofertaId" @{ materia_id = $materiaId; titulo = "Oferta PUT"; descripcion = "Put oferta"; cupo_maximo = 7; estado = "BORRADOR" } 200 "Ofertas PUT" | Out-Null
Invoke-Api POST "/api/v1/ofertas-ayudantia/$ofertaId/publicar" @{ motivo_operacion = "Smoke publicar" } 200 "Ofertas Publicar" | Out-Null
Invoke-Api POST "/api/v1/ofertas-ayudantia/$ofertaId/cerrar" @{ motivo_operacion = "Smoke cerrar" } 200 "Ofertas Cerrar" | Out-Null
Invoke-Api POST "/api/v1/ofertas-ayudantia/$ofertaCancelId/cancelar" @{ motivo_operacion = "Smoke cancelar" } 200 "Ofertas Cancelar" | Out-Null

Write-Step "03 Sesiones e inscripciones"
Invoke-Api GET "/api/v1/sesiones-ayudantia?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Sesiones Index" | Out-Null
$sesionId = New-Sesion $ofertaId "AULA-101-$runId" "08:00" "09:00"
$sesionCancelId = New-Sesion $ofertaId "AULA-102-$runId" "09:15" "10:15"
$sesionDeleteId = New-Sesion $ofertaId "AULA-103-$runId" "10:30" "11:30"
Invoke-Api GET "/api/v1/sesiones-ayudantia/$sesionId" $null 200 "Sesiones Show" | Out-Null
Invoke-Api PATCH "/api/v1/sesiones-ayudantia/$sesionId" @{ aula_nombre_cache = "Aula PATCH"; estado = "PROGRAMADA" } 200 "Sesiones PATCH" | Out-Null
Invoke-Api PUT "/api/v1/sesiones-ayudantia/$sesionId" @{ oferta_ayudantia_id = $ofertaId; auxiliar_id = $userId; fecha = "2030-08-15"; hora_inicio = "08:00"; hora_fin = "09:00"; aula_ref_id = "AULA-101-$runId"; aula_nombre_cache = "Aula PUT"; estado = "PROGRAMADA" } 200 "Sesiones PUT" | Out-Null

Invoke-Api GET "/api/v1/inscripciones-ayudantia?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Inscripciones Index" | Out-Null
$inscripcionId = New-Inscripcion $userId $sesionId "principal"
$inscripcionCancelId = New-Inscripcion $createdUserId $sesionCancelId "cancelar"
$inscripcionDeleteId = New-Inscripcion $createdUserId $sesionDeleteId "delete"
Invoke-Api GET "/api/v1/inscripciones-ayudantia/$inscripcionId" $null 200 "Inscripciones Show" | Out-Null
Invoke-Api PATCH "/api/v1/inscripciones-ayudantia/$inscripcionId" @{ usuario_id = $userId; sesion_ayudantia_id = $sesionId } 200 "Inscripciones PATCH" | Out-Null
Invoke-Api PUT "/api/v1/inscripciones-ayudantia/$inscripcionId" @{ usuario_id = $userId; sesion_ayudantia_id = $sesionId } 200 "Inscripciones PUT" | Out-Null
Invoke-Api PATCH "/api/v1/inscripciones-ayudantia/$inscripcionId/asistencia" @{ asistencia = $true } 200 "Inscripciones Asistencia" | Out-Null
Invoke-Api POST "/api/v1/inscripciones-ayudantia/$inscripcionCancelId/cancelar" @{ motivo_operacion = "Smoke cancelar inscripción" } 200 "Inscripciones Cancelar" | Out-Null
Invoke-Api POST "/api/v1/sesiones-ayudantia/$sesionId/iniciar" @{ motivo_operacion = "Smoke iniciar" } 200 "Sesiones Iniciar" | Out-Null
Invoke-Api POST "/api/v1/sesiones-ayudantia/$sesionId/finalizar" @{ motivo_operacion = "Smoke finalizar" } 200 "Sesiones Finalizar" | Out-Null
Invoke-Api POST "/api/v1/sesiones-ayudantia/$sesionCancelId/cancelar" @{ motivo_operacion = "Smoke cancelar sesión" } 200 "Sesiones Cancelar" | Out-Null

Write-Step "04 Postulaciones, auxiliares y disponibilidad"
Invoke-Api GET "/api/v1/postulaciones-auxiliar?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Postulaciones Index" | Out-Null
$postulacionId = New-Postulacion $userId $materiaAprobarId "Motivo para aprobar"
$postulacionRechazarId = New-Postulacion $userId $materiaRechazarId "Motivo para rechazar"
$postulacionCancelarId = New-Postulacion $userId $materiaCancelarId "Motivo para cancelar"
$postulacionDeleteId = New-Postulacion $createdUserId $materiaDeleteId "Motivo para delete"
Invoke-Api GET "/api/v1/postulaciones-auxiliar/$postulacionId" $null 200 "Postulaciones Show" | Out-Null
Invoke-Api PATCH "/api/v1/postulaciones-auxiliar/$postulacionId" @{ motivo = "Motivo PATCH"; experiencia = "Experiencia PATCH" } 200 "Postulaciones PATCH" | Out-Null
Invoke-Api PUT "/api/v1/postulaciones-auxiliar/$postulacionId" @{ usuario_id = $userId; materia_id = $materiaAprobarId; motivo = "Motivo PUT"; experiencia = "Experiencia PUT" } 200 "Postulaciones PUT" | Out-Null
Invoke-Api POST "/api/v1/postulaciones-auxiliar/$postulacionId/aprobar" @{ motivo_operacion = "Smoke aprobar" } 200 "Postulaciones Aprobar" | Out-Null
Invoke-Api POST "/api/v1/postulaciones-auxiliar/$postulacionRechazarId/rechazar" @{ motivo_operacion = "Smoke rechazar" } 200 "Postulaciones Rechazar" | Out-Null
Invoke-Api POST "/api/v1/postulaciones-auxiliar/$postulacionCancelarId/cancelar" @{ motivo_operacion = "Smoke cancelar postulación" } 200 "Postulaciones Cancelar" | Out-Null

Invoke-Api GET "/api/v1/auxiliares-materia?per_page=100&sort_by=created_at&sort_order=desc" $null 200 "Auxiliares Index" | Out-Null
$aux = Invoke-Api POST "/api/v1/auxiliares-materia" @{ usuario_id = $createdUserId; materia_id = $materiaAuxManualId; estado = "ACTIVO" } 201 "Auxiliares Store"
$auxId = Get-DataId $aux "Auxiliares Store"
Invoke-Api GET "/api/v1/auxiliares-materia/$auxId" $null 200 "Auxiliares Show" | Out-Null
Invoke-Api PATCH "/api/v1/auxiliares-materia/$auxId" @{ estado = "INACTIVO" } 200 "Auxiliares PATCH" | Out-Null
Invoke-Api PUT "/api/v1/auxiliares-materia/$auxId" @{ usuario_id = $createdUserId; materia_id = $materiaAuxManualId; estado = "ACTIVO" } 200 "Auxiliares PUT" | Out-Null

Invoke-Api GET "/api/v1/disponibilidad-auxiliar?per_page=10&sort_by=created_at&sort_order=desc" $null 200 "Disponibilidad Index" | Out-Null
$disp = Invoke-Api POST "/api/v1/disponibilidad-auxiliar" @{ usuario_id = $userId; dia_semana = "LUNES"; hora_inicio = "14:00"; hora_fin = "16:00"; estado = "ACTIVA" } 201 "Disponibilidad Store"
$dispId = Get-DataId $disp "Disponibilidad Store"
Invoke-Api GET "/api/v1/disponibilidad-auxiliar/$dispId" $null 200 "Disponibilidad Show" | Out-Null
Invoke-Api PATCH "/api/v1/disponibilidad-auxiliar/$dispId" @{ estado = "INACTIVA" } 200 "Disponibilidad PATCH" | Out-Null
Invoke-Api PUT "/api/v1/disponibilidad-auxiliar/$dispId" @{ usuario_id = $userId; dia_semana = "LUNES"; hora_inicio = "14:00"; hora_fin = "16:00"; estado = "ACTIVA" } 200 "Disponibilidad PUT" | Out-Null

Write-Step "05 DELETE endpoints y logout"
Invoke-Api DELETE "/api/v1/disponibilidad-auxiliar/$dispId" $null 204 "DELETE Disponibilidad" | Out-Null
Invoke-Api DELETE "/api/v1/auxiliares-materia/$auxId" $null 204 "DELETE Auxiliar" | Out-Null
Invoke-Api DELETE "/api/v1/postulaciones-auxiliar/$postulacionDeleteId" $null 204 "DELETE Postulación" | Out-Null
Invoke-Api DELETE "/api/v1/inscripciones-ayudantia/$inscripcionDeleteId" $null 204 "DELETE Inscripción" | Out-Null
Invoke-Api DELETE "/api/v1/sesiones-ayudantia/$sesionDeleteId" $null 204 "DELETE Sesión" | Out-Null
Invoke-Api DELETE "/api/v1/ofertas-ayudantia/$ofertaDeleteId" $null 204 "DELETE Oferta" | Out-Null
Invoke-Api DELETE "/api/v1/materias/$materiaDeleteId" $null 204 "DELETE Materia" | Out-Null
Invoke-Api DELETE "/api/v1/usuarios-roles/$usuarioRolId" $null 204 "DELETE UsuarioRol" | Out-Null
Invoke-Api DELETE "/api/v1/roles-usuario/$rolId" $null 204 "DELETE Rol" | Out-Null
Invoke-Api DELETE "/api/v1/cuentas-usuario/$cuentaId" $null 204 "DELETE Cuenta" | Out-Null
Invoke-Api DELETE "/api/v1/usuarios/$createdUserId" $null 204 "DELETE Usuario" | Out-Null
Invoke-Api POST "/api/v1/auth/logout" @{ motivo_operacion = "Smoke logout" } 204 "Auth Logout" | Out-Null

Write-Host "`nSMOKE TEST COMPLETO OK" -ForegroundColor Green
Write-Host "Run ID: $runId"
Write-Host "Usuario admin: $adminEmail"
