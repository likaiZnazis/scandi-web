<?php
namespace App\Entity;

use App\Models\Attribute;
use Doctrine\ORM\Mapping as ORM;


//There can be many items for a single attribute
#[ORM\Entity]
#[ORM\Table(name: 'item')]
class Item
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $item_id;

    #[ORM\Column(type: 'string')]
    private string $displayValue;

    #[ORM\Column(type: 'string')]
    private string $value;

    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'attribute_id', referencedColumnName: 'attribute_id')]
    private Attribute $attribute;

    //Getters and setters
    public function getItem_id()
    {
        return $this->item_id;
    }

    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    public function setDisplayValue($displayValue)
    {
        $this->displayValue = $displayValue;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }
}