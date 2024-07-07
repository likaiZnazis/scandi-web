<?php
namespace App\Entity;

use App\Models\Attribute;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Capacity extends Attribute
{
    public function __construct()
    {
        $name = 'Capacity';
        parent::__construct();
        $this->setId($name);
        $this->setName($name);
        $this->setType('text');
    }
}
