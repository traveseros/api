<?php

namespace App\Repository;

use App\Entity\Tramo;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ?Tramo findOneBy(array $criteria, ?array $orderBy = null)
 * @method array<Tramo> findBy(array $criteria, ?array $orderBy = null)
 * @method void persist(Tramo $entity)
 * @method void persistAndFlush(Tramo $entity)
 * @method void remove(Tramo $entity)
 */
class TramoRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tramo::class);
    }

    public function findNotLazyAll(){
        $qb = $this->createQueryBuilder('t');
        $qb->select('t', 'e')
            ->join('t.equipo', 'e');
        return $qb->getQuery()->getResult();
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->getQuery()
            ->execute();

        $this->getEntityManager()->getConnection()->exec('ALTER TABLE tramo AUTO_INCREMENT = 1');
    }
}