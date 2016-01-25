<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Evaneos\REST\CliKernel;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new CliKernel('dev', true);
$kernel->boot();
$app = $kernel->getApp();
$entityManager = $app['orm.em'];

return ConsoleRunner::createHelperSet($entityManager);
