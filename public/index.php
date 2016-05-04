<?php

use Evaneos\REST\HttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/../vendor/autoload.php';

$env = getenv('SILEX_SKT_ENV') ?: 'dev';
$debug = getenv('SILEX_SKT_DEBUG') !== '0' && $env !== 'prod';
$kernel = new HttpKernel($env, $debug, (string) Uuid::uuid4());
$response = $kernel->handle($request = Request::createFromGlobals());
$response->send();
$kernel->terminate($request, $response);
