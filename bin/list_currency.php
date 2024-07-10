<?php
use App\Entity\Currency;
use App\Entity\Price;

require_once __DIR__ . '/../app/Config/bootstrap.php';

// $currencyRepository =  $entityManager->getRepository(Category::class);
$priceRepo = ($entityManager->getRepository(Price::class))->findAll();
// $priceCurrencys = $priceRepo;
// $currency =  $entityManager->find(Currency::class, 1);
// echo $currency->getLabel();

// $currencys = $currencyRepository->findAll();

foreach ($priceRepo as $currency) {
    echo sprintf("-%s\n", $currency->getCurrency()->getLabel());
}