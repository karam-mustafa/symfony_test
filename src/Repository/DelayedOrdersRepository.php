<?php

namespace App\Repository;

use App\Entity\DelayedOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DelayedOrders>
 *
 * @method DelayedOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method DelayedOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method DelayedOrders[]    findAll()
 * @method DelayedOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DelayedOrdersRepository extends BaseRepository
{

    /**
     * @return array
     */
    public function getIds(): array
    {
        $queryBuilder = $this->createQueryBuilder('t')->select('t.id');

        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as &$result){
            $result = $result['id'];
        }

        return $results;
    }

    /**
     * check if the object is not for custom id, and create new entity object instance
     *
     * @param array $data
     * @param array $only
     * @return DelayedOrdersRepository
     */
    public function fillObject(array $data = [], array $only = []): DelayedOrdersRepository
    {
        $order = new DelayedOrders();

        if (!sizeof($only)) {
            $order->setCurrentSystemTime($data['current_system_time']);
            $order->setExpectedTimeDelivery($data['expected_time_delivery']);
            $order->setOrderId($data['order_id']);
        }

        foreach ($only as $item) {
            $order->{$item['func']}($item['value']);
        }

        $this->setObject($order);

        return $this;
    }
    public function determineEntity(): string
    {
        return DelayedOrders::class;
    }
}
