<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'order')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $order_id;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $total_price;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function getTotalPrice(): float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): void
    {
        $this->total_price = $total_price;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }
    }

    public function removeItem(OrderItem $item): void
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }
    }
}
