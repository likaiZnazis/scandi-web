<?php
use App\Entity\Currency;

require_once __DIR__ . '/../app/Config/bootstrap.php';

$id = $argv[1];
$currency = $entityManager->find(Currency::class, $id);

if($currency == null){
    echo "currency with index " + $id + " not found";
    exit(1);
}

echo sprintf("-%s\n", $currency->getLabel());