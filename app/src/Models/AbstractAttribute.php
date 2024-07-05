<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAttribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $attribute_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractProduct", inversedBy="attributes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="attribute", cascade={"persist", "remove"})
     */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    // Getters and setters...

    public function getAttributeId(): int
    {
        return $this->attribute_id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getProduct(): ?AbstractProduct
    {
        return $this->product;
    }

    public function setProduct(?AbstractProduct $product): void
    {
        $this->product = $product;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setAttribute($this);
        }
    }

    public function removeItem(Item $item): void
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            if ($item->getAttribute() === $this) {
                $item->setAttribute(null);
            }
        }
    }
}
