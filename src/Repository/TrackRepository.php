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

    public function deleteAll(TravesiaType $type): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->execute();

        //$this->getEntityManager()->getConnection()->exec('ALTER TABLE track AUTO_INCREMENT = 1');
    }

    public function getTrack(TravesiaType $type): string
    {
        $statement = $this->getEntityManager()->getConnection()->prepare('SELECT xml FROM track_raw WHERE id = ' . $type->value);
        $statement->executeStatement();
        return ($statement->executeQuery()->fetchAssociative())['xml'] ?? '';
    }

    public function updateTrack(string $xml, TravesiaType $type): void
    {
        $oldTrack = $this->getTrack($type);
        $xml = \str_replace('"', "'", $xml);

        if ('' === $oldTrack){
            $statement = $this->getEntityManager()->getConnection()
                ->prepare(
                    'INSERT INTO track_raw (id, type, xml) VALUES ('.$type->value.',"'.$type->value.'","'.$xml.'"'
                );
        }else{
            $statement = $this->getEntityManager()->getConnection()
                ->prepare(
                    'UPDATE track_raw SET xml = "'.$xml.'" WHERE id = ' . $type->value
                );
        }

        $statement->executeStatement();
    }

    public function findOnePoint(int $distance, TravesiaType $type): ?Track
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.accumulated >= :distance')
            ->andHaving('pt.type = :type')
            ->setParameter('distance', $distance)
            ->setParameter($type)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function summarizeTracks()
    {
        return $this->getEntityManager()->getConnection()->executeQuery(
            'SELECT type, COUNT(*) as points, MAX(accumulated) as length 
                 FROM track
                 GROUP BY type'
        )->fetchAllAssociative();
    }

}