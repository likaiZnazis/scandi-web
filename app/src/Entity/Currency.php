<?php
namespace App\Entity;

use App\Entity\Price;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'currency')]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $currency_id;

    #[ORM\Column(type: 'string')]
    private string $label;

    #[ORM\Column(type: 'string')]
    private string $symbol;

    /** @var Collection<int, Price> */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'currency')]
    private Collection $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    // Getters and setters...

    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setCurrency($this);
        }
    }

    public function removePrice(Price $price): void
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            if ($price->getCurrency() === $this) {
                $price->setCurrency(null);
            }
        }
    }
}
