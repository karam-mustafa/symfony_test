<?php

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController
{
    const SERVER_ERROR = 500;
    const BAD_REQUEST_ERROR = 400;
    const VALIDATION_ERROR = 422;
    const NOT_FOUND_PAGE_ERROR = 422;

    /**
     * @var bool
     */
    public bool $isError = false;
    /**
     * @var string
     */
    public string $message = 'Success Operation';
    /**
     * @var int
     */
    public int $statusCode = 200;
    /**
     * @var mixed
     */
    public mixed $data = [];

    /**
     * validate an entity, and throw exception if there is any validation error
     *
     * @param $obj
     * @param ValidatorInterface $validator
     * @return void
     * @throws \Exception
     */
    #[NoReturn] public function validateEntity($obj, ValidatorInterface $validator)
    {
        $errors = $validator->validate($obj);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            throw new \Exception($errorsString, static::VALIDATION_ERROR);
        }
    }

    /**
     * check if custom entity recorde isset in the database or not.
     *
     * @param $entityClass
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function checkIfEntityExists($manager, $entityClass, $id)
    {
        $order = $manager->getRepository($entityClass)->find($id);
        if (!$order) {
            throw new \Exception('No orders found for id ' . $id, static::NOT_FOUND_PAGE_ERROR);
        }

        return $order;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        if ($this->statusCode == 0) return static::SERVER_ERROR;
        return $this->statusCode;
    }

    /**
     * @param int $code
     * @return BaseController
     */
    public function setStatus(int $code): BaseController
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): BaseController
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function response(): JsonResponse
    {
        $toReturn = [];
        if ($this->getMessage()) {
            $toReturn['message'] = $this->getMessage();
        }

        if ($this->getData()) {
            $toReturn['message'] = $this->getData();
        }
        return $this->json($toReturn, $this->getStatusCode());
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): BaseController
    {
        $this->data = $data;

        return $this;
    }
}