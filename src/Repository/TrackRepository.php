<?php

namespace App\Repository;

use App\Entity\Track;
use App\Entity\TravesiaType;
use Doctrine\Persistence\ManagerRegistry;

class TrackRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('corta_track')
            ->delete()
            ->getQuery()
            ->execute();

        $this->getEntityManager()->getConnection()->exec('ALTER TABLE corta_track AUTO_INCREMENT = 1');
    }

    public function getTrack(TravesiaType $type): string
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT xml FROM track_raw WHERE id = ' . $type->value);
        $statement->executeStatement();
        return ($statement->executeQuery()->fetchAssociative())['xml'] ?? '';
    }

    public function findOnePoint(int $distance): ?Track
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.accumulated >= :distance')
            ->setParameter('distance', $distance)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}