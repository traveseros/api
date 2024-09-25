<?php

namespace App\Repository;



use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function persistAndFlush(object $entity): void
    {
        $this->persist($entity);
        $this->flush();
    }

    public function getReference(mixed $id): mixed
    {
        return $this->getEntityManager()->getReference($this->getClassName(), $id);
    }

    public function beginTransaction(): void
    {
        if (true === $this->getEntityManager()->getConnection()->isTransactionActive()) {
            return;
        }

        $this->getEntityManager()->beginTransaction();
    }

    public function commit(): void
    {
        if (false === $this->getEntityManager()->getConnection()->isTransactionActive()) {
            return;
        }

        $this->getEntityManager()->commit();
    }

    public function rollback(): void
    {
        if (false === $this->getEntityManager()->getConnection()->isTransactionActive()) {
            return;
        }

        $this->getEntityManager()->rollback();
    }

}