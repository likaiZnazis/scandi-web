<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

//Path to the metadata config. Since there are supper classes let's include tehir directory
$paths = ['/home/nazis/Desktop/scandi-web/fullstack-test-starter/app/src/Entity',
          '/home/nazis/Desktop/scandi-web/fullstack-test-starter/app/src/Models'];

$isDevMode = true;

//get parameters
$dbParams = require __DIR__ . '/../Database/db_params.php';

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);
