<?php

namespace App\Controller;

use App\Repository\DelayedOrdersRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1", name="api_")
 */
class DelayedOrdersController extends BaseController
{
    private DelayedOrdersRepository $delayedOrdersRepository;

    /**
     * @param DelayedOrdersRepository $delayedOrdersRepository
     */
    public function __construct(DelayedOrdersRepository $delayedOrdersRepository)
    {

        $this->delayedOrdersRepository = $delayedOrdersRepository;
    }

    #[Route('/delayed-orders', name: 'app_delayed_orders')]
    public function index(): JsonResponse
    {
        $data = [];

        foreach ($this->delayedOrdersRepository->findAll() as $item) {
            $data[] = [
                'id' => $item->getId(),
                'current_system_time' => $item->getCurrentSystemTime(),
                'expected_time_delivery' => $item->getExpectedTimeDelivery(),
                'order_id' => $item->getOrderId(),
            ];
        }

        return $this->setData($data)->response();
    }
}
