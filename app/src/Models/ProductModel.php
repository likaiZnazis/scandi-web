<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"cloth_product" = "ClothProduct", "tech_product" = "TechProduct"})
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $product_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    //create getters and setters
    /**
     * @ORM\Column(type="boolean")
     */
    protected $in_stock;

    /**
     * @ORM\Column(type="json")
     */
    protected $gallery;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $brand;

    /** @var Collection<int, Price> */
    /**
     * @ORM\OneToMany(targetEntity="Price", mappedBy="product", cascade={"persist", "remove"})
     */
    protected Collection $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setProduct($this);
        }
    }

    public function removePrice(Price $price): void
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            if ($price->getProduct() === $this) {
                $price->setProduct(null);
            }
        }
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
}
