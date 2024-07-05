<?php

namespace App\Entity;

use App\Models\Product;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class TechProduct extends Product
{
    
}
