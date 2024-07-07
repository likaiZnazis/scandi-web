<?php
namespace App\Models;

use App\Entity\ClothProduct;
use App\Entity\TechProduct;
use App\Entity\Price;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'product')]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['cloth_product' => ClothProduct::class, 'tech_product' => TechProduct::class])]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $product_id;

    #[ORM\Column(type: 'string')]
    protected $id;

    #[ORM\Column(type: 'string')]
    protected $name;

    #[ORM\Column(type: 'boolean')]
    protected $in_stock;

    #[ORM\Column(type: 'json')]
    protected $gallery;

    #[ORM\Column(type: 'text')]
    protected $description;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $brand;

    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'product')]
    protected Collection $prod_prices;

    #[ORM\OneToMany(targetEntity: Attribute::class, mappedBy: 'product')]
    protected Collection $attributes;

    //There can be only one category for a product
    #[ORM\ManyToOne(targetEntity: "Category", inversedBy: "products")]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "category_id")]
    protected Category $category;

    public function __construct()
    {
        $this->prod_prices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }
    // Getters and setters...

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function getGallery(): array
    {
        return $this->gallery;
    }

    public function setGallery(array $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    public function getIn_stock()
    {
        return $this->in_stock;
    }

    public function setIn_stock($in_stock)
    {
        $this->in_stock = $in_stock;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    public function getprod_prices(): Collection
    {
        return $this->prod_prices;
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prod_prices->contains($price)) {
            $this->prod_prices[] = $price;
            $price->setProduct($this);
        }
    }

    public function removePrice(Price $price): void
    {
        if ($this->prod_prices->contains($price)) {
            $this->prod_prices->removeElement($price);
            if ($price->getProduct() === $this) {
                $price->setProduct(null);
            }
        }
    }

    public function addAttribute(Attribute $attribute): void
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setProduct($this);
        }
    }

    public function removeAttribute(Attribute $attribute): void
    {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            if ($attribute->getProduct() === $this) {
                $attribute->setProduct(null);
            }
        }
    }

}
