<?php

use App\Entity\TechCategory;
use App\Entity\ClothCategory;
use App\Models\Product;

require_once __DIR__ . '/../app/Config/bootstrap.php';

$techCategory = new TechCategory();
$clothCategory = new ClothCategory();

$entityManager->persist($techCategory);
$entityManager->persist($clothCategory);

$entityManager->flush();