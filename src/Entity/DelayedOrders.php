<?php

namespace App\Entity;

use App\Repository\DelayedOrdersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelayedOrdersRepository::class)]
class DelayedOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orderId = null;

    #[ORM\Column(length: 255)]
    private ?string $currentSystemTime = null;

    #[ORM\Column(length: 255)]
    private ?string $expectedTimeDelivery = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $OrderId): self
    {
        $this->orderId = $OrderId;

        return $this;
    }

    public function getCurrentSystemTime(): ?string
    {
        return $this->currentSystemTime;
    }

    public function setCurrentSystemTime(string $currentTime): self
    {
        $this->currentSystemTime = $currentTime;

        return $this;
    }

    public function getExpectedTimeDelivery(): ?string
    {
        return $this->expectedTimeDelivery;
    }

    public function setExpectedTimeDelivery(string $expectedTimeDelivery): self
    {
        $this->expectedTimeDelivery = $expectedTimeDelivery;

        return $this;
    }
}
