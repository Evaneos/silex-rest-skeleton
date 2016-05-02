<?php

use Evaneos\REST\HttpKernel;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$env = getenv('SILEX_SKT_ENV') ?: 'dev';
$kernel = new HttpKernel($env, $env !== 'prod');
$response = $kernel->handle($request = Request::createFromGlobals());
$response->send();
$kernel->terminate($request, $response);
