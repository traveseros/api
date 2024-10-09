<?php

namespace App\Repository;

use App\Entity\Control;
use App\Entity\CortaTrack;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ?Control findOneBy(array $criteria, ?array $orderBy = null)
 * @method array<Control> findBy(array $criteria, ?array $orderBy = null)
 * @method void persist(Control $entity)
 * @method void persistAndFlush(Control $entity)
 * @method void remove(Control $entity)
 */
class ControlRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Control::class);
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('control')
            ->delete()
            ->getQuery()
            ->execute();

        $this->getEntityManager()->getConnection()->exec('ALTER TABLE control AUTO_INCREMENT = 1');
    }

    public function getTrack(): string
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT xml FROM track_raw WHERE id = 4');
        $statement->executeStatement();
        return ($statement->executeQuery()->fetchAssociative())['xml'] ?? '';
    }

    public function getAllSorted(): array
    {
        return $this->createQueryBuilder('control')
            ->orderBy('control.controlId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}