<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Gateways\Aulas\AulaGatewayInterface;
use App\Repositories\Contracts\SesionAyudantiaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SesionAyudantiaService extends BaseCrudService
{
    public function __construct(
        SesionAyudantiaRepositoryInterface $repository,
        private readonly AulaGatewayInterface $aulaGateway,
    ) {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $disponible = $this->aulaGateway->consultarDisponibilidad(
                $data['aula_ref_id'],
                $data['fecha'],
                $data['hora_inicio'],
                $data['hora_fin']
            );

            if (! $disponible) {
                throw new BusinessRuleException('El aula externa no está disponible para el horario solicitado.');
            }

            $reservada = $this->aulaGateway->reservarAula(
                $data['aula_ref_id'],
                $data['fecha'],
                $data['hora_inicio'],
                $data['hora_fin']
            );

            if (! $reservada) {
                throw new BusinessRuleException('No se pudo reservar el aula externa.');
            }

            $data['estado'] = $data['estado'] ?? 'PROGRAMADA';

            return $this->repository->create($data);
        });
    }

    public function iniciar(string $id): Model
    {
        return $this->cambiarEstado($id, ['PROGRAMADA'], 'EN_CURSO');
    }

    public function finalizar(string $id): Model
    {
        return $this->cambiarEstado($id, ['EN_CURSO'], 'FINALIZADA');
    }

    public function cancelar(string $id): Model
    {
        return $this->cambiarEstado($id, ['PROGRAMADA'], 'CANCELADA');
    }

    private function cambiarEstado(string $id, array $estadosPermitidos, string $nuevoEstado): Model
    {
        return DB::transaction(function () use ($id, $estadosPermitidos, $nuevoEstado): Model {
            $sesion = $this->repository->findOrFail($id);

            if (! in_array($sesion->estado, $estadosPermitidos, true)) {
                throw new BusinessRuleException("No se puede cambiar la sesión desde estado {$sesion->estado} a {$nuevoEstado}.");
            }

            return $this->repository->update($sesion, ['estado' => $nuevoEstado]);
        });
    }
}
