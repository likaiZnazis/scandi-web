<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $category_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $category_name;

    /**
     * @ORM\OneToMany(targetEntity="AbstractProduct", mappedBy="category")
     */
    protected $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    // Getters and setters...

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    public function setCategoryName(string $category_name): void
    {
        $this->category_name = $category_name;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(AbstractProduct $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }
    }

    public function removeProduct(AbstractProduct $product): void
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }
    }
}
