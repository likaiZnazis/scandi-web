<?php
namespace App\Entity;

use App\Models\Product;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'price')]
class Price
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $price_id;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $amount;

    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'currency_id')]
    private Currency $currency;

    #[ORM\ManyToOne(inversedBy: 'prod_prices')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'product_id')]
    private Product $product;

    // Getters and setters...

    public function getPriceId(): int|null
    {
        return $this->price_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
