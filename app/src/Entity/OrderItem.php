<?php
namespace App\Entity;


use App\Models\Product;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_item')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $order_item_id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: "order_id", referencedColumnName: "order_id", nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "product_id", nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'json')]
    private array $selectedAttributes;

    public function getId(): int
    {
        return $this->$order_item_id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getSelectedAttributes(): array
    {
        return $this->selectedAttributes;
    }

    public function setSelectedAttributes(array $selectedAttributes): void
    {
        $this->selectedAttributes = $selectedAttributes;
    }
}
