<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\InscripcionAyudantia;
use App\Models\SesionAyudantia;
use App\Repositories\Contracts\InscripcionAyudantiaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InscripcionAyudantiaService extends BaseCrudService
{
    public function __construct(InscripcionAyudantiaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return $this->inscribirUsuario($data['usuario_id'], $data['sesion_ayudantia_id']);
    }

    public function inscribirUsuario(string $usuarioId, string $sesionId): InscripcionAyudantia
    {
        return DB::transaction(function () use ($usuarioId, $sesionId): InscripcionAyudantia {
            $sesion = SesionAyudantia::query()
                ->with('ofertaAyudantia')
                ->lockForUpdate()
                ->findOrFail($sesionId);

            if ($sesion->estado !== 'PROGRAMADA') {
                throw new BusinessRuleException('Solo se permite inscripción a sesiones programadas.');
            }

            $existente = InscripcionAyudantia::query()
                ->where('usuario_id', $usuarioId)
                ->where('sesion_ayudantia_id', $sesionId)
                ->first();

            if ($existente && in_array($existente->estado, ['INSCRITO', 'EN_ESPERA', 'ASISTIO', 'NO_ASISTIO'], true)) {
                throw new BusinessRuleException('El usuario ya tiene una inscripción activa para esta sesión.');
            }

            $inscritosActuales = InscripcionAyudantia::query()
                ->where('sesion_ayudantia_id', $sesionId)
                ->where('estado', 'INSCRITO')
                ->lockForUpdate()
                ->count();

            $estado = $inscritosActuales < $sesion->ofertaAyudantia->cupo_maximo ? 'INSCRITO' : 'EN_ESPERA';

            if ($existente) {
                $existente->update([
                    'estado' => $estado,
                    'fecha_inscripcion' => now(),
                    'asistencia' => null,
                ]);

                return $existente->refresh();
            }

            return InscripcionAyudantia::query()->create([
                'usuario_id' => $usuarioId,
                'sesion_ayudantia_id' => $sesionId,
                'estado' => $estado,
                'fecha_inscripcion' => now(),
            ]);
        });
    }

    public function cancelar(string $id): InscripcionAyudantia
    {
        return DB::transaction(function () use ($id): InscripcionAyudantia {
            /** @var InscripcionAyudantia $inscripcion */
            $inscripcion = $this->repository->findOrFail($id);

            if (! in_array($inscripcion->estado, ['INSCRITO', 'EN_ESPERA'], true)) {
                throw new BusinessRuleException('Solo se pueden cancelar inscripciones activas o en espera.');
            }

            $inscripcion->update(['estado' => 'CANCELADO']);

            return $inscripcion->refresh();
        });
    }

    public function registrarAsistencia(string $id, bool $asistencia): InscripcionAyudantia
    {
        return DB::transaction(function () use ($id, $asistencia): InscripcionAyudantia {
            /** @var InscripcionAyudantia $inscripcion */
            $inscripcion = $this->repository->findOrFail($id);

            if ($inscripcion->estado !== 'INSCRITO') {
                throw new BusinessRuleException('Solo se puede registrar asistencia de una inscripción confirmada.');
            }

            $inscripcion->update([
                'asistencia' => $asistencia,
                'estado' => $asistencia ? 'ASISTIO' : 'NO_ASISTIO',
            ]);

            return $inscripcion->refresh();
        });
    }
}
