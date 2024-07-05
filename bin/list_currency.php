<?php
use App\Entity\Currency;

require_once __DIR__ . '/../app/Config/bootstrap.php';

$currencyRepository =  $entityManager->getRepository(Currency::class);

// $currency =  $entityManager->find(Currency::class, 1);
// echo $currency->getLabel();

$currencys = $currencyRepository->findAll();

foreach ($currencys as $currency) {
    echo sprintf("-%s\n", $currency->getCurrencyId());
    echo sprintf("-%s\n", $currency->getLabel());
    $prices = $currency->getPrices();
    foreach ($prices as $price)
        echo sprintf("-%s\n", $price->getAmount());
}