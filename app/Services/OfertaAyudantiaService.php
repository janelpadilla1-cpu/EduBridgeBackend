<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Repositories\Contracts\OfertaAyudantiaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OfertaAyudantiaService extends BaseCrudService
{
    public function __construct(OfertaAyudantiaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function publicar(string $id): Model
    {
        return $this->cambiarEstado($id, ['BORRADOR'], 'PUBLICADA');
    }

    public function cerrar(string $id): Model
    {
        return $this->cambiarEstado($id, ['PUBLICADA'], 'CERRADA');
    }

    public function cancelar(string $id): Model
    {
        return $this->cambiarEstado($id, ['BORRADOR', 'PUBLICADA'], 'CANCELADA');
    }

    private function cambiarEstado(string $id, array $estadosPermitidos, string $nuevoEstado): Model
    {
        return DB::transaction(function () use ($id, $estadosPermitidos, $nuevoEstado): Model {
            $oferta = $this->repository->findOrFail($id);

            if (! in_array($oferta->estado, $estadosPermitidos, true)) {
                throw new BusinessRuleException("No se puede cambiar la oferta desde estado {$oferta->estado} a {$nuevoEstado}.");
            }

            return $this->repository->update($oferta, ['estado' => $nuevoEstado]);
        });
    }
}
