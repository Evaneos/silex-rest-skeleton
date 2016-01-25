<?php

use Evaneos\REST\HttpKernel;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$kernel = new HttpKernel(getenv('SILEX_SKT_ENV') ?: 'dev', false);
$response = $kernel->handle($request = Request::createFromGlobals());
$response->send();
$kernel->terminate($request, $response);
