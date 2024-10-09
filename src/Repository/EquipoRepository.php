<?php

namespace App\Repository;

use App\Entity\Equipo;
use App\Entity\Tramo;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ?Equipo findOneBy(array $criteria, ?array $orderBy = null)
 * @method array<Equipo> findBy(array $criteria, ?array $orderBy = null)
 * @method void persist(Equipo $entity)
 * @method void persistAndFlush(Equipo $entity)
 * @method void remove(Equipo $entity)
 */
class EquipoRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipo::class);
    }

    public function findNotLazyAll(){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e', 't')
            ->join('e.equipo_tramo', 't');
        return $qb->getQuery()->getResult();
    }
}