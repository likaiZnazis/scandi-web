<?php
namespace App\Entity;

//Need to set a consturctor for sub classes

use App\Models\Attribute;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Color extends Attribute
{
    public function __construct()
    {
        $name = 'Color';
        parent::__construct();
        $this->setId($name);
        $this->setName($name);
        $this->setType('swatch');
    }
}
