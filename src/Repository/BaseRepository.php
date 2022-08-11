<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * @var object|null
     */
    private ?object $object;
    /**
     * @var int
     */
    protected int $customId;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->determineEntity());
    }

    /**
     * get the entity name, and we named it here determineEntity because there is already getEntityName function
     * @return mixed
     */
    public abstract function determineEntity();

    /**
     * @param object|null $entity
     * @param bool $flush
     * @return void
     */
    public function add(?object $entity = null, bool $flush = false): void
    {
        $object = $this->getObject();
        $flush = true;

        if (isset($entity)) {
            $object = $entity;
            $flush = false;
        }

        $this->getEntityManager()->persist($object);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param object|null $entity
     * @param bool $flush
     * @return void
     */
    public function edit(?object $entity = null, bool $flush = false): void
    {
        $object = $this->getObject();
        $flush = true;

        if (isset($entity)) {
            $object = $entity;
            $flush = false;
        }

        $this->getEntityManager()->persist($object);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return object
     */
    public function getObject(): object
    {
        return $this->object;
    }

    /**
     * @param object $object
     */
    public function setObject(object $object): void
    {
        $this->object = $object;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function withId(int $id)
    {
        $this->customId = $id;

        return $this;
    }
}