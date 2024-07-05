<?php
use App\Entity\Currency;
use App\Entity\Price;

require_once __DIR__ . '/../app/Config/bootstrap.php';


//Inputing the label name
$label = $argv[1];
$symbol = $argv[2];
$amount = $argv[3];

$price = new Price();
$price->setAmount($amount);

$currency = new Currency();
$currency->setLabel($label);
$currency->setSymbol($symbol);

$currency->addPrice($price);
//need to also persist the price
//Notifing that a new entity will be inserted
$entityManager->persist($price);
$entityManager->persist($currency);

//Saving the changes
$entityManager->flush();