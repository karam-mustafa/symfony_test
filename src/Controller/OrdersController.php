<?php

namespace App\Controller;

use App\Repository\OrderItemRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api", name="api_")
 */
class OrdersController extends BaseController
{
    private EntityManagerInterface $entityManager;
    private OrdersRepository $ordersRepository;
    private OrderItemRepository $orderItemRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param OrdersRepository $ordersRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        OrdersRepository       $ordersRepository,
        OrderItemRepository    $orderItemRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->ordersRepository = $ordersRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * return parameters for put and post requests
     *
     * @param $request
     * @return array
     */
    private function getRequestParameters($request): array
    {
        return [
            'billing_address' => $request->request->get('billing_address'),
            'delivery_time' => $request->request->get('delivery_time'),
            'delivery_address' => $request->request->get('delivery_address'),
            'customer_id' => $request->request->get('customer_id'),
            'items' => $request->request->get('items')
        ];
    }

    /**
     * @param $items
     * @return void
     */
    public function resolveOrderItems($items): void
    {
        foreach ($items as $item) {
            $this->orderItemRepository->fillObject([
                'quantity' => $item['quantity'],
                'order' => $this->ordersRepository->getObject(),
            ])->add();
        }
    }

    /**
     * return response entity body
     *
     * @param object $order
     * @return array
     */
    private function getDataFromEntity(object $order): array
    {
        $data = [
            'id' => $order->getId(),
            'billing_address' => $order->getBillingAddress(),
            'delivery_address' => $order->getDeliveryAddress(),
            'delivery_time' => $order->getDeliveryTime(),
            'customer_id' => $order->getCustomerId(),
            'items' => [],
        ];

        foreach ($order->getOrderItems() as $item) {
            $data['items'][] = [
                'id' => $item->getId(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return $data;
    }

    #[Route('/orders', name: 'orders_index', methods: ['GET'])]
    public function index(): Response
    {
        $data = [];

        foreach ($this->ordersRepository->findAll() as $order) {
            $data[] = $this->getDataFromEntity($order);
        }

        return $this->setData($data)->response();
    }

    #[Route('/orders', name: 'orders_new', methods: ['POST'])]
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        try {
            $this->ordersRepository->fillObject($this->getRequestParameters($request))
                ->validateEntity($this->ordersRepository->getObject(), $validator)
                ->add();

            $this->resolveOrderItems($this->getRequestParameters($request)['items']);

            return $this->setMessage('Created new orders successfully with id ' . $this->ordersRepository->getObject()->getId())->response();

        } catch (\Exception $e) {
            return $this->setMessage($e->getMessage())->setStatus($e->getCode())->response();
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/orders/{id}', name: 'orders_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        try {
            $order = $this->checkIfEntityExists($this->entityManager, $this->ordersRepository->determineEntity(), $id);

            return $this->setData($this->getDataFromEntity($order))->response();

        } catch (\Exception $e) {
            return $this->setMessage($e->getMessage())->setStatus($e->getCode())->response();
        }
    }

    #[Route('/orders/{id}', name: 'orders_edit', methods: ['PUT'])]
    public function edit(Request $request, ValidatorInterface $validator, int $id): Response
    {
        try {

            $this->checkIfEntityExists($this->entityManager, $this->ordersRepository->determineEntity(), $id);

            $this->ordersRepository->withId($id)->fillObject($this->getRequestParameters($request))
                ->validateEntity($this->ordersRepository->getObject(), $validator)
                ->add();

            $order = $this->ordersRepository->getObject();

            $this->resolveOrderItems($this->getRequestParameters($request)['items']);

            return $this->setData($this->getDataFromEntity($order))->response();

        } catch (\Exception $e) {
            return $this->setMessage($e->getMessage())->setStatus($e->getCode())->response();
        }
    }

    #[Route('/orders/{id}', name: 'orders_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->entityManager->remove($this->checkIfEntityExists($this->entityManager, $this->ordersRepository->determineEntity(), $id));
            $this->entityManager->flush();

            return $this->setMessage('Deleted a orders successfully with id ' . $id)->response();
        } catch (\Exception $e) {
            return $this->setMessage($e->getMessage())->setStatus($e->getCode())->response();
        }
    }


}