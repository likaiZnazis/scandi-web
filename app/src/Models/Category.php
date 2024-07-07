<?php
namespace App\Models;

use App\Entity\TechCategory;
use App\Entity\ClothCategory;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['tech' => TechCategory::class, 'cloth' => ClothCategory::class])]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $category_id;

    #[ORM\Column(type: 'string')]
    protected $category_name;

    //There can be many products to a single category
    #[ORM\OneToMany(targetEntity: "Product", mappedBy: "category")]
    protected Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    // Getters and setters...

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getCategory(): string
    {
        return $this->category_name;
    }

    public function setCategory(string $category_name): void
    {
        $this->category_name = $category_name;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }
    }

    public function removeProduct(Product $product): void
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            if ($product->getCategory() === $this) {
                $product->getCategory(null);
            }
        }
    }
}
