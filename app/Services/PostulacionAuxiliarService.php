<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\AuxiliarMateria;
use App\Models\PostulacionAuxiliar;
use App\Models\RolUsuario;
use App\Repositories\Contracts\PostulacionAuxiliarRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostulacionAuxiliarService extends BaseCrudService
{
    public function __construct(PostulacionAuxiliarRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $pendiente = PostulacionAuxiliar::query()
                ->where('usuario_id', $data['usuario_id'])
                ->where('materia_id', $data['materia_id'])
                ->where('estado', 'PENDIENTE')
                ->exists();

            if ($pendiente) {
                throw new BusinessRuleException('Ya existe una postulación pendiente para este usuario y materia.');
            }

            $data['estado'] = 'PENDIENTE';
            $data['fecha_postulacion'] = now();

            return $this->repository->create($data);
        });
    }

    public function aprobar(string $id): PostulacionAuxiliar
    {
        return DB::transaction(function () use ($id): PostulacionAuxiliar {
            /** @var PostulacionAuxiliar $postulacion */
            $postulacion = $this->repository->findOrFail($id);

            if ($postulacion->estado !== 'PENDIENTE') {
                throw new BusinessRuleException('Solo se pueden aprobar postulaciones pendientes.');
            }

            $postulacion->update(['estado' => 'APROBADA']);

            AuxiliarMateria::query()->firstOrCreate(
                [
                    'usuario_id' => $postulacion->usuario_id,
                    'materia_id' => $postulacion->materia_id,
                ],
                [
                    'estado' => 'ACTIVO',
                    'fecha_asignacion' => now(),
                ]
            );

            $rolAuxiliar = RolUsuario::query()->firstOrCreate(
                ['nombre' => 'AUXILIAR'],
                ['descripcion' => 'Usuario que puede dictar sesiones de ayudantía']
            );

            $postulacion->usuario->roles()->syncWithoutDetaching([
                $rolAuxiliar->id => ['id' => (string) Str::uuid()],
            ]);

            return $postulacion->refresh();
        });
    }

    public function rechazar(string $id): PostulacionAuxiliar
    {
        return $this->cambiarEstadoPendiente($id, 'RECHAZADA');
    }

    public function cancelar(string $id): PostulacionAuxiliar
    {
        return $this->cambiarEstadoPendiente($id, 'CANCELADA');
    }

    private function cambiarEstadoPendiente(string $id, string $nuevoEstado): PostulacionAuxiliar
    {
        return DB::transaction(function () use ($id, $nuevoEstado): PostulacionAuxiliar {
            /** @var PostulacionAuxiliar $postulacion */
            $postulacion = $this->repository->findOrFail($id);

            if ($postulacion->estado !== 'PENDIENTE') {
                throw new BusinessRuleException('Solo se pueden cambiar postulaciones pendientes.');
            }

            $postulacion->update(['estado' => $nuevoEstado]);

            return $postulacion->refresh();
        });
    }
}
