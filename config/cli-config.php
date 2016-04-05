<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Evaneos\REST\Kernel\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Kernel(getenv('SILEX_SKT_ENV') ?: 'dev', false);
$kernel->boot();
$app = $kernel->getApp();

if(!$app->offsetExists('orm.em')){
    throw new Exception('No entity manager defined, please check "config/cli-config.php" file');
}

$entityManager = $app['orm.em'];

return ConsoleRunner::createHelperSet($entityManager);
