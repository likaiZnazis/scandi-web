<?php
namespace App\Models;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Capacity;
use App\Entity\Other;
use App\Entity\Item;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'attribute')]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['size' => Size::class, 'color' => Color::class, 'capacity' => Capacity::class, 'other' => Other::class])]
class Attribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $attribute_id;

    #[ORM\Column(type: 'string')]
    protected $id;
    
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "attributes")]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "product_id")]
    protected Product $product;

    
    #[ORM\Column(type: "string")]
    protected $name;

    #[ORM\Column(type: "string")]
    protected $type;

    /** @var Collection<int, Item> */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: "attribute")]
    protected Collection $items;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
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
