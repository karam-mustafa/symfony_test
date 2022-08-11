<?php

namespace App\Repository;

use App\Helpers\StatusCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRepository extends ServiceEntityRepository
{

    use StatusCode;
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

    /**
     * validate an entity, and throw exception if there is any validation error
     *
     * @param $obj
     * @param ValidatorInterface $validator
     * @return BaseRepository
     * @throws \Exception
     */
    public function validateEntity($obj, ValidatorInterface $validator): BaseRepository
    {
        $errors = $validator->validate($obj);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            throw new \Exception($errorsString, static::$VALIDATION_ERROR);
        }

        return $this;
    }
}