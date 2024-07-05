<?php
namespace App\Entity;

use App\Entity\Price;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'currency')]
class Currency{

    //Proper
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $currency_id;

    #[ORM\Column(type: 'string')]
    private string $label;

    #[ORM\Column(type: 'string')]
    private string $symbol;

    //There can be many prices to the same currency
    /** @var Collection<int, Price> */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: "currency")]
    private Collection $prices;

    //Constructor
    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    //Getters and setters
    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setCurrency($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->removeElement($price)) {
            // Set the owning side to null (unless already changed)
            if ($price->getCurrency() === $this) {
                $price->setCurrency(null);
            }
        }

        return $this;
    }
}