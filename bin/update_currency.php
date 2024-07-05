<?php
use App\Entity\Currency;

require_once __DIR__ . '/../app/Config/bootstrap.php';

$id = $argv[1];
$newLabel = $argv[2];

$currency = $entityManager->find(Currency::class, $id);

if ($currency === null) {
    echo "currency $id does not exist.\n";
    exit(1);
}

$currency->setLabel($newLabel);

$entityManager->flush();