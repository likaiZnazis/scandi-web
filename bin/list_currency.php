<?php
use App\Entity\Currency;
use App\Models\Category;
require_once __DIR__ . '/../app/Config/bootstrap.php';

$currencyRepository =  $entityManager->getRepository(Category::class);

// $currency =  $entityManager->find(Currency::class, 1);
// echo $currency->getLabel();

$currencys = $currencyRepository->findAll();

foreach ($currencys as $currency) {
    echo sprintf("-%s\n", $currency->getCategoryId());
}