<?php

namespace App\Controller;

use App\Helpers\StatusCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    use StatusCode;
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
     * check if custom entity recorde isset in the database or not.
     *
     * @param $entityClass
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function checkIfEntityExists($manager, $entityClass, $id): mixed
    {
        $order = $manager->getRepository($entityClass)->find($id);
        if (!$order) {
            throw new \Exception('No orders found for id ' . $id, static::$NOT_FOUND_PAGE_ERROR);
        }

        return $order;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        if ($this->statusCode == 0) return static::$SERVER_ERROR;
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
            $toReturn['data'] = $this->getData();
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