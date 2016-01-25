<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Evaneos\REST\Kernel\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Kernel('dev', true);
$kernel->boot();
$app = $kernel->getApp();
$entityManager = $app['orm.em'];

return ConsoleRunner::createHelperSet($entityManager);
