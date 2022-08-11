<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<OrderItem>
 *
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[]    findAll()
 * @method OrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function determineEntity(): string
    {
        return OrderItem::class;
    }

    /**
     * check if the object is not for custom id, and create new entity object instance
     *
     * @param array $data
     * @return OrderItemRepository
     */
    public function fillObject(array $data = []): OrderItemRepository
    {
        $item = new OrderItem();

        if (isset($this->customId)) {
            $item = $this->getEntityManager()->find($this->determineEntity(), $this->customId);
        }
        $item->setOrders($data['order']);
        $item->setQuantity($data['quantity'] ?? -1);
        $this->setObject($item);

        return $this;
    }
}
