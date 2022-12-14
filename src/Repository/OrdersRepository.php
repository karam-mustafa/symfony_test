<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 *
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends BaseRepository
{

    /**
     * @return string
     */
    public function determineEntity(): string
    {
        return Orders::class;
    }

    /**
     * check if the object is not for custom id, and create new entity object instance
     *
     * @param array $data
     * @return $this
     */
    public function fillObject(array $data = [], $only = []): OrdersRepository
    {
        $order = new Orders();

        if (isset($this->customId)) {
            $order = $this->getEntityManager()->find($this->determineEntity(), $this->customId);
        }
        if (!sizeof($only)) {
            $order->setBillingAddress($data['billing_address']);
            $order->setDeliveryAddress($data['delivery_address']);
            $order->setDeliveryTime($data['delivery_time']);
            $order->setCustomerId($data['customer_id'] ?? -1);
            $order->setStatus(Orders::PENDING_STATUS);
        }
        foreach ($only as $item) {
            $order->{$item['func']}($item['value']);
        }

        $this->setObject($order);

        return $this;
    }

    /**
     * @param array $exceptIds
     * @return mixed
     */
    public function findAllDataGreaterThanCurrentTime(array $exceptIds = []): mixed
    {
        $queryBuilder = $this->createQueryBuilder('orders');

        $queryBuilder->where('orders.deliveryTime < CURRENT_DATE()')
            ->andWhere('orders.id not in (:ids)')->setParameter('ids', $exceptIds);

        return $queryBuilder->getQuery()->getResult();
    }
}
