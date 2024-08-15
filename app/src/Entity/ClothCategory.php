<?php
namespace App\Entity;

use App\Models\Category;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ClothCategory extends Category
{
    public function __construct()
    {
        parent::__construct();
        $this->setCategoryName('clothes');
    }
}
