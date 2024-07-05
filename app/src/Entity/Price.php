<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;



#[ORM\Entity]
#[ORM\Table(name: 'price')]
class Price{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $price_id;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $amount;

    //There can only be one currency to a price
    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'currency_id')]
    private Currency $currency;

    //Getters and setters
    public function getPrice_id(): int|null
    {
        return $this->price_id;
    }

    public function getAmount():float
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency_id()
    {
        return $this->currency_id;
    }

    public function setCurrency_id($currency_id)
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        
        return $this;
    }
}